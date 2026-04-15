<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Poll - Poll Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <style>
        .page-header {
            padding: 28px 0 20px;
        }
        .page-header h1 {
            font-size: 1.35rem;
            font-weight: 700;
            color: #1a1f2e;
            margin-bottom: 4px;
        }
        .poll-info-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }
        .tab-wrapper .nav-tabs {
            background: #fff;
            border-radius: 8px 8px 0 0;
            border: 1px solid #e5e7eb;
            border-bottom: none;
            padding: 0 12px;
        }
        .tab-wrapper .tab-content {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-top: none;
            border-radius: 0 0 8px 8px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .data-table {
            width: 100%;
            font-size: 0.85rem;
        }
        .data-table thead th {
            background: #f9fafb;
            color: #475569;
            font-weight: 600;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 10px 14px;
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap;
        }
        .data-table tbody td {
            padding: 10px 14px;
            color: #334155;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }
        .data-table tbody tr:hover {
            background: #f9fafb;
        }
        .data-table tbody tr:last-child td {
            border-bottom: none;
        }
        .badge-status {
            display: inline-block;
            padding: 3px 8px;
            font-size: 0.7rem;
            font-weight: 600;
            border-radius: 4px;
        }
        .badge-active { background: rgba(5,150,105,0.1); color: #059669; }
        .badge-released { background: rgba(217,119,6,0.1); color: #d97706; }
        .result-item { margin-bottom: 14px; }
        .result-item .result-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 4px;
        }
        .result-item .result-label {
            font-size: 0.85rem;
            font-weight: 500;
            color: #374151;
        }
        .result-item .result-count {
            font-size: 0.75rem;
            font-weight: 600;
            color: #6b7280;
        }
        .history-entry {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 12px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .history-entry:last-child { border-bottom: none; }
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #9ca3af;
        }
        .empty-state i {
            font-size: 32px;
            margin-bottom: 10px;
            display: block;
            color: #d1d5db;
        }
        .empty-state p {
            font-size: 0.85rem;
            margin: 0;
        }
        .container { max-width: 1000px; }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="/dashboard">
                <i class="fas fa-chart-bar"></i> Poll Platform
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/dashboard"><i class="fas fa-th-large me-1"></i> Dashboard</a>
                <a class="nav-link" href="/admin/dashboard"><i class="fas fa-shield-alt me-1"></i> Admin Panel</a>
                <a class="nav-link" href="/polls/create"><i class="fas fa-plus me-1"></i> Create Poll</a>
                <a class="nav-link" href="/logout">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h1><i class="fas fa-cog me-2" style="color: #2563eb;"></i>Manage Poll</h1>
                    <p class="text-muted mb-0" style="font-size: 0.9rem;"><?php echo escape($poll['question']); ?></p>
                </div>
                <a href="/admin/dashboard" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>

        <!-- Poll Info Card -->
        <div class="poll-info-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-1" style="font-size: 1rem;"><?php echo escape($poll['question']); ?></h5>
                    <span class="badge bg-<?php echo $poll['status'] === 'active' ? 'success' : 'danger'; ?>">
                        <?php echo ucfirst($poll['status']); ?>
                    </span>
                    <span class="text-muted ms-2" style="font-size: 0.8rem;">
                        Created <?php echo date('M j, Y', strtotime($poll['created_at'])); ?>
                    </span>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-warning btn-sm" id="toggleStatusBtn">
                        <i class="fas fa-power-off me-1"></i>
                        <?php echo $poll['status'] === 'active' ? 'Deactivate' : 'Activate'; ?>
                    </button>
                    <button class="btn btn-danger btn-sm" id="deletePollBtn">
                        <i class="fas fa-trash me-1"></i> Delete
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="tab-wrapper">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" href="#voters-tab" data-bs-toggle="tab">
                        <i class="fas fa-users me-1"></i> Voters
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#history-tab" data-bs-toggle="tab">
                        <i class="fas fa-history me-1"></i> Vote History
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#results-tab" data-bs-toggle="tab">
                        <i class="fas fa-chart-bar me-1"></i> Live Results
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Voters Tab -->
                <div class="tab-pane fade show active" id="voters-tab">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>IP Address</th>
                                    <th>Voted For</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="votersTableBody">
                                <?php if (!empty($voters)): ?>
                                    <?php foreach ($voters as $voter): ?>
                                    <tr>
                                        <td><code style="font-size: 0.8rem;"><?php echo escape($voter['ip_address']); ?></code></td>
                                        <td><?php echo escape($voter['option_text'] ?? 'N/A'); ?></td>
                                        <td><?php echo isset($voter['voted_at']) ? date('M j, Y g:i A', strtotime($voter['voted_at'])) : 'N/A'; ?></td>
                                        <td>
                                            <span class="badge-status <?php echo !empty($voter['is_active']) ? 'badge-active' : 'badge-released'; ?>">
                                                <?php echo !empty($voter['is_active']) ? 'Active' : 'Released'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (!empty($voter['is_active'])): ?>
                                            <button class="btn btn-sm btn-danger release-btn"
                                                    data-ip="<?php echo escape($voter['ip_address']); ?>">
                                                <i class="fas fa-redo me-1"></i> Release
                                            </button>
                                            <?php else: ?>
                                            <span class="text-muted" style="font-size: 0.8rem;">Released</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="5" class="text-center text-muted py-4">No voters yet</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- History Tab -->
                <div class="tab-pane fade" id="history-tab">
                    <div id="historyContent">
                        <div class="text-center py-4">
                            <div class="spinner-border spinner-border-sm text-primary"></div>
                            <span class="ms-2 text-muted" style="font-size: 0.85rem;">Loading history...</span>
                        </div>
                    </div>
                </div>

                <!-- Results Tab -->
                <div class="tab-pane fade" id="results-tab">
                    <div id="liveResults">
                        <div class="text-center py-4">
                            <div class="spinner-border spinner-border-sm text-primary"></div>
                            <span class="ms-2 text-muted" style="font-size: 0.85rem;">Loading results...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center mt-5 pb-4">
        <small class="text-muted">&copy; 2026 Poll Platform</small>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        const pollId = <?php echo intval($poll['id']); ?>;
        const currentStatus = '<?php echo $poll['status']; ?>';

        function escapeHtml(text) {
            if (!text) return 'N/A';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Release vote
        $(document).on('click', '.release-btn', function() {
            const ip = $(this).data('ip');
            if (!confirm('Release vote from ' + ip + '? This will allow them to vote again.')) return;

            const btn = $(this);
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

            $.ajax({
                url: '/api/admin/vote/release',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ poll_id: pollId, ip_address: ip }),
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.message || 'Failed to release vote');
                        btn.prop('disabled', false).html('<i class="fas fa-redo me-1"></i> Release');
                    }
                },
                error: function() {
                    alert('Error releasing vote');
                    btn.prop('disabled', false).html('<i class="fas fa-redo me-1"></i> Release');
                }
            });
        });

        // Toggle status
        $('#toggleStatusBtn').click(function() {
            const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
            if (!confirm((newStatus === 'inactive' ? 'Deactivate' : 'Activate') + ' this poll?')) return;

            $.ajax({
                url: '/api/admin/poll/status',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ poll_id: pollId, status: newStatus }),
                success: function(response) {
                    if (response.success) location.reload();
                    else alert('Failed to update status');
                }
            });
        });

        // Delete poll
        $('#deletePollBtn').click(function() {
            if (!confirm('Are you sure you want to DELETE this poll? This cannot be undone.')) return;
            $.ajax({
                url: '/api/admin/poll/delete',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ poll_id: pollId }),
                success: function(response) {
                    if (response.success) {
                        alert('Poll deleted');
                        window.location.href = '/admin/dashboard';
                    } else {
                        alert('Failed to delete poll');
                    }
                }
            });
        });

        // Load vote history
        function loadHistory() {
            $.ajax({
                url: '/api/admin/vote-history?poll_id=' + pollId,
                method: 'GET',
                success: function(response) {
                    if (response.success && response.history.length > 0) {
                        let html = '';
                        response.history.forEach(function(entry) {
                            const badgeMap = { 'vote': 'bg-success', 'release': 'bg-warning', 'revote': 'bg-info' };
                            const badge = badgeMap[entry.action_type] || 'bg-secondary';
                            html += '<div class="history-entry">'
                                + '<div>'
                                + '<code style="font-size: 0.8rem;">' + escapeHtml(entry.ip_address) + '</code>'
                                + ' <span class="badge ' + badge + ' ms-1" style="font-size: 0.7rem;">' + escapeHtml(entry.action_type).toUpperCase() + '</span>'
                                + '<p class="mb-0 mt-1 text-muted" style="font-size: 0.8rem;">' + (entry.option_text ? escapeHtml(entry.option_text) : 'N/A') + '</p>'
                                + '</div>'
                                + '<small class="text-muted">' + new Date(entry.timestamp).toLocaleString() + '</small>'
                                + '</div>';
                        });
                        $('#historyContent').html(html);
                    } else {
                        $('#historyContent').html('<div class="empty-state"><i class="fas fa-history"></i><p>No history yet</p></div>');
                    }
                }
            });
        }

        // Load live results
        function loadResults() {
            $.ajax({
                url: '/api/results?poll_id=' + pollId,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        let html = '';
                        response.options.forEach(function(option) {
                            const pct = (option.percentage || 0).toFixed(1);
                            html += '<div class="result-item">'
                                + '<div class="result-header">'
                                + '<span class="result-label">' + escapeHtml(option.option_text) + '</span>'
                                + '<span class="result-count">' + option.vote_count + ' votes (' + pct + '%)</span>'
                                + '</div>'
                                + '<div class="progress" style="height: 8px;"><div class="progress-bar" style="width: ' + pct + '%"></div></div>'
                                + '</div>';
                        });
                        html += '<p class="text-muted mt-2" style="font-size: 0.8rem;">Total: <strong>' + response.total_votes + '</strong> votes</p>';
                        $('#liveResults').html(html);
                    }
                }
            });
        }

        $(document).ready(function() {
            loadHistory();
            loadResults();
            setInterval(loadResults, 2000);
        });
    </script>
</body>
</html>
