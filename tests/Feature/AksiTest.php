<?php

/*
|--------------------------------------------------------------------------
| Tes aksi: MENGIRIM form (SmokeTest cuma membuka halaman).
|--------------------------------------------------------------------------
| Jalankan:  php artisan test --filter=Aksi
| Memakai database SQLite di memori yang diisi seeder, jadi database.sqlite asli tidak tersentuh.
|
| Yang dicakup : login/logout, Kelola Akun, Akun Saya, hapus -> Histori -> pulihkan, CRUD Batubara,
|                Kelola Referensi (semua jenis), Kelola Tabel Buku, dan "Edit lalu Simpan" ke-22 tabel buku.
| Belum dicakup: form tambah/ubah domain selain Batubara, import Excel, tampilan di browser.
*/

use App\Models\IdInstansi;
use App\Models\Kabupaten;
use App\Models\KelasKalori;
use App\Models\NeracaBatubara;
use App\Models\NeracaGambut;
use App\Models\NeracaMineralBukanLogam;
use App\Models\NeracaMineralLogam;
use App\Models\NeracaPanasBumi;
use App\Models\Provinsi;
use App\Models\RiwayatData;
use App\Models\StatDikBb;
use App\Models\TabelReferensi;
use App\Models\User;
use App\Support\TabelParser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
});

/* ------------------------------------------------------------------ */
/* Pembantu                                                            */
/* ------------------------------------------------------------------ */

function aksiAkun(string $email): User
{
    return User::where('email', $email)->firstOrFail();
}

function aksiSuper(): User
{
    return aksiAkun('admin@test.com');
}

/** slug domain => [slug, model neraca, email admin domain, awalan nama route] */
function aksiDomainList(): array
{
    return [
        'batubara' => ['batubara', NeracaBatubara::class, 'superbatubara@test.com', 'admin.batubara'],
        'mineral logam' => ['mineral_logam', NeracaMineralLogam::class, 'superminerallogam@test.com', 'admin.mineral-logam'],
        'mineral bukan logam' => ['mineral_bukan_logam', NeracaMineralBukanLogam::class, 'superminerallogambukan@test.com', 'admin.mineral-bukan-logam'],
        'panas bumi' => ['panas_bumi', NeracaPanasBumi::class, 'superpanasbumi@test.com', 'admin.panas-bumi'],
        'gambut' => ['gambut', NeracaGambut::class, 'supergambut@test.com', 'admin.gambut'],
    ];
}

function aksiPayloadAkun(array $ubah = []): array
{
    return array_merge([
        'name' => 'Admin Baru',
        'username' => 'admin_baru',
        'email' => 'baru@test.com',
        'role' => 'admin',
        'domain_akses' => 'gambut',
        'password' => 'rahasia123',
        'password_confirmation' => 'rahasia123',
        'is_active' => '1',
    ], $ubah);
}

function aksiPayloadBatubara(array $ubah = []): array
{
    return array_merge([
        'idbb' => (int) NeracaBatubara::max('idbb') + 1,
        'nama_objek' => 'Objek Uji Batubara',
        'tahun_data' => 2025,
        'tahun_neraca' => 2025,
        'provinsi_id' => Provinsi::query()->value('id'),
        'kabupaten_id' => Kabupaten::query()->value('id'),
        'kelas_kalori_id' => KelasKalori::query()->value('id'),
        'stat_dik_bb_id' => StatDikBb::query()->value('id'),
        'id_instansi_id' => IdInstansi::query()->value('id'),
        'tereka' => '10.5',
        'tertunjuk' => '20',
        'terukur' => '5',
        'terkira' => '4',
        'terbukti' => '6',
        'remark' => 'data uji',
    ], $ubah);
}

/**
 * Satu contoh data untuk tiap jenis Kelola Referensi: [tabel, kolom unik, payload form].
 * $akhiran dipakai supaya nama bisa dibuat "berbeda" untuk tes ubah.
 */
function aksiRef(string $jenis, string $akhiran = ''): array
{
    return match ($jenis) {
        'provinsi' => ['provinsis', 'nama_provinsi', ['nama_provinsi' => "Provinsi Uji{$akhiran}", 'pulau' => 'Jawa']],
        'kabupaten' => ['kabupatens', 'nama_kabupaten', ['provinsi_id' => Provinsi::query()->value('id'), 'nama_kabupaten' => "Kabupaten Uji{$akhiran}"]],
        'kelompok-logam' => ['kelompok_komoditas_logams', 'nama_kelompok', ['nama_kelompok' => "Kelompok Uji{$akhiran}"]],
        'komoditas-logam' => ['komoditas_logams', 'nama_komoditas', ['kelompok_komoditas_logam_id' => DB::table('kelompok_komoditas_logams')->value('id'), 'nama_komoditas' => "Komoditas Uji{$akhiran}"]],
        'kelompok-bukan-logam' => ['kelompok_komoditas_bukan_logams', 'nama_kelompok', ['nama_kelompok' => "Kelompok Uji{$akhiran}"]],
        'komoditas-bukan-logam' => ['komoditas_bukan_logams', 'nama_komoditas', ['kelompok_komoditas_bukan_logam_id' => DB::table('kelompok_komoditas_bukan_logams')->value('id'), 'nama_komoditas' => "Komoditas Uji{$akhiran}"]],
        'kelas-kalori' => ['kelas_kaloris', 'label', ['label' => "Kelas Uji{$akhiran}", 'rentang' => '1000 - 2000']],
        'status-penyelidikan' => ['stat_dik_bbs', 'label', ['label' => "Status Uji{$akhiran}"]],
        'instansi' => ['id_instansis', 'label', ['label' => "Instansi Uji{$akhiran}"]],
        'status-penyelidikan-pb' => ['stat_dik_pbs', 'label', ['label' => "Status PB Uji{$akhiran}"]],
        'klasifikasi-temperatur' => ['klasifikasi_temperaturs', 'label', ['label' => "Temperatur Uji{$akhiran}"]],
    };
}

