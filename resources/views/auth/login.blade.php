<?php
$title = 'Login - Poll System';
$content = ob_get_clean();
?>

<div class="row justify-content-center min-vh-100 align-items-center">
    <div class="col-md-5">
        <div class="card shadow-lg">
            <div class="card-body p-5">
                <h1 class="card-title text-center mb-4">🗳️ Poll System</h1>
                <h5 class="text-center text-muted mb-4">Login to Your Account</h5>

                <form method="POST" action="/login">
                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

                    <div class="mb-3">
                        <label for="email" class="form-label fw-600">Email Address</label>
                        <input 
                            type="email" 
                            class="form-control" 
                            id="email" 
                            name="email"
                            placeholder="Enter your email"
                            value="<?php echo old('email'); ?>"
                            required
                        >
                        <small class="text-muted d-block mt-2">Demo: admin@polling.test or user@polling.test</small>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label fw-600">Password</label>
                        <input 
                            type="password" 
                            class="form-control" 
                            id="password" 
                            name="password"
                            placeholder="Enter your password"
                            required
                        >
                        <small class="text-muted d-block mt-2">Demo passwords: admin123 or user123</small>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 mb-3">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                </form>

                <hr>

                <div class="alert alert-info" role="alert">
                    <h6 class="alert-heading mb-2">Test Credentials:</h6>
                    <small>
                        <strong>Admin:</strong> admin@polling.test / admin123<br>
                        <strong>User:</strong> user@polling.test / user123
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
ob_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 12px 48px rgba(0, 0, 0, 0.2);
            background: rgba(255, 255, 255, 0.98);
        }

        .btn-primary {
            background: #4f46e5;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: #4338ca;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            padding: 10px 15px;
        }

        .form-control:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .alert {
            border-radius: 8px;
            border: none;
        }

        h1 {
            color: #4f46e5;
            font-weight: bold;
        }

        h5 {
            color: #666;
        }

        .form-label {
            color: #333;
        }

        small {
            color: #999;
        }
    </style>
</head>
<body>
    <div class="row justify-content-center w-100">
        <div class="col-md-5">
            <div class="card shadow-lg">
                <div class="card-body p-5">
                    <h1 class="card-title text-center mb-4">🗳️ Poll System</h1>
                    <h5 class="text-center text-muted mb-4">Login to Your Account</h5>

                    <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo escape($_SESSION['error']); unset($_SESSION['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <form method="POST" action="/login">
                        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

                        <div class="mb-3">
                            <label for="email" class="form-label fw-600">Email Address</label>
                            <input 
                                type="email" 
                                class="form-control" 
                                id="email" 
                                name="email"
                                placeholder="Enter your email"
                                value="<?php echo old('email'); ?>"
                                required
                            >
                            <small class="text-muted d-block mt-2">Demo: admin@polling.test or user@polling.test</small>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-600">Password</label>
                            <input 
                                type="password" 
                                class="form-control" 
                                id="password" 
                                name="password"
                                placeholder="Enter your password"
                                required
                            >
                            <small class="text-muted d-block mt-2">Demo passwords: admin123 or user123</small>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 mb-3">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </button>
                    </form>

                    <hr>

                    <div class="alert alert-info" role="alert">
                        <h6 class="alert-heading mb-2">Test Credentials:</h6>
                        <small>
                            <strong>Admin:</strong> admin@polling.test / admin123<br>
                            <strong>User:</strong> user@polling.test / user123
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
