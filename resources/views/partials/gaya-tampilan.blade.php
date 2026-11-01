{{-- Gaya tampilan bersama (admin dan user): font, warna, kartu, tabel, isian, sidebar.
     Satu tempat supaya tampilan konsisten dan mudah diubah/dibatalkan: hapus @include di layout untuk kembali ke gaya lama.
     Selektor diawali "body" agar lebih kuat daripada kelas Tailwind CDN yang disisipkan belakangan. --}}
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style id="gaya-tampilan">
    body { font-family: 'IBM Plex Sans', ui-sans-serif, system-ui, -apple-system, 'Segoe UI', sans-serif; background-color: #F4F5F7; color: #1B1F24; -webkit-font-smoothing: antialiased; }

    /* Kartu & bentuk: sudut kecil, garis tipis, tanpa bayangan */
    body .rounded-xl, body .rounded-lg, body .rounded-md { border-radius: 6px; }
    body .shadow, body .shadow-sm, body .shadow-md, body .shadow-lg { box-shadow: none; }
    body .border-gray-200 { border-color: #DFE3E8; }
    body .border-gray-300 { border-color: #CBD2D9; }

    /* Teks: kontras lebih baik */
    body .text-gray-800 { color: #1B1F24; }
    body .text-gray-500 { color: #5B6572; }
    body .text-gray-400 { color: #6B7480; }
    body h1 { letter-spacing: -0.01em; font-weight: 600; }
    body .tabular, body td, body th { font-variant-numeric: tabular-nums; }

    /* Tabel */
    body th { font-weight: 500; color: #5B6572; letter-spacing: 0.02em; }
    body tbody tr:hover { background-color: #FAFBFC; }

    /* Isian */
    body input:focus, body select:focus, body textarea:focus { outline: 2px solid rgba(47, 91, 234, 0.25); outline-offset: 0; border-color: #2F5BEA; }

    /* Sidebar: putih, menu aktif kuning muda, label kelompok kecil */
    #sidebar { border-color: #DFE3E8; }
    #sidebar nav a, #sidebar a { border-radius: 6px; transition: background-color .15s; }
    #sidebar a.bg-\[\#F2C230\] { background-color: #FBF3D1; color: #1B1F24; font-weight: 500; }
    #sidebar a:not(.bg-\[\#F2C230\]):hover { background-color: #F4F5F7; }
    #sidebar .uppercase { font-size: 11px; letter-spacing: 0.06em; color: #6B7480; }
</style>