function aksiTeksTabel(): string
{
    return implode("\n", [
        implode("\t", ['Provinsi', 'Sumber Daya', 'Cadangan']),
        implode("\t", ['Kalimantan Timur', '1.234,56', '10,5']),
        implode("\t", ['Sumatera Selatan', '-', '2,25']),
        implode("\t", ['TOTAL', '1.234,56', '12,75']),
    ]);
}

function aksiPayloadTabel(array $ubah = []): array
{
    return array_merge([
        'domain' => 'batubara',
        'judul' => 'Tabel Uji Rekap',
        'tahun_data' => 2025,
        'sumber' => 'Buku Uji',
        'urutan' => 5,
        'catatan' => 'catatan uji',
        'data' => aksiTeksTabel(),
    ], $ubah);
}

/** Cara sel tabel buku TAMPIL di halaman Grafik (sama dengan komponen tabel-referensi-domain). */
function aksiTampil($v, array $kol): string
{
    $tipe = $kol['t'] ?? 'text';
    if ($v === null || $v === '') {
        return '-';
    }
    if ($tipe === 'text') {
        return (string) $v;
    }
    if ($tipe === 'num' && (float) $v == 0.0) {
        return '-';
    }

    return number_format((float) $v, $kol['d'] ?? 0, ',', '.');
}

/* ------------------------------------------------------------------ */
/* LOGIN & LOGOUT                                                      */
/* ------------------------------------------------------------------ */

it('login berhasil dan diarahkan sesuai role', function (string $login, ?string $rute) {
    $this->post(route('login.store'), ['login' => $login, 'password' => 'password'])
        ->assertRedirect($rute ? route($rute) : url('/dashboard'));

    $this->assertAuthenticated();
})->with([
    'super user (email)' => ['admin@test.com', 'superuser.dashboard'],
    'admin batubara (email)' => ['superbatubara@test.com', 'admin.batubara.grafik.index'],
    'admin gambut (email)' => ['supergambut@test.com', 'admin.gambut.grafik.index'],
    'admin panas bumi (username)' => ['admin_panasbumi', 'admin.panas-bumi.grafik.index'],
    'user biasa (email)' => ['user@test.com', null],
]);

it('login tidak peduli huruf besar/kecil', function (string $login) {
    $this->post(route('login.store'), ['login' => $login, 'password' => 'password']);

    $this->assertAuthenticated();
})->with(['EMAIL HURUF BESAR' => 'SUPERBATUBARA@TEST.COM', 'USERNAME HURUF BESAR' => 'Admin_Batubara']);

it('login gagal kalau password salah atau akun tidak ada', function (string $login, string $password) {
    $this->post(route('login.store'), ['login' => $login, 'password' => $password])
        ->assertSessionHasErrors('login');

    $this->assertGuest();
})->with([
    'password salah' => ['admin@test.com', 'salah-total'],
    'email tidak terdaftar' => ['hantu@test.com', 'password'],
    'username tidak terdaftar' => ['hantu', 'password'],
]);

it('login gagal kalau form kosong', function () {
    $this->post(route('login.store'), [])->assertSessionHasErrors(['login', 'password']);

    $this->assertGuest();
});

it('akun yang dinonaktifkan tidak bisa login', function () {
    aksiAkun('user@test.com')->update(['is_active' => false]);

    $this->post(route('login.store'), ['login' => 'user@test.com', 'password' => 'password'])
        ->assertSessionHasErrors('login');

    $this->assertGuest();
});

it('akun yang dinonaktifkan saat sedang login langsung dikeluarkan', function () {
    $user = aksiAkun('user@test.com');
    $this->actingAs($user);
    $user->update(['is_active' => false]);

    $this->get('/dashboard')->assertRedirect(route('login'));

    $this->assertGuest();
});

it('logout mengakhiri sesi', function () {
    $this->actingAs(aksiAkun('user@test.com'))
        ->post(route('logout'))
        ->assertRedirect(url('/login'));

    $this->assertGuest();
});

/* ------------------------------------------------------------------ */
/* KELOLA AKUN (Super User)                                            */
/* ------------------------------------------------------------------ */

it('Super User bisa membuat akun admin domain (username & email otomatis huruf kecil)', function () {
    $this->actingAs(aksiSuper())
        ->post(route('superuser.akun.store'), aksiPayloadAkun(['username' => 'Admin_Baru', 'email' => 'Baru@Test.com']))
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('superuser.akun.index'));

    $akun = User::where('email', 'baru@test.com')->first();

    expect($akun)->not->toBeNull();
    expect($akun->username)->toBe('admin_baru');
    expect($akun->role)->toBe('admin');
    expect($akun->domain_akses)->toBe('gambut');
    expect($akun->is_active)->toBeTrue();
    expect(Hash::check('rahasia123', $akun->password))->toBeTrue();
});

