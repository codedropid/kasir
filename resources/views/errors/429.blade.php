@extends('errors.layout')

@section('title', '429 - Terlalu Banyak Permintaan')
@section('code', '429')
@section('glow_color', 'bg-amber-500/10 border-amber-500/30 text-amber-400')
@section('badge_color', 'bg-amber-500/15 text-amber-400 border-amber-500/30')

@section('icon')
    <i data-lucide="gauge" class="w-12 h-12 text-amber-400"></i>
@endsection

@section('heading', 'Terlalu Cepat Menekan Tombol!')
@section('message', 'Sistem menerima terlalu banyak aksi dalam waktu yang sangat singkat. Hal ini dilakukan untuk melindungi keamanan data transaksi kasir.')
@section('suggestion', 'Silakan tunggu sekitar 30 detik sebelum mencoba kembali melakukan aksi pada sistem.')
