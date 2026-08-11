<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ToastHelper;
use App\Http\Controllers\Controller;
use App\Models\Beauticians;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminBeauticianController extends Controller
{
    /**
     * Tampilkan daftar staf beautician dengan pencarian & statistik booking.
     */
    public function index(Request $request)
    {
        $query = Beauticians::withCount('bookings');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('bio', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('is_active', $request->status === 'active');
        }

        $beauticians = $query->orderBy('name', 'asc')
            ->paginate(8)
            ->withQueryString();

        return view('admin.beauticians.index', compact('beauticians'));
    }

    /**
     * Tampilkan form pendaftaran beautician baru.
     */
    public function create()
    {
        return view('admin.beauticians.create');
    }

    /**
     * Simpan data beautician baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'phone'     => ['nullable', 'string', 'max:30'],
            'email'     => ['nullable', 'email', 'max:255', 'unique:beauticians,email'],
            'bio'       => ['required', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'photo'     => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $photoName = null;
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $photoName = time() . '_' . Str::slug($validated['name']) . '.' . $file->getClientOriginalExtension();
            $file->storeAs(Beauticians::PHOTO_DIRECTORY, $photoName, 'public');
        }

        Beauticians::create([
            'name'           => $validated['name'],
            'phone'          => $validated['phone'] ?? null,
            'email'          => $validated['email'] ?? null,
            'bio'            => $validated['bio'],
            'photo'          => $photoName,
            'is_active'      => $request->boolean('is_active', true),
            'total_bookings' => 0,
        ]);

        ToastHelper::success("Staf beautician '{$validated['name']}' berhasil ditambahkan! 🌸");

        return redirect()->route('admin.beauticians.index');
    }

    /**
     * Detail profil & kinerja staf beautician.
     */
    public function show(Beauticians $beautician)
    {
        $beautician->loadCount('bookings');
        $recentBookings = $beautician->bookings()
            ->with(['user', 'treatments'])
            ->orderBy('booking_date', 'desc')
            ->take(10)
            ->get();

        $avgRating = (float) $beautician->reviews()->avg('rating');

        return view('admin.beauticians.show', compact('beautician', 'recentBookings', 'avgRating'));
    }

    /**
     * Tampilkan form edit beautician.
     */
    public function edit(Beauticians $beautician)
    {
        return view('admin.beauticians.edit', compact('beautician'));
    }

    /**
     * Update profil beautician.
     */
    public function update(Request $request, Beauticians $beautician)
    {
        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'phone'     => ['nullable', 'string', 'max:30'],
            'email'     => ['nullable', 'email', 'max:255', 'unique:beauticians,email,' . $beautician->id],
            'bio'       => ['required', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'photo'     => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        if ($request->hasFile('photo')) {
            if ($beautician->photo) {
                Storage::disk('public')->delete(Beauticians::PHOTO_DIRECTORY . '/' . $beautician->photo);
            }

            $file = $request->file('photo');
            $photoName = time() . '_' . Str::slug($validated['name']) . '.' . $file->getClientOriginalExtension();
            $file->storeAs(Beauticians::PHOTO_DIRECTORY, $photoName, 'public');
            $beautician->photo = $photoName;
        }

        $beautician->update([
            'name'      => $validated['name'],
            'phone'     => $validated['phone'] ?? null,
            'email'     => $validated['email'] ?? null,
            'bio'       => $validated['bio'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        ToastHelper::success("Profil beautician '{$beautician->name}' berhasil diperbarui! ✨");

        return redirect()->route('admin.beauticians.index');
    }

    /**
     * Hapus data beautician.
     */
    public function destroy(Beauticians $beautician)
    {
        $name = $beautician->name;

        if ($beautician->photo) {
            Storage::disk('public')->delete(Beauticians::PHOTO_DIRECTORY . '/' . $beautician->photo);
        }

        $beautician->delete();

        ToastHelper::success("Staf beautician '{$name}' berhasil dihapus.");

        return redirect()->route('admin.beauticians.index');
    }

    /**
     * Toggle status aktif / penugasan beautician.
     */
    public function toggleActive(Beauticians $beautician)
    {
        $beautician->is_active = ! $beautician->is_active;
        $beautician->save();

        $statusText = $beautician->is_active ? 'diaktifkan kembali' : 'dinonaktifkan';
        ToastHelper::info("Status penugasan '{$beautician->name}' berhasil {$statusText}.");

        return redirect()->back();
    }
}
