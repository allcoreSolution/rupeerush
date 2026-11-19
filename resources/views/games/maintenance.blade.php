@extends('layouts.app')

@section('title', ($gameName ?? 'Game') . ' - Under Maintenance')

@section('content')
<div class="container py-5 text-center min-vh-75 d-flex align-items-center justify-content-center">
    <div class="card bg-dark border border-warning border-opacity-25 rounded-4 p-4 p-md-5 shadow-lg text-white" style="max-width: 580px; background: linear-gradient(145deg, #0f172a 0%, #1e1b4b 100%) !important;">
        <div class="mb-4">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning" style="width: 80px; height: 80px; font-size: 2.5rem; border: 2px dashed rgba(245, 158, 11, 0.4);">
                <i class="fa-solid fa-screwdriver-wrench"></i>
            </div>
        </div>

        <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold text-uppercase mx-auto mb-3" style="font-size: 0.75rem; letter-spacing: 0.5px;">
            <i class="fa-solid fa-clock me-1"></i> Temporary Inactive / Maintenance
        </span>

        <h3 class="fw-bold mb-2 text-white">{{ $gameName ?? 'Game' }}</h3>
        <p class="text-secondary small mb-4" style="line-height: 1.6;">
            This game is currently undergoing scheduled optimization and maintenance to ensure fair play, smooth performance, and instant payouts. Please check back shortly or explore our other live games in the lobby.
        </p>

        <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center">
            <a href="{{ route('home') }}" class="btn btn-warning fw-bold px-4 py-2 rounded-pill shadow-sm">
                <i class="fa-solid fa-house me-1"></i> Return to Gaming Lobby
            </a>
            @if(Auth::check() && Auth::user()->isAdmin())
                <a href="{{ route('admin.games.status') }}" class="btn btn-outline-light px-3 py-2 rounded-pill fw-semibold" style="font-size: 0.85rem;">
                    <i class="fa-solid fa-sliders me-1"></i> Admin Game Settings
                </a>
            @endif
        </div>
    </div>
</div>
@endsection
