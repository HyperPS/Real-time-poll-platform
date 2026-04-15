<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Poll Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <style>
        .page-header {
            padding: 32px 0 8px;
        }
        .page-header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1a1f2e;
            margin-bottom: 4px;
        }
        .page-header p {
            color: #6b7280;
            font-size: 0.9rem;
            margin: 0;
        }
        .poll-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 24px;
        }
        .poll-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            overflow: hidden;
        }
        .poll-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        }
        .poll-card .card-body {
            padding: 20px;
        }
        .poll-card .card-title {
            font-size: 1rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 10px;
            line-height: 1.4;
        }
        .poll-meta {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
            font-size: 0.8rem;
            color: #6b7280;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #9ca3af;
        }
        .empty-state i {
            font-size: 48px;
            margin-bottom: 16px;
            display: block;
            color: #d1d5db;
        }
        .empty-state p {
            font-size: 0.95rem;
            margin: 0;
        }
        @media (max-width: 991px) {
            .poll-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 575px) {
            .poll-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
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
                        <a class="nav-link active" href="/dashboard">
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

    <!-- Messages -->
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
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-poll-h me-2" style="color:#2563eb"></i>Active Polls</h1>
            <p>Browse available polls and cast your vote</p>
        </div>

        <?php if (!empty($polls)): ?>
        <div class="poll-grid">
            <?php foreach ($polls as $poll): ?>
            <div class="poll-card">
                <div class="card-body">
                    <h5 class="card-title"><?php echo escape($poll['question']); ?></h5>
                    <div class="poll-meta">
                        <span class="badge bg-<?php echo ($poll['status'] ?? 'active') === 'active' ? 'success' : 'danger'; ?>">
                            <?php echo ucfirst(escape($poll['status'] ?? 'active')); ?>
                        </span>
                        <span>
                            <i class="fas fa-calendar-alt me-1"></i>
                            <?php echo date('M j, Y', strtotime($poll['created_at'])); ?>
                        </span>
                    </div>
                    <a href="/poll/<?php echo intval($poll['id']); ?>" class="btn btn-primary w-100">
                        <i class="fas fa-vote-yea me-1"></i> Vote Now
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <p>No active polls available at the moment</p>
        </div>
        <?php endif; ?>
    </div>

    <footer class="text-center mt-5 pb-4">
        <small class="text-muted">&copy; 2026 Poll Platform</small>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>
