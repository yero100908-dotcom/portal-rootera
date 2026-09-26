<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuditLoggerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();
        $roles = ['owner', 'admin', 'mentor', 'it', 'sosmed', 'cs', 'karyawan', 'siswa'];

        return view('users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = ['owner', 'admin', 'mentor', 'it', 'sosmed', 'cs', 'karyawan', 'siswa'];

        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:owner,admin,mentor,it,sosmed,cs,karyawan,siswa',
            'phone' => 'nullable|string|max:50',
            'face_descriptor' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
            'face_descriptor' => $request->face_descriptor,
            'is_active' => true,
        ]);

        $user->assignRole($request->role);

        AuditLoggerService::log('Tambah User', "Membuat akun user baru {$user->name} ({$user->role})");

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $roles = ['owner', 'admin', 'mentor', 'it', 'sosmed', 'cs', 'karyawan', 'siswa'];

        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|in:owner,admin,mentor,it,sosmed,cs,karyawan,siswa',
            'phone' => 'nullable|string|max:50',
            'is_active' => 'required|boolean',
            'face_descriptor' => 'nullable|string',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'phone' => $request->phone,
            'is_active' => $request->is_active,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->filled('face_descriptor')) {
            $data['face_descriptor'] = $request->face_descriptor;
        }

        $user->update($data);
        $user->syncRoles([$request->role]);

        AuditLoggerService::log('Update User', "Memperbarui data user {$user->name}");

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function registerFaceView(User $user)
    {
        return view('users.register_face', compact('user'));
    }

    public function saveFace(Request $request, User $user)
    {
        $request->validate([
            'face_descriptor' => 'required|string',
        ]);

        $user->update([
            'face_descriptor' => $request->face_descriptor,
        ]);

        AuditLoggerService::log('Pendaftaran Wajah', "Mendaftarkan template wajah untuk user {$user->name}");

        return response()->json([
            'success' => true,
            'message' => "Data wajah user {$user->name} berhasil disimpan!",
        ]);
    }

    public function destroy(User $user)
    {
        AuditLoggerService::log('Hapus User', "Menghapus akun user {$user->name}");
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}
