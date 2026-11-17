@extends('layouts.app')

@section('title', 'Register via WhatsApp - rupeerush')

@section('content')
@php
    $siteSetting = \App\Models\AdminSetting::current();
    $waUrl = $siteSetting->getRegistrationWhatsappUrl($referralCode ?? request('ref'));
@endphp
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden text-center">
                <div class="p-4 text-white" style="background: linear-gradient(135deg, #128C7E 0%, #075E54 100%);">
                    <div class="d-inline-flex p-3 rounded-circle bg-white bg-opacity-20 mb-3 shadow-sm">
                        <i class="fa-brands fa-whatsapp text-white fs-1"></i>
                    </div>
                    <h4 class="fw-bold mb-1">WhatsApp Player Registration</h4>
                    <p class="text-white-50 small mb-0">Official Account Creation & Onboarding</p>
                </div>

                <div class="card-body p-4 p-md-5 bg-white">
                    <div class="mb-4">
                        <h6 class="fw-bold text-dark mb-2">Connect with Admin to Register</h6>
                        <p class="text-muted small mb-0">
                            To ensure high security, player registration is processed directly by our official administrator on WhatsApp. Admin will collect your details and create your player account.
                        </p>
                    </div>

                    @if(!empty($referralCode ?? request('ref')))
                        <div class="alert alert-warning border border-warning-subtle py-2 px-3 small rounded-3 mb-4 text-start d-flex align-items-center gap-2">
                            <i class="fa-solid fa-gift text-warning fs-5"></i>
                            <div>
                                <span class="text-muted">Referral Applied:</span>
                                <strong class="text-dark font-monospace">{{ $referralCode ?? request('ref') }}</strong>
                            </div>
                        </div>
                    @endif

                    <div class="d-grid gap-2 mb-4">
                        <a href="{{ $waUrl }}" class="btn py-3 rounded-pill fw-bold text-white shadow-sm d-flex align-items-center justify-content-center gap-2 fs-6" style="background: #25D366; border: none;">
                            <i class="fa-brands fa-whatsapp fs-5"></i>
                            <span>Open WhatsApp & Register Now</span>
                        </a>
                    </div>

                    <div class="text-muted small mb-3">
                        <i class="fa-solid fa-headset me-1 text-success"></i> WhatsApp Support Number: 
                        <strong class="text-dark font-monospace">+{{ $siteSetting->formatted_whatsapp_number }}</strong>
                    </div>

                    <div class="pt-3 border-top text-center">
                        <span class="text-muted small">Already have an account?</span>
                        <a href="{{ route('login') }}" class="text-decoration-none fw-bold small text-primary ms-1">Login Here &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Automatically redirect to WhatsApp
    setTimeout(function() {
        window.location.href = "{{ $waUrl }}";
    }, 600);
</script>
@endsection
