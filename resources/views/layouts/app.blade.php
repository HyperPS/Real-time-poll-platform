<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Poll Platform'; ?></title>

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome 6.4 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <!-- Navigation -->
    <?php if (isset($_SESSION['user_id'])): ?>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="/dashboard">
                <i class="fas fa-chart-bar"></i> Poll Platform
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard">
                            <i class="fas fa-th-large me-1"></i> Dashboard
                        </a>
                    </li>
                    <?php if ($_SESSION['user_role'] === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/dashboard">
                            <i class="fas fa-shield-alt me-1"></i> Admin Panel
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/polls/create">
                            <i class="fas fa-plus me-1"></i> Create Poll
                        </a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i> <?php echo escape($_SESSION['user_name']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/logout"><i class="fas fa-sign-out-alt me-1"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <?php endif; ?>

    <!-- Session Messages -->
    <div class="container">
        <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            <i class="fas fa-exclamation-circle me-1"></i>
            <?php echo escape($_SESSION['error']); unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            <i class="fas fa-check-circle me-1"></i>
            <?php echo escape($_SESSION['success']); unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
    </div>

    <!-- Main Content -->
    <div class="container mt-4">
        <?php echo $content ?? ''; ?>
    </div>

    <!-- Footer -->
    <footer class="text-center mt-5 pb-4">
        <small class="text-muted">&copy; 2026 Poll Platform</small>
    </footer>

    <!-- Bootstrap 5.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery 3.6 -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Custom JS -->
    <script src="/js/app.js"></script>
</body>
</html>