it('akun role user dan super user tidak menyimpan domain_akses', function (string $role) {
    $this->actingAs(aksiSuper())
        ->post(route('superuser.akun.store'), aksiPayloadAkun(['role' => $role, 'domain_akses' => 'batubara']))
        ->assertSessionHasNoErrors();

    expect(User::where('email', 'baru@test.com')->value('domain_akses'))->toBeNull();
})->with(['user', 'super_user']);

it('pembuatan akun ditolak kalau isian tidak valid', function (array $ubah, string $kolomError) {
    $awal = User::count();

    $this->actingAs(aksiSuper())
        ->post(route('superuser.akun.store'), aksiPayloadAkun($ubah))
        ->assertSessionHasErrors($kolomError);

    expect(User::count())->toBe($awal);
})->with([
    'admin tanpa domain' => [['domain_akses' => null], 'domain_akses'],
    'domain tidak dikenal' => [['domain_akses' => 'sawit'], 'domain_akses'],
    'role tidak dikenal' => [['role' => 'dewa'], 'role'],
    'email sudah dipakai' => [['email' => 'user@test.com'], 'email'],
    'email sudah dipakai (beda huruf besar)' => [['email' => 'USER@TEST.COM'], 'email'],
    'email bukan email' => [['email' => 'bukan-email'], 'email'],
    'username sudah dipakai' => [['username' => 'superuser'], 'username'],
    'username ada spasi' => [['username' => 'a b c'], 'username'],
    'username terlalu pendek' => [['username' => 'ab'], 'username'],
    'password terlalu pendek' => [['password' => '1234567', 'password_confirmation' => '1234567'], 'password'],
    'konfirmasi password beda' => [['password_confirmation' => 'lain12345'], 'password'],
    'nama kosong' => [['name' => ''], 'name'],
]);

it('hanya Super User yang boleh membuat akun', function (?string $email, int $status) {
    $awal = User::count();
    $req = $email ? $this->actingAs(aksiAkun($email)) : $this;

    $req->post(route('superuser.akun.store'), aksiPayloadAkun())->assertStatus($status);

    expect(User::count())->toBe($awal);
})->with([
    'admin domain' => ['superbatubara@test.com', 403],
    'user biasa' => ['user@test.com', 403],
    'tamu' => [null, 302],
]);

it('Super User bisa mengubah akun lain', function () {
    $target = aksiAkun('user@test.com');

    $this->actingAs(aksiSuper())
        ->put(route('superuser.akun.update', $target), [
            'name' => 'Nama Diubah',
            'username' => 'user',
            'email' => 'user@test.com',
            'role' => 'admin',
            'domain_akses' => 'batubara',
            'is_active' => '1',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('superuser.akun.index'));

    $target->refresh();
    expect($target->name)->toBe('Nama Diubah');
    expect($target->role)->toBe('admin');
    expect($target->domain_akses)->toBe('batubara');
});

it('Super User tidak bisa menurunkan atau menonaktifkan akunnya sendiri', function () {
    $su = aksiSuper();

    $this->actingAs($su)
        ->put(route('superuser.akun.update', $su), [
            'name' => $su->name,
            'username' => $su->username,
            'email' => $su->email,
            'role' => 'user',
            'is_active' => '0',
        ])
        ->assertRedirect(route('superuser.akun.index'));

    $su->refresh();
    expect($su->role)->toBe('super_user');
    expect($su->is_active)->toBeTrue();
});

it('Super User tidak bisa menonaktifkan atau menghapus akunnya sendiri', function () {
    $su = aksiSuper();

    $this->actingAs($su)->patch(route('superuser.akun.status', $su))->assertSessionHas('error');
    $this->actingAs($su)->delete(route('superuser.akun.destroy', $su))->assertSessionHas('error');

    expect($su->fresh()->is_active)->toBeTrue();
    expect(User::whereKey($su->id)->exists())->toBeTrue();
});

it('Super User bisa menonaktifkan lalu mengaktifkan lagi akun lain', function () {
    $target = aksiAkun('user@test.com');

    $this->actingAs(aksiSuper())->patch(route('superuser.akun.status', $target));
    expect($target->fresh()->is_active)->toBeFalse();

    $this->actingAs(aksiSuper())->patch(route('superuser.akun.status', $target));
    expect($target->fresh()->is_active)->toBeTrue();
});

it('Super User bisa mereset password akun lain', function () {
    $target = aksiAkun('user@test.com');

    $this->actingAs(aksiSuper())
        ->put(route('superuser.akun.password', $target), ['password' => 'passwordbaru1', 'password_confirmation' => 'passwordbaru1'])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('superuser.akun.edit', $target));

    $target->refresh();
    expect(Hash::check('passwordbaru1', $target->password))->toBeTrue();
    expect(Hash::check('password', $target->password))->toBeFalse();
});

it('reset password ditolak kalau terlalu pendek atau konfirmasi beda', function (array $data) {
    $target = aksiAkun('user@test.com');

    $this->actingAs(aksiSuper())
        ->put(route('superuser.akun.password', $target), $data)
        ->assertSessionHasErrors('password', null, 'reset');

    expect(Hash::check('password', $target->fresh()->password))->toBeTrue();
})->with([
    'terlalu pendek' => [['password' => '123', 'password_confirmation' => '123']],
    'konfirmasi beda' => [['password' => 'passwordbaru1', 'password_confirmation' => 'passwordlain1']],
]);

