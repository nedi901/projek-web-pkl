<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/** Akun Saya: semua pengguna yang login bisa ubah nama, username & password sendiri.
 * Email, role, dan domain TIDAK bisa diubah di sini (satu email = satu akun; perubahan hanya lewat Super User). */
class ProfilController extends Controller
{
    public function edit()
    {
        return view('akun-saya.edit', ['akun' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $akun = $request->user();

        $request->merge([
            'username' => Str::lower(trim((string) $request->username)),
        ]);

        // email sengaja tidak ikut divalidasi/disimpan: kalaupun dikirim manual lewat request, diabaikan
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'min:3', 'max:30', 'alpha_dash:ascii', Rule::unique('users', 'username')->ignore($akun->id)],
        ], [
            'username.alpha_dash' => 'Username hanya boleh huruf, angka, strip (-), dan garis bawah (_).',
            'username.unique' => 'Username ini sudah dipakai.',
        ]);

        $akun->update($data);

        return redirect()->route('akun-saya.edit')->with('success', 'Profil berhasil diperbarui.');
    }

    public function password(Request $request)
    {
        $request->validateWithBag('password', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ], [
            'current_password.current_password' => 'Password saat ini salah.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak sama.',
            'password.different' => 'Password baru harus berbeda dari password saat ini.',
        ]);

        $request->user()->update(['password' => $request->password]);

        return redirect()->route('akun-saya.edit')->with('success', 'Password berhasil diganti.');
    }
}
