<?php

namespace App\Http\Controllers;

use App\Models\User;      // Model Portal (Bridge)
use App\Models\LocalUser; // Model Lokal (Tabel users di checklist)
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        // Ambil User Portal yang SUDAH punya akses ke aplikasi ini (ada di tabel users lokal)
        // Kita ambil email dari LocalUser dulu
        $localEmails = LocalUser::pluck('email');

        // Ambil object User Portal lengkap dengan relasi lokalnya
        $users = User::with(['localProfile.roles', 'localProfile.restaurants'])
            ->whereIn('email', $localEmails)
            ->where('email', '!=', 'superadmin@tanly.id')
            ->latest()
            ->paginate(10);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $restaurants = Restaurant::all();
        $roles = Role::all();
        return view('users.create', compact('restaurants', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'role' => 'required|exists:roles,name',
            'restaurants' => 'nullable|array',
            'restaurants.*' => 'exists:restaurants,id',
        ]);

        // 1. CEK PORTAL: Apakah user ada di Portal Utama?
        $portalUser = DB::connection('mysql_portal')->table('users')
            ->where('email', $request->email)->first();

        if (!$portalUser) {
            return back()->withInput()->with('error', 'User belum terdaftar di Portal Utama. Silakan buat akun di Portal terlebih dahulu.');
        }

        // 2. CEK LOKAL: Apakah user sudah punya akses di sini?
        $existsLocal = LocalUser::where('email', $request->email)->exists();
        if ($existsLocal) {
            return back()->withInput()->with('error', 'User ini sudah memiliki akses. Silakan edit saja.');
        }

        // 3. SYNC ID: Buat LocalUser dengan ID yang SAMA dengan Portal
        $localUser = LocalUser::create([
            'id'       => $portalUser->id,       // <--- KUNCI STRICT SYNC
            'name'     => $portalUser->name,     // Copy nama untuk cache
            'email'    => $portalUser->email,    // Copy email
            'password' => $portalUser->password, // Copy hash password
        ]);

        // 4. ASSIGN ROLE (Spatie)
        $localUser->assignRole($request->role);

        // 5. ASSIGN RESTAURANTS (Pivot)
        if ($request->has('restaurants')) {
            $localUser->restaurants()->sync($request->restaurants);
        }

        return redirect()->route('users.index')->with('success', 'Hak akses berhasil diberikan ke user.');
    }

    public function edit(User $user)
    {
        // $user adalah object User Portal.
        // Kita perlu data restoran & role dari profile lokalnya.

        $restaurants = Restaurant::all();
        $roles = Role::all();

        return view('users.edit', compact('user', 'restaurants', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|exists:roles,name',
            'restaurants' => 'nullable|array',
            'restaurants.*' => 'exists:restaurants,id',
        ]);

        // Kita TIDAK mengubah Email/Password di sini karena itu wewenang Portal.
        // Kita hanya mengubah Hak Akses (Role & Restoran) di database Lokal.

        // 1. Ambil Local User
        $localUser = LocalUser::find($user->id); // Karena ID sudah sinkron, bisa pakai find($user->id)

        if (!$localUser) {
            // Fallback jika ID belum sinkron (kasus lama), cari by email
            $localUser = LocalUser::where('email', $user->email)->first();
        }

        if (!$localUser) {
            return back()->with('error', 'Data lokal user tidak ditemukan.');
        }

        // 2. Update Role
        $localUser->syncRoles($request->role);

        // 3. Update Restaurants
        $localUser->restaurants()->sync($request->input('restaurants', []));

        return redirect()->route('users.index')->with('success', 'Hak akses user diperbarui.');
    }

    public function destroy(User $user)
    {
        if (Auth::id() == $user->id) {
            return back()->with('error', 'Anda tidak bisa menghapus akses sendiri.');
        }

        // HAPUS DARI LOKAL SAJA (REVOKE ACCESS)
        // Hapus role dulu agar bersih (opsional jika cascade sudah nyala)
        $localUser = $user->localProfile;

        if ($localUser) {
            // $localUser->roles()->detach();
            $localUser->removeRole();
            $localUser->restaurants()->detach(); // Hapus relasi restoran
            $localUser->delete(); // Hapus user dari tabel lokal
        }

        return back()->with('success', 'Akses user dicabut dari aplikasi Checklist.');
    }
}
