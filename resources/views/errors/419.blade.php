@extends('errors.layout')

@section('title', '419 - Sesi Kedaluwarsa')
@section('code', '419')
@section('glow_color', 'bg-amber-500/10 border-amber-500/30 text-amber-400')
@section('badge_color', 'bg-amber-500/15 text-amber-400 border-amber-500/30')

@section('icon')
    <i data-lucide="timer-off" class="w-12 h-12 text-amber-400"></i>
@endsection

@section('heading', 'Sesi Anda Telah Berakhir')
@section('message', 'Waktu sesi kasir atau token keamanan keamanan halaman telah kedaluwarsa karena tidak ada aktivitas selama beberapa saat.')
@section('suggestion', 'Silakan klik tombol "Muat Ulang Halaman" atau login kembali untuk memperbarui sesi kerja kasir Anda.')