it('Super User bisa menghapus akun lain', function () {
    $akun = User::create([
        'name' => 'Akun Sementara',
        'username' => 'sementara',
        'email' => 'sementara@test.com',
        'password' => 'password',
        'role' => 'user',
        'is_active' => true,
    ]);

    $this->actingAs(aksiSuper())
        ->delete(route('superuser.akun.destroy', $akun))
        ->assertRedirect(route('superuser.akun.index'));

    expect(User::whereKey($akun->id)->exists())->toBeFalse();
});

/* ------------------------------------------------------------------ */
/* AKUN SAYA                                                           */
/* ------------------------------------------------------------------ */

it('Akun Saya: nama dan username bisa diubah, email TIDAK', function () {
    $user = aksiAkun('user@test.com');

    $this->actingAs($user)
        ->put(route('akun-saya.update'), ['name' => 'Nama Baru', 'username' => 'User_Baru', 'email' => 'curang@test.com'])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('akun-saya.edit'));

    $user->refresh();
    expect($user->name)->toBe('Nama Baru');
    expect($user->username)->toBe('user_baru');
    expect($user->email)->toBe('user@test.com');
});

it('Akun Saya: username yang dipakai orang lain ditolak', function () {
    $user = aksiAkun('user@test.com');

    $this->actingAs($user)
        ->put(route('akun-saya.update'), ['name' => 'Nama Baru', 'username' => 'superuser'])
        ->assertSessionHasErrors('username');

    expect($user->fresh()->username)->toBe('user');
});

it('Akun Saya: ganti password dengan password lama yang benar', function () {
    $user = aksiAkun('user@test.com');

    $this->actingAs($user)
        ->put(route('akun-saya.password'), ['current_password' => 'password', 'password' => 'baru12345', 'password_confirmation' => 'baru12345'])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('akun-saya.edit'));

    expect(Hash::check('baru12345', $user->fresh()->password))->toBeTrue();
});

it('Akun Saya: ganti password ditolak', function (array $data) {
    $user = aksiAkun('user@test.com');

    $this->actingAs($user)
        ->put(route('akun-saya.password'), $data)
        ->assertSessionHasErrors('password', null, 'password');

    expect(Hash::check('password', $user->fresh()->password))->toBeTrue();
})->with([
    'password baru terlalu pendek' => [['current_password' => 'password', 'password' => '123', 'password_confirmation' => '123']],
    'konfirmasi beda' => [['current_password' => 'password', 'password' => 'baru12345', 'password_confirmation' => 'lain12345']],
    'sama dengan password lama' => [['current_password' => 'password', 'password' => 'password', 'password_confirmation' => 'password']],
]);

it('Akun Saya: password lama salah ditolak', function () {
    $user = aksiAkun('user@test.com');

    $this->actingAs($user)
        ->put(route('akun-saya.password'), ['current_password' => 'ngawur', 'password' => 'baru12345', 'password_confirmation' => 'baru12345'])
        ->assertSessionHasErrors('current_password', null, 'password');

    expect(Hash::check('password', $user->fresh()->password))->toBeTrue();
});

it('Akun Saya tidak bisa dipakai tamu', function () {
    $this->put(route('akun-saya.update'), ['name' => 'X', 'username' => 'xxx'])->assertRedirect(route('login'));
});

/* ------------------------------------------------------------------ */
/* HAPUS -> HISTORI -> PULIHKAN (semua domain)                         */
/* ------------------------------------------------------------------ */

it('hapus data masuk Histori, tercatat di Log, lalu bisa dipulihkan', function (string $slug, string $model, string $email, string $rute) {
    $admin = aksiAkun($email);
    $row = $model::query()->firstOrFail();
    $id = $row->getKey();
    $nama = $row->nama_objek;

    // --- hapus ---
    $this->actingAs($admin)->delete(route("{$rute}.destroy", $row))->assertRedirect(route("{$rute}.index"));

    $terhapus = $model::withTrashed()->findOrFail($id);
    expect($terhapus->deleted_at)->not->toBeNull();
    expect((int) $terhapus->deleted_by)->toBe((int) $admin->id);
    expect($model::query()->whereKey($id)->exists())->toBeFalse();
    expect(RiwayatData::where(['domain' => $slug, 'aksi' => 'dihapus', 'data_id' => $id])->exists())->toBeTrue();

    // --- halaman yang baru berisi data setelah ada yang dihapus ---
    $this->actingAs($admin)->get(route("{$rute}.histori.index"))->assertOk()->assertSee($nama);
    $this->actingAs($admin)->get(route("{$rute}.histori.log"))->assertOk()->assertSee($nama);
    $this->actingAs(aksiSuper())->get(route('superuser.histori-semua.index'))->assertOk();
    $this->actingAs(aksiSuper())->get(route('superuser.histori-semua.index', ['tab' => 'log']))->assertOk();
    $this->actingAs(aksiSuper())->get(route('superuser.dashboard'))->assertOk();

    // --- pulihkan ---
    $this->actingAs($admin)->post(route("{$rute}.histori.restore", $id))->assertRedirect();

    expect($model::query()->whereKey($id)->exists())->toBeTrue();
    expect($model::withTrashed()->findOrFail($id)->deleted_by)->toBeNull();
    expect(RiwayatData::where(['domain' => $slug, 'aksi' => 'dipulihkan', 'data_id' => $id])->exists())->toBeTrue();
})->with(aksiDomainList());

