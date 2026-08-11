<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ToastHelper;
use App\Http\Controllers\Controller;
use App\Models\Categories;
use App\Models\Treatments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminTreatmentController extends Controller
{
    /**
     * Tampilkan daftar treatment admin dengan optimalisasi query & filter kategori.
     */
    public function index(Request $request)
    {
        $query = Treatments::query()
            ->select([
                'id', 'category_id', 'name', 'slug', 'description',
                'price', 'duration_minutes', 'images', 'badge',
                'is_active', 'rating', 'rating_count', 'sort_order', 'created_at',
            ])
            ->with(['category:id,name,slug']);

        // Filter berdasarkan kategori
        if ($request->filled('category_id') && $request->category_id !== 'all') {
            $query->where('category_id', $request->category_id);
        }

        // Filter berdasarkan kata kunci pencarian
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $treatments = $query->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc')
            ->paginate(9)
            ->withQueryString();

        $categories = Categories::where('is_active', true)->orderBy('sort_order', 'asc')->get();

        return view('admin.treatments.index', compact('treatments', 'categories'));
    }

    /**
     * Tampilkan form tambah treatment baru.
     */
    public function create()
    {
        $categories = Categories::where('is_active', true)->orderBy('sort_order', 'asc')->get();

        return view('admin.treatments.create', compact('categories'));
    }

    /**
     * Simpan treatment baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'category_id'      => ['required', 'exists:categories,id'],
            'price'            => ['required', 'numeric', 'min:0'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'badge'            => ['required', 'in:none,best_seller,new,promo'],
            'description'      => ['required', 'string'],
            'benefits'         => ['nullable', 'string'],
            'is_active'        => ['nullable', 'boolean'],
            'images'            => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        // Auto-generate slug unik
        $baseSlug = Str::slug($validated['name']);
        $slug = $baseSlug;
        $counter = 1;
        while (Treatments::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $imageName = null;
        if ($request->hasFile('images')) {
            $file = $request->file('imagess');
            $imageName = time() . '_' . Str::slug($validated['name']) . '.' . $file->getClientOriginalExtension();
            $file->storeAs(Treatments::IMAGE_DIRECTORY, $imageName, 'public');
        }

        Treatments::create([
            'name'             => $validated['name'],
            'slug'             => $slug,
            'category_id'      => $validated['category_id'],
            'price'            => $validated['price'],
            'duration_minutes' => $validated['duration_minutes'],
            'badge'            => $validated['badge'],
            'description'      => $validated['description'],
            'benefits'         => $validated['benefits'] ?? null,
            'is_active'        => $request->boolean('is_active', true),
            'images'            => $imageName,
            'rating'           => 0.0,
            'rating_count'     => 0,
            'sort_order'       => 0,
        ]);

        ToastHelper::success("Treatment '{$validated['name']}' berhasil ditambahkan! 🌸");

        return redirect()->route('admin.treatments.index');
    }

    /**
     * Tampilkan form edit treatment.
     */
    public function edit(Treatments $treatment)
    {
        $categories = Categories::where('is_active', true)->orderBy('sort_order', 'asc')->get();

        return view('admin.treatments.edit', compact('treatment', 'categories'));
    }

    /**
     * Update data treatment di database.
     */
    public function update(Request $request, Treatments $treatment)
    {
        $validated = $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'category_id'      => ['required', 'exists:categories,id'],
            'price'            => ['required', 'numeric', 'min:0'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'badge'            => ['required', 'in:none,best_seller,new,promo'],
            'description'      => ['required', 'string'],
            'benefits'         => ['nullable', 'string'],
            'is_active'        => ['nullable', 'boolean'],
            'images'            => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        if ($validated['name'] !== $treatment->name) {
            $baseSlug = Str::slug($validated['name']);
            $slug = $baseSlug;
            $counter = 1;
            while (Treatments::where('slug', $slug)->where('id', '!=', $treatment->id)->exists()) {
                $slug = "{$baseSlug}-{$counter}";
                $counter++;
            }
            $treatment->slug = $slug;
        }

        if ($request->hasFile('images')) {
            // Hapus gambar lama bila ada
            if ($treatment->images) {
                Storage::disk('public')->delete(Treatments::IMAGE_DIRECTORY . '/' . $treatment->images);
            }

            $file = $request->file('images');
            $imageName = time() . '_' . Str::slug($validated['name']) . '.' . $file->getClientOriginalExtension();
            $file->storeAs(Treatments::IMAGE_DIRECTORY, $imageName, 'public');
            $treatment->images = $imageName;
        }

        $treatment->update([
            'name'             => $validated['name'],
            'category_id'      => $validated['category_id'],
            'price'            => $validated['price'],
            'duration_minutes' => $validated['duration_minutes'],
            'badge'            => $validated['badge'],
            'description'      => $validated['description'],
            'benefits'         => $validated['benefits'] ?? null,
            'is_active'        => $request->boolean('is_active', true),
        ]);

        ToastHelper::success("Treatment '{$treatment->name}' berhasil diperbarui! ✨");

        return redirect()->route('admin.treatments.index');
    }

    /**
     * Hapus treatment dari database.
     */
    public function destroy(Treatments $treatment)
    {
        $name = $treatment->name;

        if ($treatment->images) {
            Storage::disk('public')->delete(Treatments::IMAGE_DIRECTORY . '/' . $treatment->images      );
        }

        $treatment->delete();

        ToastHelper::success("Treatment '{$name}' berhasil dihapus.");

        return redirect()->route('admin.treatments.index');
    }

    /**
     * Toggle status aktif / nonaktif treatment secara instan.
     */
    public function toggleActive(Treatments $treatment)
    {
        $treatment->is_active = ! $treatment->is_active;
        $treatment->save();

        $statusText = $treatment->is_active ? 'diaktifkan' : 'dinonaktifkan';
        ToastHelper::info("Status treatment '{$treatment->name}' berhasil {$statusText}.");

        return redirect()->back();
    }
}
