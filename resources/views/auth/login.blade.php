@extends('layouts.app')

@section('title', 'Player Login - rupeerush')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5 col-xl-4">
            
            <!-- Alert Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm p-3 mb-4 small" role="alert">
                    <i class="fa-solid fa-circle-check me-2 text-success"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm p-3 mb-4 small" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2 text-danger"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="p-4 text-center text-white position-relative" style="background: linear-gradient(135deg, #09192f 0%, #153965 100%);">
                    <span class="badge bg-success bg-opacity-75 text-white px-3 py-1 rounded-pill mb-2 small fw-bold">
                        <i class="fa-solid fa-gamepad me-1"></i> PLAYER PORTAL
                    </span>
                    <h3 class="fw-bold mb-1">Player Login</h3>
                    <p class="text-white-50 small mb-0">Enter your details to bet, play & win live payouts</p>
                </div>

                <div class="card-body p-4 bg-white">
                    <form method="POST" action="{{ route('login.post') }}">
                        @csrf

                        <!-- Mobile Number / Email -->
                        <div class="mb-3">
                            <label for="loginInput" class="form-label fw-bold small text-muted">Mobile Number / Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-mobile-screen"></i></span>
                                <input type="text" 
                                       class="form-control bg-light border-start-0 py-2 @error('login') is-invalid @enderror" 
                                       id="loginInput" 
                                       name="login" 
                                       value="{{ old('login') }}" 
                                       placeholder="Enter mobile or email" 
                                       required 
                                       autofocus>
                            </div>
                            @error('login')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="passwordInput" class="form-label fw-bold small text-muted">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-lock"></i></span>
                                <input type="password" 
                                       class="form-control bg-light border-start-0 border-end-0 py-2 @error('password') is-invalid @enderror" 
                                       id="passwordInput" 
                                       name="password" 
                                       placeholder="••••••••" 
                                       required>
                                <button type="button" class="input-group-text bg-light border-start-0 text-muted" onclick="togglePasswordVisibility('passwordInput', this)">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="rememberMe" checked>
                                <label class="form-check-label small text-muted" for="rememberMe">Remember me</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm" style="background: linear-gradient(135deg, #0077b6 0%, #0096c7 100%); border: none; font-size: 1rem;">
                            <i class="fa-solid fa-arrow-right-to-bracket me-2"></i> Login to Play
                        </button>
                    </form>

                    <div class="text-center mt-4 pt-3 border-top">
                        <span class="text-muted small">Don't have an account?</span>
                        <a href="{{ route('register') }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none fw-bold small text-success ms-1">
                            <i class="fa-brands fa-whatsapp me-1"></i> Register on WhatsApp
                        </a>
                    </div>

                    <!-- Discrete Link to Separate Admin Login -->
                    <div class="text-center mt-3 pt-2">
                        <a href="{{ route('admin.login') }}" class="text-muted small text-decoration-none">
                            <i class="fa-solid fa-shield-halved me-1 text-secondary"></i> Administrator Portal Login &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function togglePasswordVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
</script>
@endsection
