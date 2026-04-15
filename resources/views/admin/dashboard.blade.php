<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Poll System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px 0;
        }

        .navbar {
            background: rgba(15, 23, 42, 0.95);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: bold;
            color: #fff !important;
            font-size: 1.5rem;
        }

        .card {
            border: none;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .btn-primary {
            background: #4f46e5;
            border: none;
            border-radius: 8px;
        }

        .btn-primary:hover {
            background: #4338ca;
        }

        .btn-success {
            background: #10b981;
            border: none;
            border-radius: 8px;
        }

        .btn-danger {
            background: #ef4444;
            border: none;
            border-radius: 8px;
        }

        .badge {
            border-radius: 6px;
            padding: 8px 12px;
            font-weight: 600;
        }

        .table {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 8px;
        }

        tbody tr:hover {
            background: rgba(79, 70, 229, 0.05);
        }

        .container {
            max-width: 1200px;
        }

        h1 {
            color: #fff;
            font-weight: bold;
        }

        .tab-content {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 0 0 12px 12px;
            padding: 20px;
        }

        .nav-tabs {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px 12px 0 0;
            border-bottom: 2px solid #e5e7eb;
        }

        .nav-tabs .nav-link {
            color: #666;
            border: none;
        }

        .nav-tabs .nav-link.active {
            color: #4f46e5;
            border-bottom: 3px solid #4f46e5;
            background: transparent;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="/dashboard">🗳️ Real-Time Poll System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/admin/dashboard">Admin Panel</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/polls/create">
                            <i class="fas fa-plus"></i> Create Poll
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle"></i> <?php echo escape($_SESSION['user_name']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/logout">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-5">
        <h1><i class="fas fa-cog"></i> Admin Dashboard</h1>

        <!-- Nav tabs -->
        <ul class="nav nav-tabs mt-4">
            <li class="nav-item">
                <a class="nav-link active" href="#polls" data-bs-toggle="tab">All Polls</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#audit-log" data-bs-toggle="tab">Audit Log</a>
            </li>
        </ul>

        <!-- Tab content -->
        <div class="tab-content">
            <!-- All Polls Tab -->
            <div class="tab-pane fade show active" id="polls">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr class="table-light">
                                <th>ID</th>
                                <th>Question</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="pollsTableBody">
                            <!-- Polls loaded by AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Audit Log Tab -->
            <div class="tab-pane fade" id="audit-log">
                <p class="text-muted">Select a poll to view audit history</p>
                <div id="auditLogContainer"></div>
            </div>
        </div>
    </div>

    <!-- Poll Management Modal -->
    <div class="modal fade" id="managePollModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="managePollTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs mb-3">
                        <li class="nav-item">
                            <a class="nav-link active" href="#voters" data-bs-toggle="tab">Voters</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#history" data-bs-toggle="tab">History</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- Voters Tab -->
                        <div class="tab-pane fade show active" id="voters">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>IP Address</th>
                                            <th>Voted For</th>
                                            <th>Time</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="votersTableBody"></tbody>
                                </table>
                            </div>
                        </div>

                        <!-- History Tab -->
                        <div class="tab-pane fade" id="history">
                            <div id="historyContent"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="togglePollStatus">
                        <label class="form-check-label" for="togglePollStatus">
                            Deactivate Poll
                        </label>
                    </div>
                    <button type="button" class="btn btn-danger" id="deletePollBtn">
                        <i class="fas fa-trash"></i> Delete Poll
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let currentPollId = null;

        $(document).ready(function() {
            loadPolls();
        });

        function loadPolls() {
            $.ajax({
                url: '/api/polls',
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        let html = '';
                        response.polls.forEach(poll => {
                            html += `
                                <tr>
                                    <td>#${poll.id}</td>
                                    <td>${escapeHtml(poll.question)}</td>
                                    <td>
                                        <span class="badge ${poll.status === 'active' ? 'bg-success' : 'bg-danger'}">
                                            ${poll.status}
                                        </span>
                                    </td>
                                    <td>${new Date(poll.created_at).toLocaleDateString()}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="openPollManager(${poll.id})">
                                            <i class="fas fa-edit"></i> Manage
                                        </button>
                                    </td>
                                </tr>
                            `;
                        });
                        $('#pollsTableBody').html(html || '<tr><td colspan="5" class="text-center text-muted">No polls found</td></tr>');
                    }
                }
            });
        }

        function openPollManager(pollId) {
            currentPollId = pollId;
            $.ajax({
                url: `/api/polls/${pollId}`,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        const poll = response.poll;
                        $('#managePollTitle').text(poll.question);
                        loadVoters(pollId);
                        loadVoteHistory(pollId);

                        const modal = new bootstrap.Modal(document.getElementById('managePollModal'));
                        modal.show();

                        // Toggle status
                        $('#togglePollStatus').off().on('change', function() {
                            const newStatus = $(this).is(':checked') ? 'inactive' : 'active';
                            $.ajax({
                                url: '/api/admin/poll/status',
                                method: 'POST',
                                contentType: 'application/json',
                                data: JSON.stringify({ poll_id: pollId, status: newStatus }),
                                success: function(response) {
                                    if (response.success) {
                                        alert('Poll status updated');
                                        loadPolls();
                                    }
                                }
                            });
                        });

                        // Delete poll
                        $('#deletePollBtn').off().on('click', function() {
                            if (confirm('Are you sure? This cannot be undone.')) {
                                $.ajax({
                                    url: '/api/admin/poll/delete',
                                    method: 'POST',
                                    contentType: 'application/json',
                                    data: JSON.stringify({ poll_id: pollId }),
                                    success: function(response) {
                                        if (response.success) {
                                            modal.hide();
                                            loadPolls();
                                            alert('Poll deleted');
                                        }
                                    }
                                });
                            }
                        });
                    }
                }
            });
        }

        function loadVoters(pollId) {
            $.ajax({
                url: `/api/admin/voters?poll_id=${pollId}`,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        let html = '';
                        response.voters.forEach(voter => {
                            html += `
                                <tr>
                                    <td><code>${escapeHtml(voter.ip_address)}</code></td>
                                    <td>${escapeHtml(voter.option_text)}</td>
                                    <td>${new Date(voter.voted_at).toLocaleString()}</td>
                                    <td>
                                        <span class="badge ${voter.is_active ? 'bg-success' : 'bg-warning'}">
                                            ${voter.is_active ? 'Active' : 'Released'}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-danger" onclick="releaseVote(${pollId}, '${voter.ip_address}')">
                                            <i class="fas fa-redo"></i> Release
                                        </button>
                                    </td>
                                </tr>
                            `;
                        });
                        $('#votersTableBody').html(html || '<tr><td colspan="5" class="text-center text-muted">No voters</td></tr>');
                    }
                }
            });
        }

        function releaseVote(pollId, ipAddress) {
            if (confirm(`Release vote from ${ipAddress}?`)) {
                $.ajax({
                    url: '/api/admin/vote/release',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({ poll_id: pollId, ip_address: ipAddress }),
                    success: function(response) {
                        if (response.success) {
                            alert('Vote released. IP can now vote again.');
                            loadVoters(pollId);
                            loadVoteHistory(pollId);
                        } else {
                            alert(response.message);
                        }
                    }
                });
            }
        }

        function loadVoteHistory(pollId) {
            $.ajax({
                url: `/api/admin/vote-history?poll_id=${pollId}`,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        let html = '<div class="timeline">';
                        response.history.forEach(entry => {
                            const actionBadge = {
                                'vote': 'bg-success',
                                'release': 'bg-warning',
                                'revote': 'bg-info'
                            }[entry.action_type] || 'bg-secondary';

                            html += `
                                <div class="mb-3 pb-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <code>${escapeHtml(entry.ip_address)}</code>
                                            <span class="badge ${actionBadge}">${entry.action_type.toUpperCase()}</span>
                                        </div>
                                        <small class="text-muted">${new Date(entry.timestamp).toLocaleString()}</small>
                                    </div>
                                    <p class="mb-0 mt-2">
                                        <small>${entry.option_text ? escapeHtml(entry.option_text) : 'N/A'}</small>
                                    </p>
                                </div>
                            `;
                        });
                        html += '</div>';
                        $('#historyContent').html(html || '<p class="text-muted">No history</p>');
                    }
                }
            });
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
</body>
</html>