it('hapus permanen hanya boleh dilakukan Super User', function (string $slug, string $model, string $email, string $rute) {
    $admin = aksiAkun($email);
    $row = $model::query()->firstOrFail();
    $id = $row->getKey();

    $this->actingAs($admin)->delete(route("{$rute}.destroy", $row));
    $this->actingAs($admin)->delete(route("{$rute}.histori.force", $id))->assertForbidden();

    expect($model::onlyTrashed()->whereKey($id)->exists())->toBeTrue();

    $this->actingAs(aksiSuper())->delete(route("{$rute}.histori.force", $id))->assertRedirect();

    expect($model::withTrashed()->whereKey($id)->exists())->toBeFalse();
    expect(RiwayatData::where(['domain' => $slug, 'aksi' => 'dihapus_permanen', 'data_id' => $id])->exists())->toBeTrue();
})->with(aksiDomainList());

it('admin domain lain tidak boleh menghapus data domain ini', function (string $slug, string $model, string $email, string $rute) {
    $orangLain = aksiAkun($slug === 'batubara' ? 'superminerallogam@test.com' : 'superbatubara@test.com');
    $row = $model::query()->firstOrFail();

    $this->actingAs($orangLain)->delete(route("{$rute}.destroy", $row))->assertForbidden();

    expect($model::query()->whereKey($row->getKey())->exists())->toBeTrue();
})->with(aksiDomainList());

it('user biasa dan tamu tidak boleh menghapus data', function (?string $email, int $status) {
    $row = NeracaBatubara::query()->firstOrFail();
    $req = $email ? $this->actingAs(aksiAkun($email)) : $this;

    $req->delete(route('admin.batubara.destroy', $row))->assertStatus($status);

    expect(NeracaBatubara::query()->whereKey($row->id)->exists())->toBeTrue();
})->with([
    'user biasa' => ['user@test.com', 403],
    'tamu' => [null, 302],
]);

/* ------------------------------------------------------------------ */
/* CRUD BATUBARA                                                       */
/* ------------------------------------------------------------------ */

it('Batubara: tambah data, total dihitung otomatis', function () {
    $this->actingAs(aksiAkun('superbatubara@test.com'))
        ->post(route('admin.batubara.store'), aksiPayloadBatubara())
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.batubara.index'));

    $row = NeracaBatubara::where('nama_objek', 'Objek Uji Batubara')->first();

    expect($row)->not->toBeNull();
    expect((float) $row->total_sd)->toEqualWithDelta(35.5, 0.001);   // tereka + tertunjuk + terukur
    expect((float) $row->total_cad)->toEqualWithDelta(10.0, 0.001);  // terkira + terbukti
});

it('Batubara: tambah data ditolak kalau isian tidak valid', function (array $ubah, string $kolomError) {
    $awal = NeracaBatubara::count();

    // 'DOBEL' = pakai idbb yang sudah ada di database
    if (($ubah['idbb'] ?? null) === 'DOBEL') {
        $ubah['idbb'] = NeracaBatubara::query()->value('idbb');
    }

    $this->actingAs(aksiAkun('superbatubara@test.com'))
        ->post(route('admin.batubara.store'), aksiPayloadBatubara($ubah))
        ->assertSessionHasErrors($kolomError);

    expect(NeracaBatubara::count())->toBe($awal);
})->with([
    'idbb sudah ada' => [['idbb' => 'DOBEL'], 'idbb'],
    'nama objek kosong' => [['nama_objek' => ''], 'nama_objek'],
    'tahun bukan angka' => [['tahun_data' => 'dua ribu'], 'tahun_data'],
    'provinsi tidak ada' => [['provinsi_id' => 999999], 'provinsi_id'],
    'kelas kalori tidak ada' => [['kelas_kalori_id' => 999999], 'kelas_kalori_id'],
    'sumber daya bukan angka' => [['tereka' => 'abc'], 'tereka'],
]);

it('Batubara: ubah data, total dihitung ulang', function () {
    $admin = aksiAkun('superbatubara@test.com');
    $payload = aksiPayloadBatubara();

    $this->actingAs($admin)->post(route('admin.batubara.store'), $payload);
    $row = NeracaBatubara::where('nama_objek', 'Objek Uji Batubara')->firstOrFail();

    $this->actingAs($admin)
        ->put(route('admin.batubara.update', $row), array_merge($payload, [
            'nama_objek' => 'Objek Uji Diubah',
            'tereka' => '1', 'tertunjuk' => '2', 'terukur' => '3',
            'terkira' => '0.5', 'terbukti' => '0.25',
        ]))
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.batubara.index'));

    $row->refresh();
    expect($row->nama_objek)->toBe('Objek Uji Diubah');
    expect((float) $row->total_sd)->toEqualWithDelta(6.0, 0.001);
    expect((float) $row->total_cad)->toEqualWithDelta(0.75, 0.001);
});

it('Batubara: admin domain lain, user biasa, dan tamu tidak boleh menambah data', function (?string $email, int $status) {
    $awal = NeracaBatubara::count();
    $req = $email ? $this->actingAs(aksiAkun($email)) : $this;

    $req->post(route('admin.batubara.store'), aksiPayloadBatubara())->assertStatus($status);

    expect(NeracaBatubara::count())->toBe($awal);
})->with([
    'admin mineral logam' => ['superminerallogam@test.com', 403],
    'user biasa' => ['user@test.com', 403],
    'tamu' => [null, 302],
]);

/* ------------------------------------------------------------------ */
/* KELOLA REFERENSI                                                    */
/* ------------------------------------------------------------------ */

$semuaJenis = [
    'provinsi', 'kabupaten', 'kelompok-logam', 'komoditas-logam', 'kelompok-bukan-logam', 'komoditas-bukan-logam',
    'kelas-kalori', 'status-penyelidikan', 'instansi', 'status-penyelidikan-pb', 'klasifikasi-temperatur',
];

