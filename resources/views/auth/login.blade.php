<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Poll Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: #f0f2f5;
            min-height: 100vh;
            font-family: 'Inter', 'Segoe UI', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-wrapper {
            width: 100%;
            max-width: 420px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            background: #fff;
            overflow: hidden;
        }
        .card-header-section {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            padding: 28px 32px 22px;
            text-align: center;
        }
        .card-header-section .brand-icon {
            width: 44px;
            height: 44px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
        }
        .card-header-section .brand-icon i {
            color: #fff;
            font-size: 1.2rem;
        }
        .card-header-section h1 {
            color: #fff;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 4px;
        }
        .card-header-section p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.875rem;
            margin: 0;
        }
        .card-body { padding: 28px 32px 32px; }
        .btn-login {
            background: #2563eb;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            padding: 10px 20px;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            color: #fff;
            width: 100%;
        }
        .btn-login:hover {
            background: #1d4ed8;
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }
        .form-control {
            border-radius: 6px;
            border: 1px solid #e5e7eb;
            padding: 10px 14px;
            font-size: 0.875rem;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        .form-label {
            font-weight: 600;
            color: #374151;
            font-size: 0.85rem;
        }
        .alert {
            border-radius: 6px;
            border: none;
            font-size: 0.85rem;
            border-left: 4px solid transparent;
        }
        .alert-danger {
            background: rgba(220, 38, 38, 0.08);
            border-left-color: #dc2626;
            color: #991b1b;
        }
        .alert-success {
            background: rgba(5, 150, 105, 0.08);
            border-left-color: #059669;
            color: #065f46;
        }
        .credentials-box {
            background: rgba(37, 99, 235, 0.05);
            border-radius: 8px;
            padding: 14px 18px;
            border: 1px solid rgba(37, 99, 235, 0.1);
        }
        .credentials-box h6 {
            font-weight: 700;
            color: #374151;
            font-size: 0.8rem;
            margin-bottom: 8px;
        }
        .credentials-box code {
            background: rgba(37, 99, 235, 0.08);
            color: #2563eb;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 0.8rem;
        }
        .credentials-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
        }
        .credentials-row:last-child { margin-bottom: 0; }
        .credentials-label {
            font-weight: 600;
            font-size: 0.8rem;
            color: #6b7280;
        }
        .input-icon {
            position: relative;
        }
        .input-icon i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 0.85rem;
        }
        .input-icon .form-control {
            padding-left: 38px;
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="card">
            <div class="card-header-section">
                <div class="brand-icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <h1>Poll Platform</h1>
                <p>Sign in to continue</p>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-1"></i>
                    <?php echo escape($_SESSION['error']); unset($_SESSION['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-1"></i>
                    <?php echo escape($_SESSION['success']); unset($_SESSION['success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <form method="POST" action="/login" id="loginForm">
                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="input-icon">
                            <i class="fas fa-envelope"></i>
                            <input
                                type="email"
                                class="form-control"
                                id="email"
                                name="email"
                                placeholder="you@example.com"
                                value="<?php echo htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                required
                                autocomplete="email"
                            >
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-icon">
                            <i class="fas fa-lock"></i>
                            <input
                                type="password"
                                class="form-control"
                                id="password"
                                name="password"
                                placeholder="Enter your password"
                                required
                                autocomplete="current-password"
                            >
                        </div>
                    </div>

                    <button type="submit" class="btn btn-login mb-3" id="loginBtn">
                        <i class="fas fa-sign-in-alt me-1"></i> Sign In
                    </button>
                </form>

                <div class="credentials-box mt-2">
                    <h6><i class="fas fa-key me-1"></i> Demo Credentials</h6>
                    <div class="credentials-row">
                        <span class="credentials-label">Admin:</span>
                        <span><code>admin@polling.test</code> / <code>admin123</code></span>
                    </div>
                    <div class="credentials-row">
                        <span class="credentials-label">User:</span>
                        <span><code>user@polling.test</code> / <code>user123</code></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
