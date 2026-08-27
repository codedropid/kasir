@extends('errors.layout')

@section('title', '404 - Menu atau Halaman Tidak Ditemukan')
@section('code', '404')
@section('glow_color', 'bg-amber-500/10 border-amber-500/30 text-amber-400')
@section('badge_color', 'bg-amber-500/15 text-amber-400 border-amber-500/30')

@section('icon')
    <i data-lucide="coffee" class="w-12 h-12 text-amber-400"></i>
@endsection

@section('heading', 'Cangkir Ini Kosong!')
@section('message', 'Halaman atau pesanan yang Anda cari tidak ditemukan di daftar menu kafe kami. Mungkin tautan telah dipindahkan, terhapus, atau salah ketik.')
@section('suggestion', 'Periksa kembali URL yang Anda tuju atau kembali ke halaman katalog kasir untuk melanjutkan transaksi.')
