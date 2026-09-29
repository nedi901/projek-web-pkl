# rename-superuser-to-admin.ps1
# Ganti nama TOTAL: superuser -> admin (URL, route, middleware, view, folder, namespace)
# Jalankan dari ROOT project (folder yang ada file "artisan"):
#   powershell -ExecutionPolicy Bypass -File .\rename-superuser-to-admin.ps1

$ErrorActionPreference = 'Stop'

if (-not (Test-Path 'artisan')) { throw 'Jalankan dari root project (folder yang ada file artisan).' }

# --- Pengaman: jangan lanjut kalau nama tujuan udah kepake ---
foreach ($p in @('app\Http\Controllers\Admin', 'resources\views\admin', 'resources\views\components\layouts\admin.blade.php')) {
    if (Test-Path $p) { throw "Sudah ada: $p  -> dibatalkan supaya gak nimpa. Cek dulu isinya." }
}

$utf8 = New-Object System.Text.UTF8Encoding($false)   # tanpa BOM, line ending (CRLF) gak diubah
$em = [string][char]0x2014

# Aturan replace (urutan penting). Semuanya sengaja SEMPIT (harus diawali tanda kutip / punya
# konteks) supaya nama file kayak "superuser.blade.php" dan role "super_user" TIDAK ikut kena.
$rules = @(
    @{ p = '([\x27\x22])superuser\.';        r = '${1}admin.' },        # 'superuser.batubara.index' (route, middleware, view)
    @{ p = '([\x27\x22\x60])/superuser/';    r = '${1}/admin/' },       # '/superuser/x' dan `/superuser/x` (fetch JS)
    @{ p = '([\x27\x22])superuser/';         r = '${1}admin/' },        # prefix('superuser/batubara')
    @{ p = 'layouts\.superuser';             r = 'layouts.admin' },     # <x-layouts.superuser>
    @{ p = 'SuperUser';                      r = 'Admin' },             # namespace, class, alias
    @{ p = "Kelola data neraca $em Super User"; r = "Kelola data neraca $em Admin" },
    @{ p = 'Super User - Neraca SDM';        r = 'Admin - Neraca SDM' }
)

# --- 1. Ganti ISI file ---
$files = Get-ChildItem -Path 'routes', 'app', 'bootstrap', 'resources\views' -Recurse -File -Include *.php
$changed = @()
foreach ($f in $files) {
    $orig = [System.IO.File]::ReadAllText($f.FullName)
    $text = $orig
    foreach ($rule in $rules) { $text = [regex]::Replace($text, $rule.p, $rule.r) }
    if ($text -ne $orig) {
        [System.IO.File]::WriteAllText($f.FullName, $text, $utf8)
        $changed += $f.FullName.Substring((Get-Location).Path.Length + 1)
    }
}
Write-Host "`n[1/3] Isi diubah di $($changed.Count) file:" -ForegroundColor Cyan
$changed | ForEach-Object { Write-Host "   $_" }

# --- 2. Ganti nama FOLDER & FILE ---
Write-Host "`n[2/3] Rename folder/file:" -ForegroundColor Cyan
$moves = @(
    @('app\Http\Controllers\SuperUser', 'app\Http\Controllers\Admin'),
    @('resources\views\superuser', 'resources\views\admin'),
    @('resources\views\components\layouts\superuser.blade.php', 'resources\views\components\layouts\admin.blade.php')
)
foreach ($m in $moves) {
    if (Test-Path $m[0]) { Move-Item $m[0] $m[1]; Write-Host "   $($m[0])  ->  $($m[1])" }
    else { Write-Host "   (dilewati, gak ada) $($m[0])" -ForegroundColor Yellow }
}
Get-ChildItem 'app\Http\Middleware' -Filter 'CheckSuperUser*.php' -ErrorAction SilentlyContinue | ForEach-Object {
    $new = $_.Name -replace 'CheckSuperUser', 'CheckAdmin'
    Move-Item $_.FullName (Join-Path $_.DirectoryName $new)
    Write-Host "   $($_.Name)  ->  $new"
}

# --- 3. Cek sisa ---
Write-Host "`n[3/3] Cek sisa kata 'superuser' (harusnya kosong):" -ForegroundColor Cyan
$left = Get-ChildItem -Path 'routes', 'app', 'bootstrap', 'resources\views' -Recurse -File -Include *.php |
        Select-String -Pattern 'superuser' -CaseSensitive:$false
if ($left) { $left | ForEach-Object { Write-Host "   $($_.Path.Substring((Get-Location).Path.Length + 1)):$($_.LineNumber): $($_.Line.Trim())" -ForegroundColor Yellow } }
else { Write-Host "   Bersih." -ForegroundColor Green }

Write-Host "`nSelesai. Lanjut jalankan:" -ForegroundColor Green
Write-Host "   composer dump-autoload"
Write-Host "   php artisan optimize:clear"
Write-Host "   php artisan route:list --name=admin"