it('Kelola Referensi: tambah, ubah, lalu hapus data yang belum dipakai', function (string $jenis) {
    [$tabel, $kolom, $data] = aksiRef($jenis);
    [, , $ubah] = aksiRef($jenis, ' Ubah');
    $su = aksiSuper();

    $this->actingAs($su)
        ->post(route('superuser.referensi.store', $jenis), $data)
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('superuser.referensi.daftar', $jenis));

    $row = DB::table($tabel)->where($kolom, $data[$kolom])->first();
    expect($row)->not->toBeNull();

    $this->actingAs($su)
        ->put(route('superuser.referensi.update', [$jenis, $row->id]), $ubah)
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('superuser.referensi.daftar', $jenis));

    expect(DB::table($tabel)->where('id', $row->id)->value($kolom))->toBe($ubah[$kolom]);

    $this->actingAs($su)
        ->delete(route('superuser.referensi.destroy', [$jenis, $row->id]))
        ->assertRedirect(route('superuser.referensi.daftar', $jenis));

    expect(DB::table($tabel)->where('id', $row->id)->exists())->toBeFalse();
})->with($semuaJenis);

it('Kelola Referensi: nama kembar ditolak', function (string $jenis) {
    [$tabel, $kolom, $data] = aksiRef($jenis);
    $su = aksiSuper();

    $this->actingAs($su)->post(route('superuser.referensi.store', $jenis), $data)->assertSessionHasNoErrors();
    $this->actingAs($su)->post(route('superuser.referensi.store', $jenis), $data)->assertSessionHasErrors($kolom);

    expect(DB::table($tabel)->where($kolom, $data[$kolom])->count())->toBe(1);
})->with($semuaJenis);

it('Kelola Referensi: kabupaten dengan nama sama boleh ada di provinsi lain', function () {
    $provinsiIds = Provinsi::query()->orderBy('id')->limit(2)->pluck('id');
    $su = aksiSuper();

    foreach ($provinsiIds as $id) {
        $this->actingAs($su)
            ->post(route('superuser.referensi.store', 'kabupaten'), ['provinsi_id' => $id, 'nama_kabupaten' => 'Kabupaten Kembar Uji'])
            ->assertSessionHasNoErrors();
    }

    expect(Kabupaten::where('nama_kabupaten', 'Kabupaten Kembar Uji')->count())->toBe(2);
});

it('Kelola Referensi: isian tidak valid ditolak', function (string $jenis, array $data, string $kolomError) {
    $this->actingAs(aksiSuper())
        ->post(route('superuser.referensi.store', $jenis), $data)
        ->assertSessionHasErrors($kolomError);
})->with([
    'provinsi: pulau tidak dikenal' => ['provinsi', ['nama_provinsi' => 'Provinsi X', 'pulau' => 'Atlantis'], 'pulau'],
    'provinsi: nama kosong' => ['provinsi', ['nama_provinsi' => ''], 'nama_provinsi'],
    'komoditas logam: kelompok tidak ada' => ['komoditas-logam', ['kelompok_komoditas_logam_id' => 999999, 'nama_komoditas' => 'Komoditas X'], 'kelompok_komoditas_logam_id'],
    'kelas kalori: rentang kosong' => ['kelas-kalori', ['label' => 'Kelas X', 'rentang' => ''], 'rentang'],
]);

it('Kelola Referensi: data yang masih dipakai TIDAK boleh dihapus', function (string $jenis) {
    $id = match ($jenis) {
        'provinsi' => NeracaBatubara::query()->whereNotNull('provinsi_id')->value('provinsi_id'),
        'kabupaten' => NeracaBatubara::query()->whereNotNull('kabupaten_id')->value('kabupaten_id'),
        'kelas-kalori' => NeracaBatubara::query()->whereNotNull('kelas_kalori_id')->value('kelas_kalori_id'),
        'status-penyelidikan' => NeracaBatubara::query()->whereNotNull('stat_dik_bb_id')->value('stat_dik_bb_id'),
        'instansi' => NeracaBatubara::query()->whereNotNull('id_instansi_id')->value('id_instansi_id'),
        'komoditas-logam' => NeracaMineralLogam::query()->whereNotNull('komoditas_logam_id')->value('komoditas_logam_id'),
        'komoditas-bukan-logam' => NeracaMineralBukanLogam::query()->whereNotNull('komoditas_bukan_logam_id')->value('komoditas_bukan_logam_id'),
        'kelompok-logam' => DB::table('komoditas_logams')->value('kelompok_komoditas_logam_id'),
        'kelompok-bukan-logam' => DB::table('komoditas_bukan_logams')->value('kelompok_komoditas_bukan_logam_id'),
        'status-penyelidikan-pb' => NeracaPanasBumi::query()->whereNotNull('stat_dik_pb_id')->value('stat_dik_pb_id'),
        'klasifikasi-temperatur' => NeracaPanasBumi::query()->whereNotNull('klasifikasi_temperatur_id')->value('klasifikasi_temperatur_id'),
    };

    if ($id === null) {
        $this->markTestSkipped("Seeder tidak punya data yang memakai referensi '{$jenis}', jadi tes ini dilewati.");
    }

    [$tabel] = aksiRef($jenis);

    $this->actingAs(aksiSuper())
        ->delete(route('superuser.referensi.destroy', [$jenis, $id]))
        ->assertSessionHas('error', fn ($pesan) => str_contains($pesan, 'Tidak bisa dihapus'));

    expect(DB::table($tabel)->where('id', $id)->exists())->toBeTrue();
})->with($semuaJenis);

