@extends('errors.layout')

@section('title', '503 - Sedang Dalam Pemeliharaan')
@section('code', '503')
@section('glow_color', 'bg-amber-500/10 border-amber-500/30 text-amber-400')
@section('badge_color', 'bg-amber-500/15 text-amber-400 border-amber-500/30')

@section('icon')
    <i data-lucide="wrench" class="w-12 h-12 text-amber-400"></i>
@endsection

@section('heading', 'Sedang Kalibrasi Mesin Kafe')
@section('message', 'Sistem Kasir Kafe sedang menjalani pemeliharaan rutin atau pembaruan fitur untuk meningkatkan performa dan keandalan transaksi.')
@section('suggestion', 'Layanan akan segera aktif kembali dalam beberapa menit. Silakan tunggu sebentar dan muat ulang halaman.')
