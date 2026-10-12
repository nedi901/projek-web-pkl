<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Pendaftaran akun dari halaman login.
 * Akun baru TIDAK langsung aktif: role asli tetap "user" (read-only), status "menunggu",
 * dan role/domain yang diminta baru berlaku setelah Super User menyetujui di Kelola Akun.
 * Dengan begitu tidak ada yang bisa menjadikan dirinya Admin/Super User cuma lewat formulir.
 */
class DaftarController extends Controller
{
    public function create()
    {
        return view('auth.daftar');
    }

    public function store(Request $request)
    {
        $request->merge([
            'username' => Str::lower(trim((string) $request->username)),
            'email' => Str::lower(trim((string) $request->email)),
        ]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'min:3', 'max:30', 'alpha_dash:ascii', Rule::unique('users', 'username')],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role_diminta' => ['required', Rule::in(array_keys(User::ROLES))],
            'domain_diminta' => ['nullable', 'required_if:role_diminta,admin', Rule::in(array_keys(User::DOMAINS))],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ], [
            'username.alpha_dash' => 'Username hanya boleh huruf, angka, strip (-), dan garis bawah (_).',
            'username.unique' => 'Username ini sudah dipakai.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'domain_diminta.required_if' => 'Pilih domain yang akan kamu kelola.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
        ]);

        User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => 'user',
            'domain_akses' => null,
            'is_active' => false,
            'status_akun' => 'menunggu',
            'role_diminta' => $data['role_diminta'],
            'domain_diminta' => $data['role_diminta'] === 'admin' ? $data['domain_diminta'] : null,
            'keterangan' => $data['keterangan'] ?? null,
        ]);

        return redirect()->route('login')
            ->with('success', 'Pendaftaran terkirim. Akunmu bisa dipakai login setelah disetujui Super User.');
    }
}
