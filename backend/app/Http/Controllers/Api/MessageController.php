<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Device;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Jobs\SendWhatsAppMessage;
use App\Services\WhatsAppService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    public function __construct(private readonly WhatsAppService $waService) {}

    private function getUser(Request $request): User
    {
        $user = $request->user();
        if (!$user) {
            abort(401, 'Unauthenticated');
        }
        return $user;
    }

    /**
     * Send a single text message (queued).
     */
    public function sendText(Request $request): JsonResponse
    {
        // 1. Dukung alias nama field upload: 'file', 'photo', 'picture', 'attachment', 'media' -> dipetakan ke 'image'
        $uploadedFile = $request->file('image')
            ?? $request->file('file')
            ?? $request->file('photo')
            ?? $request->file('picture')
            ?? $request->file('attachment')
            ?? $request->file('media');

        if ($uploadedFile && !$request->hasFile('image')) {
            $request->files->set('image', $uploadedFile);
        }

        // 2. Deteksi dini error upload dari PHP engine (misal upload_max_filesize di php.ini)
        $rawFile = $request->file('image');
        if ($rawFile && !$rawFile->isValid()) {
            $uploadError = $rawFile->getError();
            $maxIni = ini_get('upload_max_filesize') ?: '2M';
            $postIni = ini_get('post_max_size') ?: '8M';
            $errorMsg = match ($uploadError) {
                UPLOAD_ERR_INI_SIZE   => "Ukuran file yang diunggah melebihi batas 'upload_max_filesize' di php.ini Apache ({$maxIni}). Harap perbesar konfigurasi upload_max_filesize di php.ini atau kompres gambar Anda.",
                UPLOAD_ERR_FORM_SIZE  => "Ukuran file melebihi batas form MAX_FILE_SIZE.",
                UPLOAD_ERR_PARTIAL    => "File hanya terunggah sebagian (koneksi terputus). Silakan coba lagi.",
                UPLOAD_ERR_NO_FILE    => "Tidak ada file yang terunggah.",
                UPLOAD_ERR_NO_TMP_DIR => "Server PHP kehilangan folder sementara (upload_tmp_dir tidak ditemukan/tidak writable).",
                UPLOAD_ERR_CANT_WRITE => "Gagal menulis file ke hard disk server (permission denied pada folder temporary).",
                default               => "Terjadi kesalahan upload pada PHP server (Kode Error: {$uploadError}).",
            };
            return response()->json([
                'success' => false,
                'message' => $errorMsg,
            ], 422);
        }

        // 3. Normalisasi jika 'image' dikirim sebagai string (URL, Base64, atau string teks)
        if (!$request->hasFile('image') && $request->has('image')) {
            $rawImageVal = $request->input('image');
            if (is_string($rawImageVal)) {
                $trimmed = trim($rawImageVal);
                
                // 3a. String URL (http://... atau https://...)
                if (filter_var($trimmed, FILTER_VALIDATE_URL)) {
                    if (!$request->filled('media_url')) {
                        $request->merge(['media_url' => $trimmed]);
                    }
                    $request->request->remove('image');
                }
                // 3b. String Base64 Image (data:image/...;base64,...)
                elseif (preg_match('/^data:image\/(jpeg|jpg|png|webp);base64,(.+)$/is', $trimmed, $b64Matches)) {
                    $ext = strtolower($b64Matches[1] === 'jpeg' ? 'jpg' : $b64Matches[1]);
                    $decodedData = base64_decode(preg_replace('/\s+/', '', $b64Matches[2]));
                    if ($decodedData !== false && strlen($decodedData) > 0) {
                        $filename = 'wa_' . time() . '_' . \Illuminate\Support\Str::random(24) . '.' . $ext;
                        $destinationPath = public_path('uploads');
                        if (!file_exists($destinationPath)) {
                            mkdir($destinationPath, 0755, true);
                        }
                        file_put_contents($destinationPath . '/' . $filename, $decodedData);
                        $request->merge(['media_url' => asset('uploads/' . $filename)]);
                        $request->request->remove('image');
                    }
                }
                // 3c. Placeholder kosong dari form-data
                elseif ($trimmed === '' || $trimmed === 'null' || $trimmed === 'undefined') {
                    $request->request->remove('image');
                }
                // 3d. Pengguna mengirim teks nama file (misal "gambar.png") tapi bukan binary file
                else {
                    $contentType = $request->header('Content-Type') ?? '';
                    $diagHelp = "Server menerima parameter 'image' sebagai string teks (\"{$trimmed}\"), bukan binary file upload.";
                    if (str_contains(strtolower($contentType), 'application/json')) {
                        $diagHelp .= " Request terdeteksi menggunakan 'Content-Type: application/json'. Untuk mengunggah file fisik, gunakan tab 'form-data' di Postman (bukan raw JSON).";
                    } else {
                        $diagHelp .= " Pada Postman tab 'form-data', pastikan tipe field 'image' diubah dari [Text] menjadi [File] lalu klik tombol 'Select Files'. Jika di tab 'Headers' ada Content-Type yang dicentang manual, harap UNCHECK/hapus agar boundary terkirim otomatis.";
                    }
                    return response()->json([
                        'success' => false,
                        'message' => $diagHelp,
                        'received_value' => $trimmed,
                        'content_type_header' => $contentType,
                    ], 422);
                }
            }
        }

        $validated = $request->validate([
            'device_id'  => 'required|string|exists:devices,device_id',
            'to'         => 'required|string|max:20',
            'message'    => 'required|string|max:4096',
            'media_url'  => [
                'nullable', 'string', 'url', 'max:2048',
                function ($attribute, $value, $fail) use ($request) {
                    $parsed = parse_url($value);
                    if (!$parsed || empty($parsed['host']) || !in_array(strtolower($parsed['scheme'] ?? ''), ['http', 'https'])) {
                        $fail('URL media tidak valid (hanya protokol HTTP/HTTPS yang diizinkan).');
                        return;
                    }
                    $host = strtolower($parsed['host']);
                    $path = strtolower($parsed['path'] ?? '');

                    // 1. Mutlak blokir Cloud Metadata Service (AWS/GCP/Azure/Kubernetes metadata)
                    $cloudMetadata = ['169.254.169.254', 'metadata.google.internal', 'instance-data'];
                    if (in_array($host, $cloudMetadata, true) || str_ends_with($host, '.internal')) {
                        $fail('Akses ke cloud metadata service diblokir demi keamanan.');
                        return;
                    }

                    // 2. Jika URL mengarah ke file statis /uploads/ pada server aplikasi ini sendiri (baik di localhost, IP lokal, atau domain resmi)
                    $appHost = strtolower(parse_url(config('app.url'), PHP_URL_HOST) ?? '');
                    $currentHost = strtolower($request->getHost());
                    $isOwnServer = in_array($host, [$appHost, $currentHost, 'localhost', '127.0.0.1', '::1'], true);

                    if (str_starts_with($path, '/uploads/')) {
                        // File upload lokal yang sah -> Izinkan
                        return;
                    }

                    // 3. Jika di environment production, blokir akses ke internal loopback selain /uploads/
                    if (app()->environment('production')) {
                        $blockedHosts = ['localhost', '127.0.0.1', '::1', '0.0.0.0'];
                        if (in_array($host, $blockedHosts, true) && !$isOwnServer) {
                            $fail('Alamat URL media tidak diizinkan di production (host privat internal).');
                            return;
                        }
                        $resolvedIp = gethostbyname($host);
                        if ($resolvedIp !== $host) {
                            if (filter_var($resolvedIp, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false && !$isOwnServer) {
                                $fail('Host media tidak boleh mengarah ke IP privat atau internal network.');
                            }
                        }
                    }
                }
            ],
            // Strict Mimes whitelist: Only JPG, PNG, WebP with bail to stop on first failure
            'image'      => 'bail|nullable|file|mimes:jpeg,jpg,png,webp|max:10240',
            'min_delay'  => 'nullable|integer|min:500|max:10000',
            'max_delay'  => 'nullable|integer|min:1000|max:30000',
        ], [
            'image.file'    => 'Field "image" harus berupa file gambar fisik (JPG, JPEG, PNG, WebP) yang di-upload via multipart/form-data. Jika Anda ingin mengirimkan link URL gambar dari internet, gunakan field "media_url".',
            'image.mimes'   => 'Format file gambar hanya boleh berupa JPG, JPEG, PNG, atau WebP.',
            'image.max'     => 'Ukuran file gambar tidak boleh melebihi 10 MB (10.240 KB).',
            'media_url.url' => 'Format media_url harus berupa URL HTTP/HTTPS yang valid.',
        ]);

        $user = $this->getUser($request);

        // Strictly verify that the device belongs to the authenticated user
        $device = Device::where('device_id', $validated['device_id'])
            ->when(!$user->isAdmin(), fn($q) => $q->where('user_id', $user->id))
            ->first();

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Perangkat WhatsApp tidak ditemukan pada akun Anda atau milik pengguna lain.',
            ], 403);
        }

        // Sync latest status from Go service
        $this->waService->getDeviceStatus($device);

        if (!$device->isConnected()) {
            return response()->json([
                'success' => false,
                'message' => 'Perangkat WhatsApp (' . $device->name . ') belum terhubung/offline.',
            ], 422);
        }

        // 🔒 Enforce Message Limit based on user's active package (Daily or Monthly)
        if (!$user->canSendMessages(1)) {
            $pkg = $user->getPackage();
            $periodLabel = $user->isMonthlyLimit() ? 'bulanan' : 'harian';
            $resetTime = $user->isMonthlyLimit() ? 'awal bulan berikutnya' : 'pukul 00:00 WIB setiap hari';
            return response()->json([
                'success'         => false,
                'message'         => "Kuota pesan {$periodLabel} untuk paket {$pkg->name} (Maks {$user->getMessageLimit()} pesan/{$pkg->cycle_label}) telah habis. Kuota di-reset pada {$resetTime}.",
                'error_code'      => $user->isMonthlyLimit() ? 'MONTHLY_MESSAGE_LIMIT_REACHED' : 'DAILY_MESSAGE_LIMIT_REACHED',
                'limit'           => $user->getMessageLimit(),
                'used'            => $user->getCurrentCycleSentMessagesCount(),
                'remaining'       => $user->getRemainingMessages(),
                'cycle'           => $user->getLimitCycle(),
            ], 429);
        }

        // Normalize phone number to 62 format
        $toPhone = preg_replace('/[^0-9]/', '', $validated['to']);
        if (str_starts_with($toPhone, '0')) {
            $toPhone = '62' . substr($toPhone, 1);
        } elseif (str_starts_with($toPhone, '8')) {
            $toPhone = '62' . $toPhone;
        }

        // Handle uploaded image file securely
        $mediaUrl = $validated['media_url'] ?? null;
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $file = $request->file('image');

            // CR-03 Fix: Strictly use server-detected extension, never trust client-supplied extension
            $safeExt = strtolower($file->guessExtension() ?: 'png');
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
            if (!in_array($safeExt, $allowedExtensions, true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format file gambar tidak diizinkan. Hanya file JPG, PNG, atau WebP yang diterima.',
                ], 422);
            }

            $filename = 'wa_' . time() . '_' . \Illuminate\Support\Str::random(24) . '.' . $safeExt;
            $destinationPath = public_path('uploads');
            
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            
            $file->move($destinationPath, $filename);
            $mediaUrl = asset('uploads/' . $filename);
            
            \Illuminate\Support\Facades\Log::info('[WA Message] Image saved securely to public/uploads', [
                'filename' => $filename,
                'url'      => $mediaUrl,
            ]);
        }

        $message = Message::create([
            'device_id' => $device->id,
            'to'        => $toPhone,
            'message'   => $validated['message'],
            'type'      => $mediaUrl ? 'image' : 'text',
            'media_url' => $mediaUrl,
            'status'    => 'pending',
            'min_delay' => $validated['min_delay'] ?? 1000,
            'max_delay' => $validated['max_delay'] ?? 4000,
        ]);

        // Dispatch to queue
        SendWhatsAppMessage::dispatch($message, $device);

        return response()->json([
            'success' => true,
            'message' => 'Pesan berhasil dimasukkan ke antrean delivery Queue.',
            'data'    => [
                'message_id'      => $message->id,
                'status'          => $message->status,
                'remaining_quota' => $user->getRemainingDailyMessages(),
            ],
        ], 202);
    }

    /**
     * Send bulk messages (batch with queue).
     */
    public function sendBulk(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'device_id'    => 'required|string|exists:devices,device_id',
            'recipients'   => 'required|array|min:1|max:100',
            'recipients.*' => 'required|string|max:20',
            'message'      => 'required|string|max:4096',
            'min_delay'    => 'nullable|integer|min:2000|max:30000',
            'max_delay'    => 'nullable|integer|min:5000|max:60000',
        ]);

        $user = $this->getUser($request);

        // Strictly verify that the device belongs to the authenticated user
        $device = Device::where('device_id', $validated['device_id'])
            ->when(!$user->isAdmin(), fn($q) => $q->where('user_id', $user->id))
            ->first();

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Perangkat WhatsApp tidak ditemukan pada akun Anda atau milik pengguna lain.',
            ], 403);
        }

        // Sync latest status from Go service
        $this->waService->getDeviceStatus($device);

        if (!$device->isConnected()) {
            return response()->json([
                'success' => false,
                'message' => 'Perangkat WhatsApp (' . $device->name . ') belum terhubung/offline.',
            ], 422);
        }

        $recipientCount = count($validated['recipients']);

        // 🔒 Enforce Message Limit for Bulk sending (Daily or Monthly)
        if (!$user->canSendMessages($recipientCount)) {
            $pkg = $user->getPackage();
            $remaining = $user->getRemainingMessages();
            $periodLabel = $user->isMonthlyLimit() ? 'bulanan' : 'harian';
            return response()->json([
                'success'         => false,
                'message'         => "Pengiriman batch ({$recipientCount} pesan) melebihi sisa kuota {$periodLabel} paket {$pkg->name} ({$remaining} tersisa dari {$user->getMessageLimit()}/{$pkg->cycle_label}).",
                'error_code'      => $user->isMonthlyLimit() ? 'MONTHLY_MESSAGE_LIMIT_EXCEEDED' : 'DAILY_MESSAGE_LIMIT_EXCEEDED',
                'limit'           => $user->getMessageLimit(),
                'used'            => $user->getCurrentCycleSentMessagesCount(),
                'remaining'       => $remaining,
                'cycle'           => $user->getLimitCycle(),
                'requested_count' => $recipientCount,
            ], 429);
        }

        $queued = [];
        $delay  = 0;

        foreach ($validated['recipients'] as $recipient) {
            $message = Message::create([
                'device_id' => $device->id,
                'to'        => $recipient,
                'message'   => $validated['message'],
                'type'      => 'text',
                'status'    => 'pending',
                'min_delay' => $validated['min_delay'] ?? 2000,
                'max_delay' => $validated['max_delay'] ?? 6000,
            ]);

            SendWhatsAppMessage::dispatch($message, $device)->delay(now()->addSeconds($delay));
            $delay += 5;

            $queued[] = ['message_id' => $message->id, 'to' => $recipient];
        }

        return response()->json([
            'success' => true,
            'message' => count($queued) . ' pesan berhasil dimasukkan ke antrean delivery Queue.',
            'data'    => $queued,
        ], 202);
    }

    /**
     * Get message history for the authenticated user ONLY.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $this->getUser($request);
        $perPage = (int) $request->query('per_page', 10);
        $perPage = min(max($perPage, 5), 100);
        
        $messages = Message::whereHas('device', fn($q) => $q->where('user_id', $user->id))
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => $messages,
        ]);
    }
}
