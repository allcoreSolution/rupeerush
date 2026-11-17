<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal Login - rupeerush Controlling Hub</title>

    <!-- Google Fonts: Inter & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800;900&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: radial-gradient(circle at 50% 20%, #152e4d 0%, #081426 60%, #030a14 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            color: #f1f5f9;
        }
        .admin-card {
            background: #0f1d30;
            border: 1px solid rgba(56, 189, 248, 0.25);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6), 0 0 40px rgba(56, 189, 248, 0.1);
            border-radius: 20px;
            overflow: hidden;
            width: 100%;
            max-width: 440px;
        }
        .admin-header {
            background: linear-gradient(180deg, #162a45 0%, #0f1d30 100%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            padding: 32px 24px 24px;
            text-align: center;
        }
        .admin-icon-shield {
            width: 68px;
            height: 68px;
            border-radius: 18px;
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.8rem;
            margin-bottom: 16px;
            box-shadow: 0 8px 24px rgba(2, 132, 199, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .admin-brand-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: #fff;
            margin-bottom: 4px;
        }
        .admin-brand-title span {
            color: #38bdf8;
        }
        .form-control-admin {
            background: #081426 !important;
            border: 1px solid #1e3a5f !important;
            color: #f1f5f9 !important;
            border-radius: 12px;
            padding: 12px 14px;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }
        .form-control-admin:focus {
            border-color: #38bdf8 !important;
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.2) !important;
            color: #fff !important;
        }
        .input-group-admin-text {
            background: #081426;
            border: 1px solid #1e3a5f;
            color: #64748b;
            border-radius: 12px 0 0 12px;
            padding: 0 14px;
        }
        .btn-admin-submit {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            border: none;
            color: #fff;
            font-weight: 700;
            padding: 13px;
            border-radius: 50px;
            font-size: 1rem;
            letter-spacing: 0.5px;
            transition: all 0.2s;
            box-shadow: 0 6px 20px rgba(2, 132, 199, 0.35);
        }
        .btn-admin-submit:hover {
            background: linear-gradient(135deg, #0369a1 0%, #075985 100%);
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(2, 132, 199, 0.5);
        }
    </style>
</head>
<body>

<div class="admin-card">
    <div class="admin-header">
        <div class="admin-icon-shield">
            <i class="fa-solid fa-shield-halved"></i>
        </div>
        <div class="admin-brand-title">RUPEERUSH <span>ADMIN</span></div>
        <div class="small text-info fw-bold text-uppercase letter-spacing-1 mb-2" style="font-size: 0.72rem; letter-spacing: 1.5px;">RUPEERUSH CONTROLLING HUB</div>
        <div>
            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill" style="font-size: 0.7rem; font-weight: 700;">
                <i class="fa-solid fa-lock me-1"></i> RESTRICTED ACCESS &bull; ADMINS ONLY
            </span>
        </div>
    </div>

    <div class="p-4">
        <!-- Error / Status Alerts -->
        @if(session('error'))
            <div class="alert alert-danger border-0 rounded-3 p-3 small mb-3 bg-danger bg-opacity-25 text-white" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-1 text-danger"></i> {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success border-0 rounded-3 p-3 small mb-3 bg-success bg-opacity-25 text-white" role="alert">
                <i class="fa-solid fa-circle-check me-1 text-success"></i> {{ session('success') }}
            </div>
        @endif

        @if($errors->has('login'))
            <div class="alert alert-danger border-0 rounded-3 p-3 small mb-3 bg-danger bg-opacity-25 text-white" role="alert">
                <i class="fa-solid fa-circle-exclamation me-1 text-danger"></i> {{ $errors->first('login') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.post') }}">
            @csrf

            <!-- Admin Login Identifier -->
            <div class="mb-3">
                <label for="adminLoginInput" class="form-label text-light small fw-bold" style="font-size: 0.8rem; color: #94a3b8;">
                    ADMINISTRATOR ID / PHONE / EMAIL
                </label>
                <div class="input-group">
                    <span class="input-group-text input-group-admin-text"><i class="fa-solid fa-user-shield"></i></span>
                    <input type="text" 
                           class="form-control form-control-admin border-start-0 @error('login') is-invalid @enderror" 
                           id="adminLoginInput" 
                           name="login" 
                           value="{{ old('login') }}" 
                            placeholder="Enter User ID, phone, or email" 
                            required 
                            autofocus>
                </div>
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label for="adminPasswordInput" class="form-label text-light small fw-bold" style="font-size: 0.8rem; color: #94a3b8;">
                    SECURITY KEY / PASSWORD
                </label>
                <div class="input-group">
                    <span class="input-group-text input-group-admin-text"><i class="fa-solid fa-key"></i></span>
                    <input type="password" 
                           class="form-control form-control-admin border-start-0 border-end-0 @error('password') is-invalid @enderror" 
                           id="adminPasswordInput" 
                           name="password" 
                           placeholder="••••••••••••" 
                           required>
                    <button type="button" class="input-group-text input-group-admin-text border-start-0" style="border-radius: 0 12px 12px 0;" onclick="toggleAdminPass()">
                        <i class="fa-regular fa-eye" id="togglePassIcon"></i>
                    </button>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="form-check">
                    <input class="form-check-input bg-dark border-secondary" type="checkbox" name="remember" id="rememberAdmin" checked>
                    <label class="form-check-label text-muted small" for="rememberAdmin" style="font-size: 0.8rem;">
                        Keep me signed in
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-admin-submit w-100 mb-3">
                <i class="fa-solid fa-fingerprint me-2"></i> Authenticate & Enter Hub
            </button>
        </form>

        <!-- Links to Player Portal -->
        <div class="text-center mt-4 pt-2 border-top border-secondary border-opacity-25 d-flex justify-content-between small">
            <a href="{{ route('home') }}" class="text-decoration-none text-muted" style="font-size: 0.8rem;">
                <i class="fa-solid fa-arrow-left me-1"></i> Live Game Portal
            </a>
            <a href="{{ route('login') }}" class="text-decoration-none text-info fw-bold" style="font-size: 0.8rem;">
                <i class="fa-solid fa-gamepad me-1"></i> Player Login &rarr;
            </a>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function toggleAdminPass() {
        const input = document.getElementById('adminPasswordInput');
        const icon = document.getElementById('togglePassIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
</script>
</body>
</html>
