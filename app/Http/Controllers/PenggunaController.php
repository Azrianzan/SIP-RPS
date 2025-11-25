<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class PenggunaController extends Controller
{
    public function index()
    {
        // Ambil semua user beserta info rolenya
        $users = User::with('role')->get();
        // Ambil role untuk dropdown tambah user
        $roles = Role::all();
        
        return view('pengguna.index', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role_id' => 'required|exists:roles,id'
        ]);

        User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
        ]);

        return redirect()->route('pengguna.index')->with('success', 'Pengguna berhasil ditambahkan');
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            // Validasi email unik tapi abaikan ID pengguna ini sendiri
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role_id' => 'required|exists:roles,id',
            'password' => 'nullable|min:6' // Password boleh kosong jika tidak ingin diganti
        ]);

        $data = [
            'nama' => $request->nama,
            'email' => $request->email,
            'role_id' => $request->role_id,
        ];

        // Hanya update password jika input password diisi
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('pengguna.index')->with('success', 'Data pengguna berhasil diperbarui');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('pengguna.index')->with('success', 'Pengguna berhasil dihapus');
    }
}