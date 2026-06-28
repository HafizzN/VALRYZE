<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->orderBy('name')->paginate(15);
        return view('settings.users.index', compact('users'));
    }
    public function create()
    {
        $roles = Role::all();
        return view('settings.users.create', compact('roles'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'role'  => 'required|exists:roles,name',
        ]);
        $user = User::create(['name' => $request->name, 'email' => $request->email, 'password' => Hash::make($request->password)]);
        $user->assignRole($request->role);
        return redirect()->route('settings.users.index')->with('success', 'User berhasil dibuat.');
    }
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('settings.users.edit', compact('user', 'roles'));
    }
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role'  => 'required|exists:roles,name',
        ]);
        $user->update($request->only(['name', 'email']));
        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }
        $user->syncRoles([$request->role]);
        return redirect()->route('settings.users.index')->with('success', 'User berhasil diperbarui.');
    }
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) return back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        $user->delete();
        return redirect()->route('settings.users.index')->with('success', 'User berhasil dihapus.');
    }
}
