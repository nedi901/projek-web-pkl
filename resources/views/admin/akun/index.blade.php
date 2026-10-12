<x-layouts.admin title="Kelola Akun">
    @php
        $badge = [
            'super_user' => 'bg-purple-100 text-purple-800',
            'admin' => 'bg-yellow-100 text-yellow-800',
            'user' => 'bg-gray-100 text-gray-700',
        ];
    @endphp

    <div class="flex justify-between items-end mb-4">
        <div>
            <h1 class="text-xl font-bold">Kelola Akun</h1>
            <p class="text-xs text-gray-500">Buat, ubah, nonaktifkan, dan hapus akun pengguna. Hanya Super User yang dapat membuat dan mengelola akun.</p>
        </div>
        <a href="{{ route('superuser.akun.create') }}" class="bg-[#F2C230] hover:bg-[#e0b526] text-black text-sm font-semibold px-4 py-2 rounded-md">
            + Tambah Akun
        </a>

    </div>

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-2.5 mb-4">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 mb-4">
        @foreach([
            ['Total Akun', $ringkasan['total']],
            ['Super User', $ringkasan['super_user']],
            ['Admin', $ringkasan['admin']],
            ['User', $ringkasan['user']],
            ['Nonaktif', $ringkasan['nonaktif']],
        ] as [$lbl, $jml])
            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3">
                <div class="text-[10px] uppercase text-gray-400">{{ $lbl }}</div>
                <div class="text-lg font-bold">{{ $jml }}</div>
            </div>
        @endforeach
    </div>

    <form method="GET" class="flex flex-wrap gap-3 mb-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, username, atau email..."
               class="border-gray-300 rounded-md text-sm flex-1 min-w-[220px]">
        <select name="role" class="border-gray-300 rounded-md text-sm">
            <option value="">Semua role</option>
            @foreach(\App\Models\User::ROLES as $val => $lbl)
                <option value="{{ $val }}" @selected(request('role') === $val)>{{ $lbl }}</option>
            @endforeach
        </select>
        <select name="status" class="border-gray-300 rounded-md text-sm">
            <option value="">Semua status</option>
            <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
            <option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif</option>
        </select>
        <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-md text-sm">Filter</button>
    </form>

    <div class="bg-white border rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-3 py-2 text-left">Nama</th>
                        <th class="px-3 py-2 text-left">Username</th>
                        <th class="px-3 py-2 text-left">Email</th>
                        <th class="px-3 py-2 text-left">Role</th>
                        <th class="px-3 py-2 text-left">Domain</th>
                        <th class="px-3 py-2 text-left">Status</th>
                        <th class="px-3 py-2 text-left">Login Terakhir</th>
                        <th class="px-3 py-2 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($akuns as $a)
                    @php $saya = $a->id === auth()->id(); @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 font-medium">{{ $a->name }}@if($saya) <span class="text-[10px] text-gray-400">(Anda)</span>@endif</td>
                        <td class="px-3 py-2 font-mono text-xs">{{ $a->username }}</td>
                        <td class="px-3 py-2">{{ $a->email }}</td>
                        <td class="px-3 py-2"><span class="px-2 py-0.5 rounded text-xs font-medium {{ $badge[$a->role] ?? 'bg-gray-100' }}">{{ $a->roleLabel() }}</span></td>
                        <td class="px-3 py-2">{{ $a->role === 'super_user' ? 'Semua domain' : $a->domainLabel() }}</td>
                        <td class="px-3 py-2">
                            @if($a->is_active)
                                <span class="px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-600">
                            {{ $a->last_login_at ? $a->last_login_at->copy()->timezone('Asia/Jakarta')->locale('id')->translatedFormat('d M Y, H:i') . ' WIB' : 'Belum pernah' }}
                        </td>
                        <td class="px-3 py-2 text-center whitespace-nowrap">
                            <a href="{{ route('superuser.akun.edit', $a) }}" class="text-blue-600 hover:underline mr-3">Edit</a>
                            @unless($saya)
                                <form method="POST" action="{{ route('superuser.akun.status', $a) }}" class="inline"
                                      onsubmit="return confirm('{{ $a->is_active ? 'Nonaktifkan akun ini? Akun tidak bisa login lagi.' : 'Aktifkan kembali akun ini?' }}')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-yellow-700 hover:underline mr-3">{{ $a->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                                </form>
                                <form method="POST" action="{{ route('superuser.akun.destroy', $a) }}" class="inline"
                                      onsubmit="return confirm('HAPUS PERMANEN akun ini? Tindakan ini tidak bisa dibatalkan. Kalau cuma mau menutup akses, pakai Nonaktifkan.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                </form>
                            @endunless
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-6 text-gray-400">Tidak ada akun yang cocok</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $akuns->links() }}</div>
</x-layouts.admin>
