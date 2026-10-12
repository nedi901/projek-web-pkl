<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Kelola Akun - hanya Super User (middleware super.user).
 * Akun TIDAK bisa dibuat sendiri oleh pengguna: semua akun dibuat, diatur, dan dikelola Super User.
 * Pengaman: tidak bisa mengubah role/status/menghapus akun sendiri, dan tidak bisa
 * menurunkan / menonaktifkan / menghapus Super User aktif TERAKHIR (supaya tidak ada yang terkunci).
 */
class AkunController extends Controller
{
    public function index(Request $request)
    {
        $akuns = User::query()
            ->when($request->search, function ($q, $cari) {
                $q->where(function ($w) use ($cari) {
                    $w->where('name', 'like', "%{$cari}%")
                        ->orWhere('username', 'like', "%{$cari}%")
                        ->orWhere('email', 'like', "%{$cari}%");
                });
            })
            ->when($request->role, fn ($q, $role) => $q->where('role', $role))
            ->when(in_array($request->status, ['aktif', 'nonaktif'], true), fn ($q) => $q->where('is_active', $request->status === 'aktif'))
            ->orderByRaw("CASE role WHEN 'super_user' THEN 0 WHEN 'admin' THEN 1 ELSE 2 END")
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $ringkasan = [
            'total' => User::count(),
            'super_user' => User::where('role', 'super_user')->count(),
            'admin' => User::where('role', 'admin')->count(),
            'user' => User::where('role', 'user')->count(),
            'nonaktif' => User::where('is_active', false)->count(),
        ];

        return view('admin.akun.index', compact('akuns', 'ringkasan'));
    }

    public function create()
    {
        return view('admin.akun.create');
    }

    public function store(Request $request)
    {
        $this->normalisasi($request);

        $data = $request->validate($this->rules() + [
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], $this->pesan());

        $data['domain_akses'] = $data['role'] === 'admin' ? $data['domain_akses'] : null;
        $data['is_active'] = $request->boolean('is_active');

        $akun = User::create($data);

        return redirect()->route('superuser.akun.index')
            ->with('success', "Akun \"{$akun->username}\" berhasil dibuat.");
    }

    public function edit(User $akun)
    {
        return view('admin.akun.edit', compact('akun'));
    }

    public function update(Request $request, User $akun)
    {
        // akun sendiri: role, domain, dan status dikunci supaya tidak mengunci diri sendiri
        if ($akun->id === Auth::id()) {
            $request->merge([
                'role' => $akun->role,
                'domain_akses' => $akun->domain_akses,
                'is_active' => '1',
            ]);
        }

        $this->normalisasi($request);

        $data = $request->validate($this->rules($akun), $this->pesan());
        $data['domain_akses'] = $data['role'] === 'admin' ? $data['domain_akses'] : null;
        $data['is_active'] = $request->boolean('is_active');

        if ($this->superUserAktifTerakhir($akun) && ($data['role'] !== 'super_user' || ! $data['is_active'])) {
            return back()->withInput()
                ->with('error', 'Tidak bisa: ini satu-satunya Super User aktif. Buat/aktifkan Super User lain dulu.');
        }

        $akun->update($data);

        return redirect()->route('superuser.akun.index')
            ->with('success', "Akun \"{$akun->username}\" berhasil diperbarui.");
    }

    public function resetPassword(Request $request, User $akun)
    {
        $request->validateWithBag('reset', [
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], $this->pesan());

        $akun->update(['password' => $request->password]);

        return redirect()->route('superuser.akun.edit', $akun)
            ->with('success', "Password akun \"{$akun->username}\" berhasil direset.");
    }

    public function toggleStatus(User $akun)
    {
        if ($akun->id === Auth::id()) {
            return back()->with('error', 'Kamu tidak bisa menonaktifkan akunmu sendiri.');
        }

        if ($akun->is_active && $this->superUserAktifTerakhir($akun)) {
            return back()->with('error', 'Tidak bisa menonaktifkan satu-satunya Super User aktif.');
        }

        $akun->update(['is_active' => ! $akun->is_active]);

        return back()->with('success', "Akun \"{$akun->username}\" sekarang " . ($akun->is_active ? 'aktif' : 'nonaktif') . '.');
    }

    public function destroy(User $akun)
    {
        if ($akun->id === Auth::id()) {
            return back()->with('error', 'Kamu tidak bisa menghapus akunmu sendiri.');
        }

        if ($this->superUserAktifTerakhir($akun)) {
            return back()->with('error', 'Tidak bisa menghapus satu-satunya Super User aktif.');
        }

        $nama = $akun->username;
        $akun->delete();

        return redirect()->route('superuser.akun.index')
            ->with('success', "Akun \"{$nama}\" dihapus permanen. Riwayat di Histori tetap menyimpan namanya.");
    }

    private function rules(?User $akun = null): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'min:3', 'max:30', 'alpha_dash:ascii', Rule::unique('users', 'username')->ignore($akun?->id)],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($akun?->id)],
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
            'domain_akses' => ['nullable', 'required_if:role,admin', Rule::in(array_keys(User::DOMAINS))],
        ];
    }

    private function pesan(): array
    {
        return [
            'username.alpha_dash' => 'Username hanya boleh huruf, angka, strip (-), dan garis bawah (_).',
            'username.unique' => 'Username ini sudah dipakai.',
            'email.unique' => 'Email ini sudah dipakai.',
            'domain_akses.required_if' => 'Admin wajib dipilihkan domain yang dikelola.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
        ];
    }

    private function normalisasi(Request $request): void
    {
        $request->merge([
            'username' => Str::lower(trim((string) $request->username)),
            'email' => Str::lower(trim((string) $request->email)),
        ]);
    }

    private function superUserAktifTerakhir(User $akun): bool
    {
        return $akun->role === 'super_user'
            && $akun->is_active
            && User::where('role', 'super_user')->where('is_active', true)->where('id', '!=', $akun->id)->doesntExist();
    }
}
