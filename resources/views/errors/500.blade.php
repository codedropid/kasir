@extends('errors.layout')

@section('title', '500 - Terjadi Kesalahan Sistem')
@section('code', '500')
@section('glow_color', 'bg-red-500/10 border-red-500/30 text-red-400')
@section('badge_color', 'bg-red-500/15 text-red-400 border-red-500/30')

@section('icon')
    <i data-lucide="server-crash" class="w-12 h-12 text-red-400"></i>
@endsection

@section('heading', 'Kopi Tumpah di Mesin!')
@section('message', 'Terjadi kendala teknis internal pada server aplikasi saat memproses pesanan atau data. Data transaksi terakhir Anda tetap aman tersimpan di database.')
@section('suggestion', 'Silakan coba muat ulang halaman. Jika kendala masih berlanjut, hubungi tim teknis atau administrator sistem kafe.')
