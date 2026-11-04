<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminPackageController extends Controller
{
    /**
     * Display a listing of the packages.
     */
    public function index(): View
    {
        if (\Illuminate\Support\Facades\Schema::hasTable('packages') && !\Illuminate\Support\Facades\Schema::hasColumn('packages', 'reset_cycle')) {
            try {
                \Illuminate\Support\Facades\Schema::table('packages', function ($table) {
                    $table->string('reset_cycle', 16)->default('daily')->after('daily_message_limit');
                });
            } catch (\Throwable $e) {
                // Ignore if already added
            }
        }

        $packages = Package::withCount('users')
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        return view('admin.packages.index', compact('packages'));
    }

    /**
     * Store a newly created package in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'slug'                => 'nullable|string|max:100|unique:packages,slug',
            'description'         => 'nullable|string|max:1000',
            'price'               => 'required|integer|min:0',
            'max_devices'         => 'required|integer|min:1|max:100',
            'daily_message_limit' => 'required|integer|min:1|max:1000000',
            'reset_cycle'         => 'nullable|in:daily,monthly',
            'benefits'            => 'nullable|string', // raw multiline text
            'status'              => 'required|in:active,coming_soon,inactive',
            'badge'               => 'nullable|string|max:50',
            'sort_order'          => 'nullable|integer|min:0',
        ]);

        $slug = !empty($validated['slug']) 
            ? Str::slug($validated['slug']) 
            : Str::slug($validated['name']);

        // Check unique slug fallback
        $originalSlug = $slug;
        $counter = 1;
        while (Package::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        // Ensure benefits capacity and cycle match package specifications
        $benefitsArray = $this->syncOrBuildBenefits(
            $validated['benefits'] ?? '',
            (int) $validated['max_devices'],
            (int) $validated['daily_message_limit'],
            $validated['reset_cycle'] ?? 'daily'
        );

        Package::create([
            'name'                => $validated['name'],
            'slug'                => $slug,
            'description'         => $validated['description'] ?? null,
            'price'               => $validated['price'],
            'max_devices'         => $validated['max_devices'],
            'daily_message_limit' => $validated['daily_message_limit'],
            'reset_cycle'         => $validated['reset_cycle'] ?? 'daily',
            'benefits'            => $benefitsArray,
            'status'              => $validated['status'],
            'badge'               => $validated['badge'] ?? null,
            'sort_order'          => $validated['sort_order'] ?? 0,
        ]);

        return redirect()->route('admin.packages.index')
            ->with('success', "Paket '{$validated['name']}' berhasil ditambahkan.");
    }

    /**
     * Update the specified package in storage.
     */
    public function update(Request $request, Package $package): RedirectResponse
    {
        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'slug'                => 'required|string|max:100|unique:packages,slug,' . $package->id,
            'description'         => 'nullable|string|max:1000',
            'price'               => 'required|integer|min:0',
            'max_devices'         => 'required|integer|min:1|max:100',
            'daily_message_limit' => 'required|integer|min:1|max:1000000',
            'reset_cycle'         => 'nullable|in:daily,monthly',
            'benefits'            => 'nullable|string',
            'status'              => 'required|in:active,coming_soon,inactive',
            'badge'               => 'nullable|string|max:50',
            'sort_order'          => 'nullable|integer|min:0',
        ]);

        // Safeguard: Do not allow the sole free package to have price > 0
        if (($package->slug === 'free' || $package->price === 0) && (int) $validated['price'] > 0) {
            $otherFree = Package::where('id', '!=', $package->id)->where('price', 0)->where('status', 'active')->exists();
            if (!$otherFree) {
                return back()->withInput()->with('error', 'Paket gratis dasar sistem tidak boleh diberi harga lebih dari Rp 0 kecuali sudah ada paket gratis lain yang berstatus aktif.');
            }
        }

        // Ensure benefits capacity and cycle match package specifications
        $benefitsArray = $this->syncOrBuildBenefits(
            $validated['benefits'] ?? '',
            (int) $validated['max_devices'],
            (int) $validated['daily_message_limit'],
            $validated['reset_cycle'] ?? 'daily'
        );

        $package->update([
            'name'                => $validated['name'],
            'slug'                => Str::slug($validated['slug']),
            'description'         => $validated['description'] ?? null,
            'price'               => $validated['price'],
            'max_devices'         => $validated['max_devices'],
            'daily_message_limit' => $validated['daily_message_limit'],
            'reset_cycle'         => $validated['reset_cycle'] ?? 'daily',
            'benefits'            => $benefitsArray,
            'status'              => $validated['status'],
            'badge'               => $validated['badge'] ?? null,
            'sort_order'          => $validated['sort_order'] ?? 0,
        ]);

        return redirect()->route('admin.packages.index')
            ->with('success', "Paket '{$package->name}' berhasil diperbarui.");
    }

    /**
     * Remove the specified package from storage.
     */
    public function destroy(Package $package): RedirectResponse
    {
        // Check if package is default free
        if ($package->slug === 'free' || $package->price === 0) {
            $otherFree = Package::where('id', '!=', $package->id)->where('price', 0)->exists();
            if (!$otherFree) {
                return back()->with('error', 'Paket default Free tidak dapat dihapus karena sistem membutuhkan minimal 1 paket dasar.');
            }
        }

        $freePackage = Package::getDefaultPackage();
        // Reassign affected users to default free package
        User::where('package_id', $package->id)->update(['package_id' => $freePackage->id]);

        $packageName = $package->name;
        $package->delete();

        return redirect()->route('admin.packages.index')
            ->with('success', "Paket '{$packageName}' berhasil dihapus. Pengguna terkait telah dialihkan ke paket dasar.");
    }

    /**
     * Parse benefits from textarea and ensure capacity lines match package specifications.
     */
    private function syncOrBuildBenefits(?string $raw, int $maxDevices, int $messageLimit, string $resetCycle): array
    {
        $cycleLabel = $resetCycle === 'monthly' ? 'Bulan' : 'Hari';
        $resetTime  = $resetCycle === 'monthly' ? 'Awal Bulan' : '24 Jam';
        $numFmt     = number_format($messageLimit, 0, ',', '.');

        $deviceLine = "{$maxDevices} Device WhatsApp terhubung";
        $limitLine  = "{$numFmt} Pesan WhatsApp / {$cycleLabel}";
        $resetLine  = "Reset Kuota Setiap {$resetTime}";

        $rawLines = $this->parseBenefits($raw);
        if (empty($rawLines)) {
            return [
                $deviceLine,
                $limitLine,
                $resetLine,
                'REST API Key Terisolasi & Webhook',
                'Anti-Ban Human Delay & Antrean Cerdas',
            ];
        }

        $deviceReplaced = false;
        $limitReplaced  = false;
        $resetReplaced  = false;
        $updated = [];

        foreach ($rawLines as $line) {
            $lower = strtolower($line);
            if (!$deviceReplaced && (str_contains($lower, 'device') || str_contains($lower, 'nomor') || str_contains($lower, 'perangkat'))) {
                $updated[] = $deviceLine;
                $deviceReplaced = true;
                continue;
            }
            if (!$limitReplaced && (str_contains($lower, 'pesan') || str_contains($lower, 'msg') || str_contains($lower, 'kuota'))) {
                $updated[] = $limitLine;
                $limitReplaced = true;
                continue;
            }
            if (!$resetReplaced && str_contains($lower, 'reset')) {
                $updated[] = $resetLine;
                $resetReplaced = true;
                continue;
            }
            $updated[] = $line;
        }

        if (!$resetReplaced) {
            $updated[] = $resetLine;
        }
        if (!$limitReplaced) {
            array_unshift($updated, $limitLine);
        }
        if (!$deviceReplaced) {
            array_unshift($updated, $deviceLine);
        }

        return array_values(array_unique($updated));
    }

    /**
     * Helper to convert multiline string of benefits to array.
     */
    private function parseBenefits(?string $raw): array
    {
        if (empty(trim($raw ?? ''))) {
            return [];
        }

        $lines = preg_split('/\r\n|\r|\n/', $raw);
        $clean = [];
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed !== '') {
                // remove leading bullet points if user typed them
                $trimmed = ltrim($trimmed, '-*• \t');
                if ($trimmed !== '') {
                    $clean[] = $trimmed;
                }
            }
        }
        return array_values($clean);
    }
}