it('Kelola Referensi: referensi yang dipakai data di Histori juga tidak boleh dihapus', function () {
    $kelas = KelasKalori::create(['label' => 'Kelas Histori Uji', 'rentang' => '1 - 2']);
    $id = NeracaBatubara::query()->value('id');

    NeracaBatubara::whereKey($id)->update(['kelas_kalori_id' => $kelas->id]);
    NeracaBatubara::findOrFail($id)->delete(); // masuk Histori

    $this->actingAs(aksiSuper())
        ->delete(route('superuser.referensi.destroy', ['kelas-kalori', $kelas->id]))
        ->assertSessionHas('error', fn ($pesan) => str_contains($pesan, 'Tidak bisa dihapus'));

    expect(KelasKalori::whereKey($kelas->id)->exists())->toBeTrue();
});

it('Kelola Referensi hanya untuk Super User', function (?string $email, int $status) {
    $awal = Provinsi::count();
    $id = Provinsi::query()->value('id');
    $req = $email ? $this->actingAs(aksiAkun($email)) : $this;

    $req->post(route('superuser.referensi.store', 'provinsi'), ['nama_provinsi' => 'Provinsi Ilegal', 'pulau' => 'Jawa'])->assertStatus($status);
    $req->delete(route('superuser.referensi.destroy', ['provinsi', $id]))->assertStatus($status);

    expect(Provinsi::count())->toBe($awal);
})->with([
    'admin domain' => ['superbatubara@test.com', 403],
    'user biasa' => ['user@test.com', 403],
    'tamu' => [null, 302],
]);

/* ------------------------------------------------------------------ */
/* KELOLA TABEL BUKU                                                   */
/* ------------------------------------------------------------------ */

it('Tabel Buku: simpan dari teks Excel, tipe kolom & angka terbaca benar', function () {
    $this->actingAs(aksiSuper())
        ->post(route('superuser.tabel-buku.store'), aksiPayloadTabel())
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('superuser.tabel-buku.index'));

    $tabel = TabelReferensi::where('judul', 'Tabel Uji Rekap')->first();

    expect($tabel)->not->toBeNull();
    expect($tabel->domain)->toBe('batubara');
    expect($tabel->kode)->toBe('tabel-uji-rekap-2025');
    expect($tabel->kolom)->toHaveCount(3);
    expect(array_column($tabel->kolom, 't'))->toBe(['text', 'num', 'num']);
    expect($tabel->kolom[1]['d'])->toBe(2);
    expect($tabel->baris)->toHaveCount(3);
    expect($tabel->baris[0]['c'][1])->toEqualWithDelta(1234.56, 0.0001);
    expect($tabel->baris[0]['c'][2])->toEqualWithDelta(10.5, 0.0001);
    expect($tabel->baris[1]['c'][1])->toEqual(0);       // "-" dibaca 0
    expect($tabel->baris[2]['s'])->toBe('total');         // baris TOTAL otomatis bergaya total
});

it('Tabel Buku: tabel baru muncul di halaman Grafik dengan angka berformat Indonesia', function () {
    $this->actingAs(aksiSuper())->post(route('superuser.tabel-buku.store'), aksiPayloadTabel());

    $this->actingAs(aksiAkun('superbatubara@test.com'))
        ->get(route('admin.batubara.grafik.index'))
        ->assertOk()
        ->assertSee('Tabel Uji Rekap')
        ->assertSee('1.234,56')
        ->assertSee('10,50')
        ->assertSee('2,25');
});

it('Tabel Buku: kode otomatis dibuat unik kalau judulnya sama', function () {
    $su = aksiSuper();

    $this->actingAs($su)->post(route('superuser.tabel-buku.store'), aksiPayloadTabel());
    $this->actingAs($su)->post(route('superuser.tabel-buku.store'), aksiPayloadTabel())->assertSessionHasNoErrors();

    expect(TabelReferensi::where('judul', 'Tabel Uji Rekap')->orderBy('id')->pluck('kode')->all())
        ->toBe(['tabel-uji-rekap-2025', 'tabel-uji-rekap-2025-2']);
});

it('Tabel Buku: isian tidak valid ditolak', function (array $ubah, string $kolomError) {
    $awal = TabelReferensi::count();

    // 'DOBEL' = pakai kode yang sudah ada di database
    if (($ubah['kode'] ?? null) === 'DOBEL') {
        $ubah['kode'] = TabelReferensi::query()->value('kode');
    }

    $this->actingAs(aksiSuper())
        ->post(route('superuser.tabel-buku.store'), aksiPayloadTabel($ubah))
        ->assertSessionHasErrors($kolomError);

    expect(TabelReferensi::count())->toBe($awal);
})->with([
    'domain tidak dikenal' => [['domain' => 'sawit'], 'domain'],
    'judul kosong' => [['judul' => ''], 'judul'],
    'tahun di luar rentang' => [['tahun_data' => 1800], 'tahun_data'],
    'kode ada spasi' => [['kode' => 'kode salah'], 'kode'],
    'kode sudah dipakai' => [['kode' => 'DOBEL'], 'kode'],
    'data kosong' => [['data' => ''], 'data'],
    'data cuma judul kolom' => [['data' => "Provinsi\tNilai"], 'data'],
    'data tidak dipisah TAB' => [['data' => "Provinsi Nilai\nJawa 10"], 'data'],
    'baris punya kolom lebih banyak dari judul' => [['data' => "A\tB\nx\t1\t2"], 'data'],
]);

