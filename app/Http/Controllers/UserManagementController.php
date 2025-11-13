<?php

namespace App\Http\Controllers;

use App\Models\User; // Menggunakan Model User bawaan Laravel
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    // READ (Index)
    public function index()
    {
        // Hanya menampilkan user selain yang sedang login (Admin tidak boleh hapus diri sendiri)
        $users = User::where('id', '!=', auth()->id())->latest()->paginate(10);
        return view('users.index', compact('users'));
    }

    // CREATE (Form)
    public function create()
    {
        return view('users.create');
    }

    // CREATE (Store)
    public function store(Request $request)
    {
        // Validasi Wajib
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(['admin', 'kasir'])], // Pastikan role hanya 'admin' atau 'kasir'
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);
        
        return redirect()->route('users.index')->with('status', 'User baru berhasil ditambahkan!');
    }

    // UPDATE (Form Edit)
    public function edit(User $user)
    {
        // User admin tidak boleh edit dirinya sendiri melalui form ini
        if (auth()->id() == $user->id) {
            return redirect()->route('users.index')->with('error', 'Gunakan halaman profil untuk mengedit akun Anda sendiri.');
        }
        return view('users.edit', compact('user'));
    }

    // UPDATE (Store)
    public function update(Request $request, User $user)
    {
        // Validasi untuk update
        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in(['admin', 'kasir'])],
        ];

        // Jika password diisi, validasi password juga
        if ($request->filled('password')) {
            $rules['password'] = 'nullable|string|min:8|confirmed';
        }
        
        $validated = $request->validate($rules);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];

        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }
        
        $user->save();

        return redirect()->route('users.index')->with('status', 'User berhasil diperbarui!');
    }

    // DELETE (Destroy)
    public function destroy(User $user)
    {
        // User admin tidak boleh menghapus akunnya sendiri
        if (auth()->id() == $user->id) {
            return redirect()->route('users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }
        
        $user->delete(); 
        return redirect()->route('users.index')->with('status', 'User berhasil dihapus!');
    }
}