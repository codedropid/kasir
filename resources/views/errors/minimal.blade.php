@extends('errors.layout')

@section('title', $title ?? 'Terjadi Kendala')
@section('code', $exception ? $exception->getStatusCode() : '500')
@section('glow_color', 'bg-amber-500/10 border-amber-500/30 text-amber-400')
@section('badge_color', 'bg-amber-500/15 text-amber-400 border-amber-500/30')

@section('icon')
    <i data-lucide="alert-triangle" class="w-12 h-12 text-amber-400"></i>
@endsection

@section('heading', 'Terjadi Kendala pada Sistem')
@section('message', $message ?? 'Maaf, sistem mengalami kendala saat memproses permintaan Anda.')
@section('suggestion', 'Silakan klik tombol muat ulang halaman atau kembali ke halaman utama kasir.')