it('Tabel Buku: ubah isi tabel, angka & gaya total ikut berubah', function () {
    $su = aksiSuper();
    $this->actingAs($su)->post(route('superuser.tabel-buku.store'), aksiPayloadTabel());
    $tabel = TabelReferensi::where('judul', 'Tabel Uji Rekap')->firstOrFail();

    $teksBaru = implode("\n", [
        implode("\t", ['Provinsi', 'Sumber Daya', 'Cadangan']),
        implode("\t", ['Kalimantan Timur', '2.000,00', '10,5']),
        implode("\t", ['Sumatera Selatan', '-', '2,25']),
        implode("\t", ['TOTAL', '2.000,00', '12,75']),
    ]);

    $this->actingAs($su)
        ->put(route('superuser.tabel-buku.update', $tabel), aksiPayloadTabel(['judul' => 'Tabel Uji Diubah', 'data' => $teksBaru]))
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('superuser.tabel-buku.index'));

    $tabel->refresh();
    expect($tabel->judul)->toBe('Tabel Uji Diubah');
    expect($tabel->baris[0]['c'][1])->toEqualWithDelta(2000.0, 0.0001);
    expect($tabel->baris[2]['s'])->toBe('total');
});

it('Tabel Buku: hapus tabel', function () {
    $su = aksiSuper();
    $this->actingAs($su)->post(route('superuser.tabel-buku.store'), aksiPayloadTabel());
    $tabel = TabelReferensi::where('judul', 'Tabel Uji Rekap')->firstOrFail();

    $this->actingAs($su)->delete(route('superuser.tabel-buku.destroy', $tabel))->assertRedirect(route('superuser.tabel-buku.index'));

    expect(TabelReferensi::whereKey($tabel->id)->exists())->toBeFalse();
});

it('Tabel Buku hanya untuk Super User', function (?string $email, int $status) {
    $awal = TabelReferensi::count();
    $req = $email ? $this->actingAs(aksiAkun($email)) : $this;

    $req->post(route('superuser.tabel-buku.store'), aksiPayloadTabel())->assertStatus($status);
    $req->delete(route('superuser.tabel-buku.destroy', TabelReferensi::query()->firstOrFail()))->assertStatus($status);

    expect(TabelReferensi::count())->toBe($awal);
})->with([
    'admin domain' => ['superbatubara@test.com', 403],
    'user biasa' => ['user@test.com', 403],
    'tamu' => [null, 302],
]);

it('Tabel Buku: Edit lalu Simpan TANPA mengubah apa pun tidak boleh mengubah tampilan tabel', function () {
    $su = aksiSuper();
    $masalah = [];

    foreach (TabelReferensi::query()->orderBy('id')->get() as $tabel) {
        $respon = $this->actingAs($su)->put(route('superuser.tabel-buku.update', $tabel), [
            'domain' => $tabel->domain,
            'judul' => $tabel->judul,
            'tahun_data' => $tabel->tahun_data,
            'sumber' => $tabel->sumber,
            'urutan' => $tabel->urutan,
            'catatan' => $tabel->catatan,
            'data' => TabelParser::keTeks($tabel->kolom, $tabel->baris),
        ]);

        if ($respon->headers->get('Location') !== route('superuser.tabel-buku.index')) {
            $galat = app('session.store')->get('errors');
            $masalah[] = "{$tabel->kode}: simpan ditolak" . ($galat ? ' (' . implode('; ', $galat->all()) . ')' : '');
            continue;
        }

        $baru = TabelReferensi::findOrFail($tabel->id);
        $beda = 0;

        if (count($baru->kolom) !== count($tabel->kolom) || count($baru->baris) !== count($tabel->baris)) {
            $masalah[] = "{$tabel->kode}: jumlah kolom/baris berubah";
            continue;
        }

        foreach ($tabel->kolom as $j => $kol) {
            if (($baru->kolom[$j]['l'] ?? null) !== $kol['l'] || ($baru->kolom[$j]['t'] ?? null) !== ($kol['t'] ?? 'text')) {
                $masalah[] = "{$tabel->kode}: kolom \"{$kol['l']}\" berubah tipe dari " . ($kol['t'] ?? 'text') . ' menjadi ' . ($baru->kolom[$j]['t'] ?? '?');
            }
        }

        foreach ($tabel->baris as $i => $row) {
            foreach ($tabel->kolom as $j => $kol) {
                $asli = aksiTampil($row['c'][$j] ?? '', $kol);
                $kini = aksiTampil($baru->baris[$i]['c'][$j] ?? '', $baru->kolom[$j]);

                if ($asli !== $kini && $beda < 3) {
                    $beda++;
                    $masalah[] = "{$tabel->kode}: baris " . ($i + 1) . ", kolom \"{$kol['l']}\" tampil \"{$asli}\" tapi setelah Simpan jadi \"{$kini}\"";
                }
            }

            if (in_array($row['s'] ?? '', ['sub', 'child'], true) && ($baru->baris[$i]['s'] ?? '') !== $row['s']) {
                $masalah[] = "{$tabel->kode}: gaya baris " . ($i + 1) . ' (' . $row['s'] . ') hilang';
            }
        }
    }

    expect($masalah)->toBe([], "Tabel yang berubah setelah Edit -> Simpan:\n" . implode("\n", $masalah));
});
