<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class AdminTestimonialController extends Controller
{
    /**
     * Display a listing of the testimonials.
     */
    public function index(Request $request): View
    {
        $this->ensureTableExists();

        $query = Testimonial::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('role_or_company', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $testimonials = $query->orderBy('sort_order', 'asc')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total'      => Testimonial::count(),
            'featured'   => Testimonial::where('is_featured', true)->count(),
            'avg_rating' => round(Testimonial::avg('rating') ?? 5.0, 1),
            'avg_rate'   => round(Testimonial::avg('rating') ?? 5.0, 1),
        ];

        return view('admin.testimonials.index', compact('testimonials', 'stats'));
    }

    /**
     * Ensure the testimonials table exists with initial seed data.
     */
    protected function ensureTableExists(): void
    {
        if (!Schema::hasTable('testimonials')) {
            Schema::create('testimonials', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100);
                $table->string('role_or_company', 150)->nullable();
                $table->string('avatar')->nullable();
                $table->unsignedTinyInteger('rating')->default(5);
                $table->text('content');
                $table->boolean('is_featured')->default(true);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });

            DB::table('testimonials')->insert([
                [
                    'name' => 'Budi Pratama',
                    'role_or_company' => 'CTO di KirimCepat Logistik',
                    'avatar' => null,
                    'rating' => 5,
                    'content' => 'Integrasi API JalurGaib luar biasa cepat. Pengiriman OTP dan resi otomatis kami naik tingkat keberhasilannya hingga 99.8% tanpa delay.',
                    'is_featured' => 1,
                    'sort_order' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Sarah Anggraini',
                    'role_or_company' => 'Founder & Owner TokoModis.id',
                    'avatar' => null,
                    'rating' => 5,
                    'content' => 'Dulu tim CS kami kewalahan balas ribuan broadcast promo manual. Sejak pakai JalurGaib, sistem webhook otomatis handle notifikasi order dalam hitungan detik!',
                    'is_featured' => 1,
                    'sort_order' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Dian Kusuma',
                    'role_or_company' => 'Lead DevOps di Finvest Tech',
                    'avatar' => null,
                    'rating' => 5,
                    'content' => 'Multi-session engine Baileys-nya sangat stabil. Latensi webhook ke endpoint internal kami rata-rata hanya 120ms. Dashboard-nya clean dan modern.',
                    'is_featured' => 1,
                    'sort_order' => 3,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Hendro Santoso',
                    'role_or_company' => 'Operational Manager di WarungKopiku',
                    'avatar' => null,
                    'rating' => 5,
                    'content' => 'Setup QR cuma butuh 10 detik. Fitur auto-reconnect membuat kami tenang tidak takut koneksi putus tiba-tiba di jam sibuk operasional restoran.',
                    'is_featured' => 1,
                    'sort_order' => 4,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }

    /**
     * Store a newly created testimonial.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:100',
            'role_or_company' => 'nullable|string|max:150',
            'rating'          => 'required|integer|min:1|max:5',
            'content'         => 'required|string|max:1000',
            'is_featured'     => 'nullable|boolean',
            'sort_order'      => 'nullable|integer|min:0',
        ]);

        Testimonial::create([
            'name'            => $validated['name'],
            'role_or_company' => $validated['role_or_company'] ?? null,
            'rating'          => $validated['rating'],
            'content'         => $validated['content'],
            'is_featured'     => (bool) ($request->has('is_featured')),
            'sort_order'      => $validated['sort_order'] ?? 0,
        ]);

        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Testimoni baru berhasil ditambahkan dan akan tampil di landing page!');
    }

    /**
     * Update the specified testimonial.
     */
    public function update(Request $request, Testimonial $testimonial): RedirectResponse
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:100',
            'role_or_company' => 'nullable|string|max:150',
            'rating'          => 'required|integer|min:1|max:5',
            'content'         => 'required|string|max:1000',
            'is_featured'     => 'nullable|boolean',
            'sort_order'      => 'nullable|integer|min:0',
        ]);

        $testimonial->update([
            'name'            => $validated['name'],
            'role_or_company' => $validated['role_or_company'] ?? null,
            'rating'          => $validated['rating'],
            'content'         => $validated['content'],
            'is_featured'     => (bool) ($request->has('is_featured')),
            'sort_order'      => $validated['sort_order'] ?? $testimonial->sort_order,
        ]);

        return redirect()->route('admin.testimonials.index')
            ->with('success', "Testimoni '{$testimonial->name}' berhasil diperbarui.");
    }

    /**
     * Remove the specified testimonial.
     */
    public function destroy(Testimonial $testimonial): RedirectResponse
    {
        $name = $testimonial->name;
        $testimonial->delete();

        return redirect()->route('admin.testimonials.index')
            ->with('success', "Testimoni dari '{$name}' telah dihapus.");
    }

    /**
     * Toggle the featured/landing page visibility status.
     */
    public function toggle(Testimonial $testimonial): RedirectResponse
    {
        $testimonial->update([
            'is_featured' => !$testimonial->is_featured,
        ]);

        $status = $testimonial->is_featured ? 'ditampilkan' : 'disembunyikan';
        return redirect()->route('admin.testimonials.index')
            ->with('success', "Status testimoni '{$testimonial->name}' kini {$status} di landing page.");
    }
}
