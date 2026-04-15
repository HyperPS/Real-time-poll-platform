<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Real-Time Poll Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
            min-height: 100vh;
            display: flex;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            width: 240px;
            min-height: 100vh;
            background: #1a1f2e;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1040;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s;
        }
        .sidebar-brand {
            padding: 20px 18px;
            border-bottom: 1px solid rgba(255,255,255,0.07);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sidebar-brand .brand-icon {
            width: 34px;
            height: 34px;
            background: #2563eb;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 15px;
            flex-shrink: 0;
        }
        .sidebar-brand span {
            color: #fff;
            font-weight: 700;
            font-size: 15px;
            white-space: nowrap;
        }
        .sidebar-nav {
            padding: 14px 0;
            flex: 1;
        }
        .sidebar-nav .nav-label {
            color: rgba(255,255,255,0.35);
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            padding: 12px 22px 6px;
        }
        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 22px;
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }
        .sidebar-nav a:hover {
            color: #fff;
            background: rgba(255,255,255,0.05);
        }
        .sidebar-nav a.active {
            color: #fff;
            background: rgba(37,99,235,0.15);
            border-left-color: #2563eb;
        }
        .sidebar-nav a i {
            width: 18px;
            text-align: center;
            font-size: 14px;
        }
        .sidebar-user {
            padding: 16px 18px;
            border-top: 1px solid rgba(255,255,255,0.07);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sidebar-user .user-avatar {
            width: 34px;
            height: 34px;
            background: #2563eb;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 13px;
            font-weight: 600;
            flex-shrink: 0;
        }
        .sidebar-user .user-info {
            overflow: hidden;
        }
        .sidebar-user .user-info .user-name {
            color: #fff;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .sidebar-user .user-info .user-role {
            color: rgba(255,255,255,0.4);
            font-size: 11px;
            text-transform: capitalize;
        }

        /* ===== MAIN CONTENT ===== */
        .main-wrapper {
            margin-left: 240px;
            flex: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ===== TOP NAVBAR ===== */
        .top-navbar {
            background: #fff;
            border-bottom: 1px solid #e2e5ea;
            padding: 0 28px;
            height: 58px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1030;
        }
        .top-navbar .page-title {
            font-size: 16px;
            font-weight: 700;
            color: #1a1f2e;
        }
        .top-navbar .navbar-actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .top-navbar .navbar-actions a {
            color: #64748b;
            font-size: 13px;
            text-decoration: none;
            font-weight: 500;
        }
        .top-navbar .navbar-actions a:hover { color: #2563eb; }

        /* ===== CONTENT AREA ===== */
        .content-area {
            padding: 24px 28px 40px;
            flex: 1;
        }

        /* ===== STAT CARDS ===== */
        .stat-card {
            background: #fff;
            border: 1px solid #e2e5ea;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            border-left: 4px solid transparent;
        }
        .stat-card.border-primary { border-left-color: #2563eb; }
        .stat-card.border-success { border-left-color: #059669; }
        .stat-card.border-warning { border-left-color: #d97706; }
        .stat-card.border-accent  { border-left-color: #8b5cf6; }
        .stat-card .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
            flex-shrink: 0;
        }
        .stat-icon.bg-primary { background: rgba(37,99,235,0.1); color: #2563eb; }
        .stat-icon.bg-success { background: rgba(5,150,105,0.1); color: #059669; }
        .stat-icon.bg-warning { background: rgba(217,119,6,0.1); color: #d97706; }
        .stat-icon.bg-accent  { background: rgba(139,92,246,0.1); color: #8b5cf6; }
        .stat-card .stat-info .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: #1a1f2e;
            line-height: 1.1;
        }
        .stat-card .stat-info .stat-label {
            font-size: 12.5px;
            color: #64748b;
            font-weight: 500;
            margin-top: 2px;
        }

        /* ===== TABS ===== */
        .dashboard-tabs {
            margin-top: 24px;
        }
        .dashboard-tabs .nav-tabs {
            background: #fff;
            border-radius: 8px 8px 0 0;
            border: 1px solid #e2e5ea;
            border-bottom: none;
            padding: 0 8px;
        }
        .dashboard-tabs .nav-tabs .nav-link {
            border: none;
            color: #64748b;
            font-size: 13.5px;
            font-weight: 600;
            padding: 14px 18px;
            border-bottom: 2px solid transparent;
            margin-bottom: -1px;
            background: transparent;
        }
        .dashboard-tabs .nav-tabs .nav-link:hover {
            color: #1a1f2e;
        }
        .dashboard-tabs .nav-tabs .nav-link.active {
            color: #2563eb;
            border-bottom-color: #2563eb;
            background: transparent;
        }
        .dashboard-tabs .tab-content {
            background: #fff;
            border: 1px solid #e2e5ea;
            border-top: none;
            border-radius: 0 0 8px 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            padding: 20px;
        }

        /* ===== TABLES ===== */
        .table-container { overflow-x: auto; }
        .data-table {
            width: 100%;
            font-size: 13px;
        }
        .data-table thead th {
            background: #f8f9fb;
            color: #475569;
            font-weight: 600;
            font-size: 11.5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 10px 14px;
            border-bottom: 1px solid #e2e5ea;
            white-space: nowrap;
        }
        .data-table tbody td {
            padding: 10px 14px;
            color: #334155;
            border-bottom: 1px solid #f1f3f5;
            vertical-align: middle;
        }
        .data-table tbody tr:hover {
            background: #f8f9fb;
        }
        .data-table tbody tr:last-child td { border-bottom: none; }

        /* ===== BADGES ===== */
        .badge-status {
            display: inline-block;
            padding: 3px 10px;
            font-size: 11.5px;
            font-weight: 600;
            border-radius: 4px;
        }
        .badge-active   { background: rgba(5,150,105,0.1); color: #059669; }
        .badge-inactive { background: rgba(220,38,38,0.1); color: #dc2626; }
        .badge-closed   { background: rgba(100,116,139,0.1); color: #64748b; }
        .badge-admin    { background: rgba(139,92,246,0.1); color: #8b5cf6; }
        .badge-user     { background: rgba(37,99,235,0.1); color: #2563eb; }

        .badge-action {
            display: inline-block;
            padding: 3px 10px;
            font-size: 11px;
            font-weight: 600;
            border-radius: 4px;
        }
        .badge-login_success  { background: rgba(5,150,105,0.1); color: #059669; }
        .badge-login_failed   { background: rgba(220,38,38,0.1); color: #dc2626; }
        .badge-vote_cast      { background: rgba(37,99,235,0.1); color: #2563eb; }
        .badge-poll_created   { background: rgba(139,92,246,0.1); color: #8b5cf6; }
        .badge-poll_closed    { background: rgba(217,119,6,0.1); color: #d97706; }
        .badge-logout         { background: rgba(100,116,139,0.1); color: #64748b; }
        .badge-default        { background: rgba(100,116,139,0.1); color: #64748b; }

        /* ===== BUTTONS ===== */
        .btn-sm {
            font-size: 12px;
            padding: 4px 12px;
            border-radius: 5px;
            font-weight: 500;
        }
        .btn-primary-custom {
            background: #2563eb;
            color: #fff;
            border: none;
        }
        .btn-primary-custom:hover { background: #1d4ed8; color: #fff; }
        .btn-danger-custom {
            background: #dc2626;
            color: #fff;
            border: none;
        }
        .btn-danger-custom:hover { background: #b91c1c; color: #fff; }
        .btn-outline-secondary {
            border: 1px solid #cbd5e1;
            color: #475569;
            background: #fff;
        }
        .btn-outline-secondary:hover {
            background: #f8f9fb;
            color: #1a1f2e;
            border-color: #94a3b8;
        }

        /* ===== FORM STYLES ===== */
        .form-section .form-label {
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 4px;
        }
        .form-section .form-control,
        .form-section .form-select {
            font-size: 13.5px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 8px 12px;
        }
        .form-section .form-control:focus,
        .form-section .form-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
        }

        /* ===== FILTER BAR ===== */
        .filter-bar {
            display: flex;
            gap: 12px;
            align-items: center;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }
        .filter-bar .form-select {
            font-size: 12.5px;
            padding: 6px 10px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            max-width: 200px;
        }
        .filter-bar label {
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
        }

        /* ===== ALERT ===== */
        .alert-container {
            margin-bottom: 16px;
        }
        .alert-container .alert {
            font-size: 13px;
            border-radius: 6px;
            padding: 10px 16px;
        }

        /* ===== MODAL ===== */
        .modal-header {
            background: #f8f9fb;
            border-bottom: 1px solid #e2e5ea;
            padding: 14px 20px;
        }
        .modal-title {
            font-size: 15px;
            font-weight: 700;
            color: #1a1f2e;
        }
        .modal-body { padding: 20px; }
        .user-stat-header {
            display: flex;
            align-items: center;
            gap: 14px;
            padding-bottom: 16px;
            border-bottom: 1px solid #f1f3f5;
            margin-bottom: 16px;
        }
        .user-stat-header .user-avatar-lg {
            width: 48px;
            height: 48px;
            background: #2563eb;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 18px;
            font-weight: 600;
            flex-shrink: 0;
        }
        .user-stat-header .user-meta h6 {
            margin: 0;
            font-size: 14px;
            font-weight: 700;
            color: #1a1f2e;
        }
        .user-stat-header .user-meta small {
            color: #64748b;
            font-size: 12px;
        }
        .modal-section-title {
            font-size: 13px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .modal-section-title i { color: #2563eb; font-size: 13px; }

        /* ===== MISC ===== */
        .text-truncate-sm {
            max-width: 150px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: inline-block;
            vertical-align: middle;
        }
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #94a3b8;
        }
        .empty-state i { font-size: 32px; margin-bottom: 10px; display: block; }
        .empty-state p { font-size: 13px; margin: 0; }
        .refresh-indicator {
            font-size: 11px;
            color: #94a3b8;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .spinner-sm {
            width: 14px;
            height: 14px;
            border-width: 2px;
        }

        @media (max-width: 991px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-wrapper { margin-left: 0; }
            .sidebar-toggle { display: inline-flex !important; }
        }
        @media (min-width: 992px) {
            .sidebar-toggle { display: none !important; }
        }
    </style>
</head>
<body>

<!-- ===== SIDEBAR ===== -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon"><i class="fas fa-chart-bar"></i></div>
        <span>Poll Platform</span>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-label">Navigation</div>
        <a href="/dashboard"><i class="fas fa-th-large"></i> Dashboard</a>
        <a href="/admin/dashboard" class="active"><i class="fas fa-shield-alt"></i> Admin Panel</a>
        <a href="/polls/create"><i class="fas fa-plus-circle"></i> Create Poll</a>
        <div class="nav-label">Account</div>
        <a href="/logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
    <div class="sidebar-user">
        <div class="user-avatar"><?php echo strtoupper(substr(escape($_SESSION['user_name'] ?? 'A'), 0, 1)); ?></div>
        <div class="user-info">
            <div class="user-name"><?php echo escape($_SESSION['user_name'] ?? 'Admin'); ?></div>
            <div class="user-role"><?php echo escape($_SESSION['user_role'] ?? 'admin'); ?></div>
        </div>
    </div>
</aside>

<!-- ===== MAIN WRAPPER ===== -->
<div class="main-wrapper">

    <!-- Top Navbar -->
    <header class="top-navbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-outline-secondary sidebar-toggle" onclick="document.getElementById('sidebar').classList.toggle('show')">
                <i class="fas fa-bars"></i>
            </button>
            <span class="page-title"><i class="fas fa-shield-alt me-2" style="color:#2563eb"></i>Admin Dashboard</span>
        </div>
        <div class="navbar-actions">
            <span class="text-muted" style="font-size:12px">
                <i class="fas fa-clock me-1"></i>
                <span id="currentTime"></span>
            </span>
        </div>
    </header>

    <!-- Content Area -->
    <div class="content-area">

        <!-- ===== STAT CARDS ===== -->
        <div class="row g-3 mb-2">
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card border-primary">
                    <div class="stat-icon bg-primary"><i class="fas fa-poll-h"></i></div>
                    <div class="stat-info">
                        <div class="stat-value"><?php echo (int)($totalPolls ?? 0); ?></div>
                        <div class="stat-label">Total Polls</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card border-success">
                    <div class="stat-icon bg-success"><i class="fas fa-broadcast-tower"></i></div>
                    <div class="stat-info">
                        <div class="stat-value"><?php echo (int)($activePolls ?? 0); ?></div>
                        <div class="stat-label">Active Polls</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card border-warning">
                    <div class="stat-icon bg-warning"><i class="fas fa-users"></i></div>
                    <div class="stat-info">
                        <div class="stat-value"><?php echo (int)($totalUsers ?? 0); ?></div>
                        <div class="stat-label">Total Users</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card border-accent">
                    <div class="stat-icon bg-accent"><i class="fas fa-vote-yea"></i></div>
                    <div class="stat-info">
                        <div class="stat-value"><?php echo (int)($totalVotes ?? 0); ?></div>
                        <div class="stat-label">Total Votes</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== TABS ===== -->
        <div class="dashboard-tabs">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-polls" type="button">
                        <i class="fas fa-poll-h me-1"></i> Polls
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-votes" type="button" id="votesTabBtn">
                        <i class="fas fa-vote-yea me-1"></i> Votes
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-users" type="button">
                        <i class="fas fa-users me-1"></i> Users
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-logs" type="button" id="logsTabBtn">
                        <i class="fas fa-file-alt me-1"></i> Activity Logs
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-adduser" type="button">
                        <i class="fas fa-user-plus me-1"></i> Add User
                    </button>
                </li>
            </ul>

            <div class="tab-content">

                <!-- ==================== TAB 1: POLLS ==================== -->
                <div class="tab-pane fade show active" id="tab-polls">
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Question</th>
                                    <th>Status</th>
                                    <th>Votes</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($polls)): ?>
                                    <?php foreach ($polls as $i => $poll): ?>
                                    <tr>
                                        <td><?php echo $i + 1; ?></td>
                                        <td><?php echo escape($poll['question']); ?></td>
                                        <td>
                                            <span class="badge-status <?php echo $poll['status'] === 'active' ? 'badge-active' : 'badge-closed'; ?>">
                                                <?php echo escape($poll['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo (int)($poll['vote_count'] ?? 0); ?></td>
                                        <td><?php echo escape(date('M j, Y', strtotime($poll['created_at']))); ?></td>
                                        <td>
                                            <a href="/admin/polls/<?php echo (int)$poll['id']; ?>" class="btn btn-sm btn-primary-custom">
                                                <i class="fas fa-cog me-1"></i>Manage
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6">
                                            <div class="empty-state">
                                                <i class="fas fa-poll-h"></i>
                                                <p>No polls found</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- ==================== TAB 2: VOTES ==================== -->
                <div class="tab-pane fade" id="tab-votes">
                    <div id="votesLoading" class="text-center py-4">
                        <div class="spinner-border spinner-sm text-primary" role="status"></div>
                        <span class="ms-2 text-muted" style="font-size:13px">Loading votes...</span>
                    </div>
                    <div class="table-container" id="votesTableWrap" style="display:none">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Poll</th>
                                    <th>Option Voted</th>
                                    <th>User</th>
                                    <th>IP Address</th>
                                    <th>User Agent</th>
                                    <th>Device</th>
                                    <th>Voted At</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="votesTableBody"></tbody>
                        </table>
                    </div>
                </div>

                <!-- ==================== TAB 3: USERS ==================== -->
                <div class="tab-pane fade" id="tab-users">
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Votes</th>
                                    <th>Last Login</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($users)): ?>
                                    <?php foreach ($users as $i => $user): ?>
                                    <tr id="user-row-<?php echo (int)$user['id']; ?>">
                                        <td><?php echo $i + 1; ?></td>
                                        <td><?php echo escape($user['name']); ?></td>
                                        <td><?php echo escape($user['email']); ?></td>
                                        <td>
                                            <span class="badge-status <?php echo $user['role'] === 'admin' ? 'badge-admin' : 'badge-user'; ?>">
                                                <?php echo escape($user['role']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo (int)($user['vote_count'] ?? 0); ?></td>
                                        <td><?php echo $user['last_login'] ? escape(date('M j, Y H:i', strtotime($user['last_login']))) : '<span class="text-muted">Never</span>'; ?></td>
                                        <td>
                                            <span class="badge-status <?php echo !empty($user['is_active']) ? 'badge-active' : 'badge-inactive'; ?>">
                                                <?php echo !empty($user['is_active']) ? 'active' : 'inactive'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-primary-custom me-1" onclick="viewUserStats(<?php echo (int)$user['id']; ?>, '<?php echo escape($user['name']); ?>')">
                                                <i class="fas fa-eye me-1"></i>View
                                            </button>
                                            <?php if ((int)$user['id'] !== (int)($_SESSION['user_id'] ?? 0)): ?>
                                            <button class="btn btn-sm btn-danger-custom" onclick="deleteUser(<?php echo (int)$user['id']; ?>, '<?php echo escape($user['name']); ?>')">
                                                <i class="fas fa-trash-alt me-1"></i>Delete
                                            </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8">
                                            <div class="empty-state">
                                                <i class="fas fa-users"></i>
                                                <p>No users found</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- ==================== TAB 4: ACTIVITY LOGS ==================== -->
                <div class="tab-pane fade" id="tab-logs">
                    <div class="filter-bar">
                        <div>
                            <label>Action</label>
                            <select class="form-select" id="filterAction" onchange="loadActivityLogs()">
                                <option value="">All Actions</option>
                                <option value="login_success">Login Success</option>
                                <option value="login_failed">Login Failed</option>
                                <option value="vote_cast">Vote Cast</option>
                                <option value="poll_created">Poll Created</option>
                                <option value="poll_closed">Poll Closed</option>
                                <option value="logout">Logout</option>
                            </select>
                        </div>
                        <div>
                            <label>User</label>
                            <select class="form-select" id="filterUser" onchange="loadActivityLogs()">
                                <option value="">All Users</option>
                                <?php if (!empty($users)): ?>
                                    <?php foreach ($users as $user): ?>
                                    <option value="<?php echo (int)$user['id']; ?>"><?php echo escape($user['name']); ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="ms-auto refresh-indicator" id="logsRefreshIndicator">
                            <i class="fas fa-sync-alt"></i> Auto-refresh: 10s
                        </div>
                    </div>
                    <div id="logsLoading" class="text-center py-4">
                        <div class="spinner-border spinner-sm text-primary" role="status"></div>
                        <span class="ms-2 text-muted" style="font-size:13px">Loading logs...</span>
                    </div>
                    <div class="table-container" id="logsTableWrap" style="display:none">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>IP Address</th>
                                    <th>Device</th>
                                    <th>Browser</th>
                                    <th>OS</th>
                                    <th>Timestamp</th>
                                </tr>
                            </thead>
                            <tbody id="logsTableBody"></tbody>
                        </table>
                    </div>
                </div>

                <!-- ==================== TAB 5: ADD USER ==================== -->
                <div class="tab-pane fade" id="tab-adduser">
                    <div class="row justify-content-center">
                        <div class="col-lg-6">
                            <div class="form-section">
                                <h6 style="font-size:14px;font-weight:700;color:#1a1f2e;margin-bottom:20px">
                                    <i class="fas fa-user-plus me-2" style="color:#2563eb"></i>Create New User
                                </h6>
                                <div class="alert-container" id="addUserAlert"></div>
                                <form id="addUserForm" onsubmit="return createUser(event)">
                                    <div class="mb-3">
                                        <label class="form-label">Name</label>
                                        <input type="text" class="form-control" id="newUserName" name="name" required placeholder="Full name">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" id="newUserEmail" name="email" required placeholder="user@example.com">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Password</label>
                                        <input type="password" class="form-control" id="newUserPassword" name="password" required minlength="6" placeholder="Minimum 6 characters">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Role</label>
                                        <select class="form-select" id="newUserRole" name="role">
                                            <option value="user">User</option>
                                            <option value="admin">Admin</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary-custom" id="addUserBtn" style="padding:8px 24px;font-size:13.5px">
                                        <i class="fas fa-plus me-1"></i> Create User
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- ===== USER STATS MODAL ===== -->
<div class="modal fade" id="userStatsModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content" style="border:none;border-radius:10px;overflow:hidden">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user me-2" style="color:#2563eb"></i>User Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="userStatsBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
(function() {
    'use strict';

    // ===== Utility =====
    function escapeHtml(text) {
        if (text === null || text === undefined) return '';
        var div = document.createElement('div');
        div.textContent = String(text);
        return div.innerHTML;
    }

    function truncate(str, len) {
        if (!str) return '';
        str = String(str);
        return str.length > len ? str.substring(0, len) + '...' : str;
    }

    function formatDate(dateStr) {
        if (!dateStr) return '-';
        var d = new Date(dateStr);
        if (isNaN(d.getTime())) return escapeHtml(dateStr);
        return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
             + ' ' + d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
    }

    function actionBadgeClass(action) {
        var map = {
            'login_success': 'badge-login_success',
            'login_failed': 'badge-login_failed',
            'vote_cast': 'badge-vote_cast',
            'poll_created': 'badge-poll_created',
            'poll_closed': 'badge-poll_closed',
            'logout': 'badge-logout'
        };
        return map[action] || 'badge-default';
    }

    // ===== Clock =====
    function updateClock() {
        var now = new Date();
        var el = document.getElementById('currentTime');
        if (el) el.textContent = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
    }
    updateClock();
    setInterval(updateClock, 30000);

    // ===== VOTES TAB (AJAX) =====
    var votesLoaded = false;

    document.getElementById('votesTabBtn').addEventListener('shown.bs.tab', function() {
        if (!votesLoaded) loadVotes();
    });

    function loadVotes() {
        $('#votesLoading').show();
        $('#votesTableWrap').hide();
        $.getJSON('/api/admin/votes')
            .done(function(data) {
                var votes = data.votes || data.data || data || [];
                if (!Array.isArray(votes)) votes = [];
                var html = '';
                if (votes.length === 0) {
                    html = '<tr><td colspan="9"><div class="empty-state"><i class="fas fa-vote-yea"></i><p>No votes recorded</p></div></td></tr>';
                } else {
                    for (var i = 0; i < votes.length; i++) {
                        var v = votes[i];
                        var isActive = v.is_active === 1 || v.is_active === '1' || v.is_active === true;
                        var statusClass = isActive ? 'badge-active' : 'badge-inactive';
                        var statusText = isActive ? 'active' : 'released';
                        html += '<tr>'
                            + '<td>' + (i + 1) + '</td>'
                            + '<td>' + escapeHtml(v.poll_question || v.poll || '-') + '</td>'
                            + '<td>' + escapeHtml(v.option_text || v.option || '-') + '</td>'
                            + '<td>' + escapeHtml(v.user_name || v.user || '-') + '</td>'
                            + '<td>' + escapeHtml(v.ip_address || '-') + '</td>'
                            + '<td><span class="text-truncate-sm" title="' + escapeHtml(v.user_agent || '') + '">' + escapeHtml(truncate(v.user_agent, 30)) + '</span></td>'
                            + '<td>' + escapeHtml(v.device_type || v.device || '-') + '</td>'
                            + '<td>' + formatDate(v.voted_at || v.created_at) + '</td>'
                            + '<td><span class="badge-status ' + statusClass + '">' + statusText + '</span></td>'
                            + '</tr>';
                    }
                }
                $('#votesTableBody').html(html);
                $('#votesLoading').hide();
                $('#votesTableWrap').show();
                votesLoaded = true;
            })
            .fail(function() {
                $('#votesTableBody').html('<tr><td colspan="9"><div class="empty-state"><i class="fas fa-exclamation-triangle"></i><p>Failed to load votes</p></div></td></tr>');
                $('#votesLoading').hide();
                $('#votesTableWrap').show();
            });
    }

    // ===== ACTIVITY LOGS TAB (AJAX + Auto-refresh) =====
    var logsLoaded = false;
    var logsInterval = null;

    document.getElementById('logsTabBtn').addEventListener('shown.bs.tab', function() {
        loadActivityLogs();
        if (!logsInterval) {
            logsInterval = setInterval(loadActivityLogs, 10000);
        }
    });

    window.loadActivityLogs = function() {
        var action = $('#filterAction').val();
        var userId = $('#filterUser').val();
        var params = {};
        if (action) params.action = action;
        if (userId) params.user_id = userId;

        if (logsLoaded) {
            $('#logsRefreshIndicator').html('<i class="fas fa-sync-alt fa-spin"></i> Refreshing...');
        } else {
            $('#logsLoading').show();
            $('#logsTableWrap').hide();
        }

        $.getJSON('/api/admin/activity-logs', params)
            .done(function(data) {
                var logs = data.logs || data.data || data || [];
                if (!Array.isArray(logs)) logs = [];
                var html = '';
                if (logs.length === 0) {
                    html = '<tr><td colspan="8"><div class="empty-state"><i class="fas fa-file-alt"></i><p>No activity logs found</p></div></td></tr>';
                } else {
                    for (var i = 0; i < logs.length; i++) {
                        var log = logs[i];
                        html += '<tr>'
                            + '<td>' + (i + 1) + '</td>'
                            + '<td>' + escapeHtml(log.user_name || '-') + '</td>'
                            + '<td><span class="badge-action ' + actionBadgeClass(log.action) + '">' + escapeHtml(log.action || '-') + '</span></td>'
                            + '<td>' + escapeHtml(log.ip_address || '-') + '</td>'
                            + '<td>' + escapeHtml(log.device_type || '-') + '</td>'
                            + '<td>' + escapeHtml(log.browser || '-') + '</td>'
                            + '<td>' + escapeHtml(log.os_platform || '-') + '</td>'
                            + '<td>' + formatDate(log.created_at) + '</td>'
                            + '</tr>';
                    }
                }
                $('#logsTableBody').html(html);
                $('#logsLoading').hide();
                $('#logsTableWrap').show();
                logsLoaded = true;
                $('#logsRefreshIndicator').html('<i class="fas fa-sync-alt"></i> Auto-refresh: 10s');
            })
            .fail(function() {
                $('#logsTableBody').html('<tr><td colspan="8"><div class="empty-state"><i class="fas fa-exclamation-triangle"></i><p>Failed to load activity logs</p></div></td></tr>');
                $('#logsLoading').hide();
                $('#logsTableWrap').show();
                $('#logsRefreshIndicator').html('<i class="fas fa-sync-alt"></i> Auto-refresh: 10s');
            });
    };

    // ===== USER STATS MODAL =====
    window.viewUserStats = function(userId, userName) {
        var modal = new bootstrap.Modal(document.getElementById('userStatsModal'));
        $('#userStatsBody').html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>');
        modal.show();

        $.getJSON('/api/admin/user/stats', { user_id: userId })
            .done(function(data) {
                var user = data.user || data;
                var votes = data.votes || [];
                var activity = data.activity_logs || data.activity || [];

                var html = '<div class="user-stat-header">'
                    + '<div class="user-avatar-lg">' + escapeHtml((user.name || userName || '?').charAt(0).toUpperCase()) + '</div>'
                    + '<div class="user-meta">'
                    + '<h6>' + escapeHtml(user.name || userName) + '</h6>'
                    + '<small>' + escapeHtml(user.email || '') + ' &middot; ' + escapeHtml(user.role || '') + '</small>'
                    + '</div></div>';

                // Votes section
                html += '<div class="modal-section-title"><i class="fas fa-vote-yea"></i> Votes</div>';
                if (votes.length > 0) {
                    html += '<div class="table-container"><table class="data-table"><thead><tr><th>Poll</th><th>Option</th><th>IP</th><th>Date</th></tr></thead><tbody>';
                    for (var i = 0; i < votes.length; i++) {
                        var vt = votes[i];
                        html += '<tr>'
                            + '<td>' + escapeHtml(vt.poll_question || vt.poll || '-') + '</td>'
                            + '<td>' + escapeHtml(vt.option_text || vt.option || '-') + '</td>'
                            + '<td>' + escapeHtml(vt.ip_address || '-') + '</td>'
                            + '<td>' + formatDate(vt.voted_at || vt.created_at) + '</td>'
                            + '</tr>';
                    }
                    html += '</tbody></table></div>';
                } else {
                    html += '<p class="text-muted" style="font-size:13px">No votes recorded.</p>';
                }

                // Activity section
                html += '<div class="modal-section-title mt-3"><i class="fas fa-file-alt"></i> Recent Activity</div>';
                if (activity.length > 0) {
                    html += '<div class="table-container"><table class="data-table"><thead><tr><th>Action</th><th>IP</th><th>Device</th><th>Date</th></tr></thead><tbody>';
                    for (var j = 0; j < activity.length; j++) {
                        var act = activity[j];
                        html += '<tr>'
                            + '<td><span class="badge-action ' + actionBadgeClass(act.action) + '">' + escapeHtml(act.action || '-') + '</span></td>'
                            + '<td>' + escapeHtml(act.ip_address || '-') + '</td>'
                            + '<td>' + escapeHtml(act.device_type || '-') + '</td>'
                            + '<td>' + formatDate(act.created_at) + '</td>'
                            + '</tr>';
                    }
                    html += '</tbody></table></div>';
                } else {
                    html += '<p class="text-muted" style="font-size:13px">No recent activity.</p>';
                }

                $('#userStatsBody').html(html);
            })
            .fail(function() {
                $('#userStatsBody').html('<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><p>Failed to load user stats</p></div>');
            });
    };

    // ===== DELETE USER =====
    window.deleteUser = function(userId, userName) {
        if (!confirm('Are you sure you want to delete user "' + userName + '"? This action cannot be undone.')) return;

        $.ajax({
            url: '/api/admin/user/delete',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ user_id: userId }),
            dataType: 'json'
        })
        .done(function(data) {
            if (data.success || data.status === 'success') {
                var row = document.getElementById('user-row-' + userId);
                if (row) row.remove();
                showAlert('#addUserAlert', 'success', 'User deleted successfully.');
            } else {
                alert(data.message || 'Failed to delete user.');
            }
        })
        .fail(function() {
            alert('Request failed. Please try again.');
        });
    };

    // ===== ADD USER =====
    window.createUser = function(e) {
        e.preventDefault();
        var btn = document.getElementById('addUserBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Creating...';

        $.ajax({
            url: '/api/admin/user/create',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                name: $('#newUserName').val().trim(),
                email: $('#newUserEmail').val().trim(),
                password: $('#newUserPassword').val(),
                role: $('#newUserRole').val()
            }),
            dataType: 'json'
        })
        .done(function(data) {
            if (data.success || data.status === 'success') {
                showAlert('#addUserAlert', 'success', 'User created successfully.');
                document.getElementById('addUserForm').reset();
            } else {
                showAlert('#addUserAlert', 'danger', data.message || 'Failed to create user.');
            }
        })
        .fail(function(xhr) {
            var msg = 'Request failed.';
            try { msg = JSON.parse(xhr.responseText).message || msg; } catch(ex) {}
            showAlert('#addUserAlert', 'danger', msg);
        })
        .always(function() {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-plus me-1"></i> Create User';
        });

        return false;
    };

    function showAlert(selector, type, message) {
        var html = '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">'
            + escapeHtml(message)
            + '<button type="button" class="btn-close" data-bs-dismiss="alert" style="font-size:10px;padding:12px"></button>'
            + '</div>';
        $(selector).html(html);
        setTimeout(function() { $(selector + ' .alert').alert('close'); }, 5000);
    }

})();
</script>
</body>
</html>
