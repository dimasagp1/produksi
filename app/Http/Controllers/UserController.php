<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Super Admin selalu di atas, sisanya urut nama
        $users = $query->orderByRaw("FIELD(role, 'super_admin') DESC")
            ->orderBy('name')
            ->paginate(10)
            ->appends($request->all());

        return view('users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:super_admin,admin,gm,manager,spv,leader,operator',
            'status' => 'required|in:active,inactive',
            'password' => 'required|min:6',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'status' => $request->status,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|in:super_admin,admin,gm,manager,spv,leader,operator',
            'status' => 'required|in:active,inactive',
            'password' => 'nullable|min:6',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'status' => $request->status,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Data pengguna diperbarui.');
    }

    public function destroy($id)
    {
        if (auth()->id() == $id) return back()->with('error', 'Tidak dapat menghapus akun sendiri!');

        $user = User::findOrFail($id);

        // Proteksi: Admin biasa tidak boleh hapus Super Admin
        if ($user->role === 'super_admin' && auth()->user()->role !== 'super_admin') {
            return back()->with('error', 'Hanya sesama Super Admin yang bisa menghapus Super Admin!');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus.');
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    public function toggleStatus($id)
    {
        if (auth()->id() == $id) return response()->json(['success' => false, 'message' => 'Tidak bisa menonaktifkan diri sendiri!'], 403);

        $user = User::findOrFail($id);
        $user->status = ($user->status === 'active') ? 'inactive' : 'active';
        $user->save();

        return response()->json(['success' => true, 'new_status' => $user->status]);
    }

    public function toggleSystemLock()
    {
        // Hanya Super Admin yang boleh eksekusi
        if (auth()->user()->role !== 'super_admin') {
            return back()->with('error', 'Anda tidak memiliki akses!');
        }

        if (Storage::exists('system_locked')) {
            Storage::delete('system_locked');
            $msg = 'Sistem telah DIBUKA kembali.';
        } else {
            Storage::put('system_locked', 'true');
            $msg = 'Sistem BERHASIL DIKUNCI. User lain tidak dapat mengakses.';
        }

        return back()->with('success', $msg);
    }
}
