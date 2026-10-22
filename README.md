# Sistem Informasi Neraca Sumber Daya Mineral, Batubara, dan Panas Bumi Indonesia

Aplikasi web (Laravel) untuk mengelola dan menampilkan **neraca sumber daya dan cadangan** lima domain:
**Batubara, Mineral Logam, Mineral Bukan Logam, Panas Bumi, dan Gambut**, lengkap dengan Dashboard, Grafik, tabel
rekap "buku", riwayat penghapusan, dan import Excel.

## Fitur utama

- **Dashboard** ringkasan semua domain (Super User dan User), memakai data tahun **2024–2025**.
- **Grafik dan Visualisasi** per domain: total sumber daya/cadangan, klasifikasi (tereka, tertunjuk, terukur, dst.),
  perbandingan antar provinsi, tren per tahun, dan filter (komoditas, klasifikasi, pulau, provinsi).
- **Data per domain**: tambah, ubah, hapus, cari; total sumber daya/cadangan dihitung otomatis.
- **Histori**: data yang dihapus masuk Histori, bisa **dipulihkan**; hapus permanen hanya oleh Super User; ada log aktivitas.
- **Import Excel** per domain (lihat "Template import").
- **Kelola Referensi**: provinsi, kabupaten, komoditas, kelas kalori, status penyelidikan, instansi, klasifikasi temperatur.
  Referensi yang masih dipakai data **tidak bisa dihapus**.
- **Kelola Tabel Buku**: tempel tabel dari Excel (pisah TAB), otomatis jadi tabel rekap di halaman Grafik.
- **Kelola Akun** (Super User): buat/ubah/nonaktifkan akun, reset password. **Akun Saya**: ubah nama, username, password.

## Peran

| Peran | Hak akses |
|---|---|
| **Super User** | Kendali penuh: semua domain, akun, referensi, tabel buku, hapus permanen |
| **Admin domain** | Mengelola data **satu domain** miliknya (tambah, ubah, hapus, import, pulihkan) |
| **User** | Hanya melihat (Dashboard dan Grafik) |

## Menjalankan di komputer sendiri

Kebutuhan: PHP 8.3 atau lebih baru, Composer, dan ekstensi PHP `sqlite`, `zip`, `gd`/`mbstring` (umumnya sudah ada).

