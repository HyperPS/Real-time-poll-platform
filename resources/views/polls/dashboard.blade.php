<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Poll System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4f46e5;
            --secondary: #10b981;
            --danger: #ef4444;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px 0;
        }

        .navbar {
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: bold;
            color: #fff !important;
            font-size: 1.5rem;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: #fff !important;
        }

        .card {
            border: none;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            cursor: pointer;
            transform-style: preserve-3d;
        }

        .card:hover {
            transform: translateY(-8px) rotateX(3deg);
            box-shadow: 0 16px 48px rgba(79, 70, 229, 0.3);
        }

        .card-body {
            padding: 24px;
        }

        .btn-primary {
            background: var(--primary);
            border: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: #4338ca;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
            color: #fff;
        }

        .btn-success {
            background: var(--secondary);
            border: none;
            border-radius: 8px;
            font-weight: 600;
        }

        .btn-success:hover {
            background: #059669;
            transform: translateY(-2px);
            color: #fff;
        }

        .alert {
            border-radius: 8px;
            border: none;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .badge {
            border-radius: 6px;
            padding: 8px 12px;
            font-weight: 600;
        }

        .badge-primary {
            background: var(--primary);
        }

        .badge-success {
            background: var(--secondary);
        }

        .container {
            max-width: 1200px;
        }

        h1 {
            color: #fff;
            font-weight: bold;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .poll-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 24px;
            margin-top: 30px;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #fff;
        }

        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.7;
        }

        .empty-state p {
            font-size: 1.1rem;
        }

        footer {
            margin-top: 60px;
            padding: 20px;
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
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
                        <a class="nav-link active" href="/dashboard">Dashboard</a>
                    </li>
                    <?php if ($_SESSION['user_role'] === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/dashboard">Admin Panel</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/polls/create">
                            <i class="fas fa-plus"></i> Create Poll
                        </a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle"></i> <?php echo escape($_SESSION['user_name']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Messages -->
    <div class="container mt-4">
        <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> <?php echo escape($_SESSION['error']); unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <?php echo escape($_SESSION['success']); unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
    </div>

    <!-- Main Content -->
    <div class="container">
        <h1>Available Polls</h1>
        <p class="text-light mt-2">Select a poll below to vote</p>

        <div class="poll-grid" id="pollsContainer">
            <!-- Polls will be loaded here by AJAX -->
        </div>

        <div id="emptyState" class="empty-state">
            <i class="fas fa-inbox"></i>
            <p>No active polls available at the moment</p>
        </div>
    </div>

    <!-- Poll modal -->
    <div class="modal fade" id="pollModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content rounded-3" style="border: none; box-shadow: 0 12px 48px rgba(0, 0, 0, 0.2);">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="pollTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="pollContent">
                    <!-- Poll details loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2026 Real-Time Poll Platform. Built with Laravel & AJAX.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="/public/js/poll-voting.js"></script>
    <script>
        $(document).ready(function() {
            loadPolls();
        });

        function loadPolls() {
            $.ajax({
                url: '/api/polls',
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    if (response.success && response.polls.length > 0) {
                        let html = '';
                        response.polls.forEach(poll => {
                            html += `
                                <div class="card poll-card" data-poll-id="${poll.id}">
                                    <div class="card-body">
                                        <h5 class="card-title">${escapeHtml(poll.question)}</h5>
                                        <p class="text-muted small">
                                            <i class="fas fa-calendar"></i> ${new Date(poll.created_at).toLocaleDateString()}
                                        </p>
                                        <button class="btn btn-primary w-100 vote-btn" data-poll-id="${poll.id}">
                                            <i class="fas fa-check-circle"></i> Vote Now
                                        </button>
                                    </div>
                                </div>
                            `;
                        });
                        $('#pollsContainer').html(html);
                        $('#emptyState').hide();

                        // Add click handlers
                        $('.vote-btn').click(function(e) {
                            e.stopPropagation();
                            const pollId = $(this).data('poll-id');
                            openPollModal(pollId);
                        });
                    } else {
                        $('#pollsContainer').html('');
                        $('#emptyState').show();
                    }
                },
                error: function() {
                    alert('Failed to load polls');
                }
            });
        }

        function openPollModal(pollId) {
            $.ajax({
                url: `/api/polls/${pollId}`,
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    if (response.success) {
                        const poll = response.poll;
                        $('#pollTitle').text(poll.question);

                        let html = '<div class="voting-container">';
                        poll.options.forEach(option => {
                            html += `
                                <div class="form-check mb-3">
                                    <input class="form-check-input option-radio" type="radio" name="option" 
                                           value="${option.id}" id="option_${option.id}">
                                    <label class="form-check-label flex-grow-1" for="option_${option.id}">
                                        ${escapeHtml(option.option_text)}
                                    </label>
                                </div>
                            `;
                        });

                        html += `
                            <div class="d-flex gap-2 mt-4">
                                <button type="button" class="btn btn-success flex-grow-1" id="submitVoteBtn">
                                    <i class="fas fa-vote-yea"></i> Submit Vote
                                </button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            </div>
                        `;
                        html += `
                            <div id="resultsContainer" style="display: none; margin-top: 20px;">
                                <h6>Live Results:</h6>
                                <div id="pollResults"></div>
                            </div>
                        </div>`;

                        $('#pollContent').html(html);

                        // Auto-refresh results
                        let resultInterval = setInterval(() => {
                            updatePollResults(pollId);
                        }, 1000);

                        // Submit vote
                        $('#submitVoteBtn').click(function() {
                            const optionId = $('input[name="option"]:checked').val();
                            if (!optionId) {
                                alert('Please select an option');
                                return;
                            }

                            $.ajax({
                                url: '/api/vote/cast',
                                method: 'POST',
                                contentType: 'application/json',
                                data: JSON.stringify({
                                    poll_id: pollId,
                                    option_id: optionId
                                }),
                                success: function(response) {
                                    if (response.success) {
                                        $('#resultsContainer').show();
                                        $('.option-radio').prop('disabled', true);
                                        $('#submitVoteBtn').prop('disabled', true).html('<i class="fas fa-check"></i> Vote Recorded!');
                                        updatePollResults(pollId);
                                    } else {
                                        alert(response.message || 'Failed to cast vote');
                                    }
                                },
                                error: function() {
                                    alert('Error casting vote');
                                }
                            });
                        });

                        // Close modal handler
                        $('#pollModal').on('hidden.bs.modal', function() {
                            clearInterval(resultInterval);
                        });

                        const modal = new bootstrap.Modal(document.getElementById('pollModal'));
                        modal.show();
                    }
                },
                error: function() {
                    alert('Failed to load poll');
                }
            });
        }

        function updatePollResults(pollId) {
            $.ajax({
                url: `/api/results?poll_id=${pollId}`,
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    if (response.success) {
                        let html = '';
                        response.options.forEach(option => {
                            const percentage = option.percentage || 0;
                            html += `
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="fw-500">${escapeHtml(option.option_text)}</span>
                                        <span class="badge bg-primary">${option.vote_count} votes</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: ${percentage}%">${percentage.toFixed(1)}%</div>
                                    </div>
                                </div>
                            `;
                        });
                        html += `<p class="text-muted small mt-3">Total Votes: ${response.total_votes}</p>`;
                        $('#pollResults').html(html);
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
