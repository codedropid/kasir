@extends('errors.layout')

@section('title', '403 - Akses Ditolak')
@section('code', '403')
@section('glow_color', 'bg-rose-500/10 border-rose-500/30 text-rose-400')
@section('badge_color', 'bg-rose-500/15 text-rose-400 border-rose-500/30')

@section('icon')
    <i data-lucide="shield-alert" class="w-12 h-12 text-rose-400"></i>
@endsection

@section('heading', 'Akses Terbatas / Ditolak')
@section('message', 'Anda tidak memiliki hak akses yang cukup untuk membuka fitur ini. Halaman manajemen produk dan laporan omset hanya dapat diakses oleh Administrator.')
@section('suggestion', 'Silakan masuk dengan akun Admin atau hubungi supervisor kafe jika Anda memerlukan izin akses.')
