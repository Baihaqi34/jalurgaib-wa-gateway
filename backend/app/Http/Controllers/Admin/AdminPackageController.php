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

        // Convert newline separated benefits to array
        $benefitsArray = $this->parseBenefits($validated['benefits'] ?? '');

        Package::create([
            'name'                => $validated['name'],
            'slug'                => $slug,
            'description'         => $validated['description'] ?? null,
            'price'               => $validated['price'],
            'max_devices'         => $validated['max_devices'],
            'daily_message_limit' => $validated['daily_message_limit'],
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
            'benefits'            => 'nullable|string',
            'status'              => 'required|in:active,coming_soon,inactive',
            'badge'               => 'nullable|string|max:50',
            'sort_order'          => 'nullable|integer|min:0',
        ]);

        $benefitsArray = $this->parseBenefits($validated['benefits'] ?? '');

        $package->update([
            'name'                => $validated['name'],
            'slug'                => Str::slug($validated['slug']),
            'description'         => $validated['description'] ?? null,
            'price'               => $validated['price'],
            'max_devices'         => $validated['max_devices'],
            'daily_message_limit' => $validated['daily_message_limit'],
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
