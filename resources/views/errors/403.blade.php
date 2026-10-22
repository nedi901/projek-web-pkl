@extends('errors.layout')

@section('kode', '403')
@section('judul', 'Akses ditolak')
@section('pesan', 'Kamu tidak punya izin untuk membuka halaman ini.')

@section('tambahan')
    @php
        $detail = isset($exception) ? trim((string) $exception->getMessage()) : '';
    @endphp
    @if($detail !== '' && $detail !== 'This action is unauthorized.')
        <p class="text-xs text-gray-500 bg-gray-50 border border-gray-200 rounded-md px-3 py-2 mb-6">{{ $detail }}</p>
    @endif
@endsection