```
composer install
copy .env.example .env          (Windows)   |   cp .env.example .env   (Linux/Mac)
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Lalu buka <http://127.0.0.1:8000/login>. Di Windows bisa juga klik dua kali `mulai.bat`
(file harus satu folder dengan `artisan`, dan `php` harus bisa dipanggil dari CMD).

### Akun contoh

Login dengan email **atau** username. **Semua password: `password`** (hanya untuk demo, lihat "Sebelum dipakai sungguhan").

| Peran | Email | Username |
|---|---|---|
| Super User | admin@test.com | superuser |
| Admin Batubara | superbatubara@test.com | admin_batubara |
| Admin Mineral Logam | superminerallogam@test.com | admin_logam |
| Admin Mineral Bukan Logam | superminerallogambukan@test.com | admin_bukanlogam |
| Admin Panas Bumi | superpanasbumi@test.com | admin_panasbumi |
| Admin Gambut | supergambut@test.com | admin_gambut |
| User Biasa | user@test.com | user |

## Template import Excel

Baris pertama = judul kolom (huruf kecil, persis seperti di bawah). Satu baris data = satu objek.

| Domain | Kolom |
|---|---|
| Batubara | `provinsi, pulau, kabupaten, klsbb, statdikbb, idinstansi, idbb, tahun_data, tahun_neraca, namobj, hipbb, tereka, tertunjuk, terukur, total_sd, terkira, terbukti, total_cad, remark, bujur, lintang` |
| Mineral Logam | `provinsi, pulau, kabupaten, kellgm, jnskom, statdiklgm, idinstansi, idlgm, tahun_data, tahun_neraca, namobj, bjhhip, loghip, bjhtrka, logtrka, bjhtjuk, logtjuk, bjhtkur, logtkur, bjhtkira, logtkira, bjhtbkt, logtbkt, remark, bujur, lintang` |
| Mineral Bukan Logam | `idbl, nama_objek, tahun_data, tahun_neraca, komoditas, provinsi, kabupaten, stat_dik, instansi, hipotetik, tereka, tertunjuk, terukur, terkira, terbukti, bujur, lintang, remark` |
| Panas Bumi | `idpb, nama_objek, tahun_data, tahun_neraca, provinsi, kabupaten, stat_dik, instansi, klasifikasi_temperatur, temperatur_reservoir, spekulatif, hipotetik, terduga, mungkin, terbukti, kapasitas_terpasang, bujur, lintang, remark` |
| Gambut | `idgb, nama_objek, tahun_data, tahun_neraca, provinsi, kabupaten, nilai_kalori_min, nilai_kalori_max, luas_ha, volume_juta_m3, total_sd, bujur, lintang, remark` |

Aturan import:

- Sel angka **kosong atau `-`** dibaca 0. Angka format Indonesia di sel teks (`1.234,5`) ikut terbaca.
- **Teks bukan angka** (mis. `abc`) membatalkan **seluruh** import dengan pesan yang menyebut isinya; tidak ada data setengah jadi.
- **Judul kolom salah atau hilang**: import dibatalkan dengan pesan nama kolomnya.
- Baris **tanpa ID** atau dengan **referensi tidak dikenal** (komoditas, status penyelidikan) dilewati dan dilaporkan setelah import.
- ID yang **sudah ada** dilewati, jadi mengimpor file yang sama dua kali tidak menggandakan data.
- Batubara dan Mineral Logam memakai nama kolom atribut KUGI untuk angkanya, tetapi **template ini belum selaras dengan semua
  atribut file KUGI asli** (lihat "Batasan yang diketahui").

## Tes otomatis

```
php artisan test
```

Ratusan tes mencakup: login/logout dan pembatasan percobaan login, hak akses per peran, kelola akun, hapus lalu pulihkan
di semua domain, tambah/ubah data dan validasinya, kolom angka dikosongkan, Kelola Referensi, Kelola Tabel Buku, import Excel
(termasuk file bermasalah), angka di Dashboard/Grafik dibandingkan langsung dengan isi database, dan halaman error.
Tes memakai database di memori, jadi `database.sqlite` asli tidak tersentuh.

## Sumber data

Buku *Neraca Sumber Daya dan Cadangan Mineral, Batubara, dan Panas Bumi Indonesia* edisi 2025 (data 2024) dan 2026
(data 2025). Data panas bumi berupa rekap per provinsi (Tabel 47) dan sebagian lapangan (Tabel 45; **tabel ini tidak memuat
provinsi**, pemetaan provinsi lapangan dilakukan manual). Detail dan catatan tiap angka ada di komentar seeder
(`database/seeders`).

## Sebelum dipakai sungguhan (server / pengguna lain)

1. **Ganti semua password akun contoh**: login sebagai Super User, buka Kelola Akun, lalu Reset Password tiap akun (atau
   hapus akun yang tidak dipakai).
2. Di `.env` server: `APP_ENV=production` dan **`APP_DEBUG=false`** supaya detail error tidak tampil ke pengguna.
3. Setelah mengubah `.env`: `php artisan config:cache` (dan `php artisan route:cache` bila perlu).
4. **Backup rutin** `database/database.sqlite` (cukup salin file-nya).
5. Percobaan login dibatasi (5 kali salah per akun, 20 per alamat IP, per menit); halaman error sudah rapi dan tidak membocorkan
   detail teknis.

## Batasan yang diketahui

- Dashboard dan Grafik memakai data tahun **2024–2025**. Data tahun lain belum ada.
- **Klasifikasi temperatur panas bumi** belum terisi karena buku tidak membagi data per kelas suhu; filternya otomatis
  disembunyikan sampai ada data berklasifikasi.
- **Template import belum selaras penuh dengan atribut KUGI asli** (mis. kolom `TREKABB`, `TJUKBB` untuk Batubara;
  `SPEKPB`, `HIPPB` untuk Panas Bumi), dan KUGI tidak memuat kolom provinsi/kabupaten/tahun.
- Tampilan di HP/tablet belum diperiksa.
- Tidak ada fitur unduh/ekspor data (Excel/PDF) dan tidak ada peta sebaran.
