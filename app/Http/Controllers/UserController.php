<?php

namespace App\Http\Controllers;

use App\Models\User;      
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['roles', 'restaurants'])
            ->where('email', '!=', 'superadmin@tanly.id')
            ->latest()
            ->paginate(10);

        return view('users.index', compact('users'));
    }

    public function edit(User $user)
    {
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

        // 1. Update Role langsung via Spatie
        $user->syncRoles($request->role);

        // 2. Update Restoran langsung via Pivot Table
        $user->restaurants()->sync($request->input('restaurants', []));

        return redirect()->route('users.index')->with('success', 'Hak akses user berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if (Auth::id() == $user->id) {
            return back()->with('error', 'Anda tidak bisa menghapus akses Anda sendiri.');
        }

        // Bersihkan relasi agar tidak ada data yatim piatu di database
        $user->syncRoles([]);
        $user->restaurants()->detach(); 
        
        // Hapus user dari database lokal Checklist
        $user->delete(); 

        return back()->with('success', 'Akses user berhasil dicabut dari aplikasi Checklist.');
    }
}
