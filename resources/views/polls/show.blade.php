<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo escape($poll['question']); ?> - Poll Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <style>
        .poll-container { max-width: 720px; margin: 0 auto; }
        .poll-header {
            padding: 28px 0 20px;
        }
        .poll-header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1a1f2e;
            margin-bottom: 8px;
        }
        .poll-header .poll-meta {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.85rem;
            color: #6b7280;
        }
        .option-card {
            background: #fff;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 14px 18px;
            cursor: pointer;
            transition: border-color 0.2s ease, background 0.2s ease;
            margin-bottom: 10px;
        }
        .option-card:hover {
            border-color: #2563eb;
            background: rgba(37, 99, 235, 0.03);
        }
        .option-card.selected {
            border-color: #2563eb;
            background: rgba(37, 99, 235, 0.05);
        }
        .option-card input[type="radio"] { display: none; }
        .option-label {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            width: 100%;
            margin: 0;
        }
        .radio-circle {
            width: 20px;
            height: 20px;
            border: 2px solid #d1d5db;
            border-radius: 50%;
            position: relative;
            flex-shrink: 0;
            transition: border-color 0.2s ease;
        }
        .option-card.selected .radio-circle {
            border-color: #2563eb;
        }
        .option-card.selected .radio-circle::after {
            content: '';
            position: absolute;
            top: 3px;
            left: 3px;
            width: 10px;
            height: 10px;
            background: #2563eb;
            border-radius: 50%;
        }
        .btn-vote {
            background: #2563eb;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            padding: 12px 24px;
            font-size: 0.95rem;
            color: #fff;
            transition: all 0.2s ease;
            width: 100%;
        }
        .btn-vote:hover {
            background: #1d4ed8;
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }
        .btn-vote:disabled {
            background: #059669;
            opacity: 1;
            transform: none;
            box-shadow: none;
        }
        .results-section {
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
        .results-section h6 {
            font-weight: 700;
            font-size: 0.9rem;
            color: #1f2937;
            margin-bottom: 16px;
        }
        .result-item {
            margin-bottom: 14px;
        }
        .result-item .result-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 4px;
        }
        .result-item .result-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
        }
        .result-item .result-count {
            font-size: 0.75rem;
            font-weight: 600;
            color: #6b7280;
        }
        .progress {
            height: 8px;
            border-radius: 4px;
            background: #e5e7eb;
        }
        .progress-bar {
            background: linear-gradient(90deg, #2563eb, #3b82f6);
            border-radius: 4px;
            transition: width 0.6s ease;
        }
        .live-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(5, 150, 105, 0.08);
            color: #059669;
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .live-badge::before {
            content: '';
            width: 6px;
            height: 6px;
            background: #059669;
            border-radius: 50%;
            animation: livePulse 1.5s infinite;
        }
        @keyframes livePulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
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
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/dashboard"><i class="fas fa-arrow-left me-1"></i> Back to Dashboard</a>
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <a class="nav-link" href="/admin/dashboard">Admin Panel</a>
                <?php endif; ?>
                <a class="nav-link" href="/logout">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <div class="poll-container">
            <div class="poll-header">
                <h1><?php echo escape($poll['question']); ?></h1>
                <div class="poll-meta">
                    <span class="badge bg-<?php echo $poll['status'] === 'active' ? 'success' : 'danger'; ?>">
                        <?php echo ucfirst($poll['status']); ?>
                    </span>
                    <span>
                        <i class="fas fa-calendar-alt me-1"></i>
                        <?php echo date('M j, Y', strtotime($poll['created_at'])); ?>
                    </span>
                </div>
            </div>

            <div class="card">
                <div class="card-body" style="padding: 24px;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0 fw-bold" style="font-size: 1rem;">Cast Your Vote</h5>
                        <span class="live-badge">LIVE</span>
                    </div>

                    <!-- Voting Options -->
                    <div id="votingOptions">
                        <?php if (!empty($poll['options'])): ?>
                            <?php foreach ($poll['options'] as $option): ?>
                            <div class="option-card" onclick="selectOption(this, <?php echo $option['id']; ?>)">
                                <label class="option-label">
                                    <input type="radio" name="vote_option" value="<?php echo $option['id']; ?>">
                                    <span class="radio-circle"></span>
                                    <span style="font-weight: 500;"><?php echo escape($option['option_text']); ?></span>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <button type="button" class="btn btn-vote mt-3" id="submitVoteBtn" onclick="castVoteAction()">
                        <i class="fas fa-vote-yea me-1"></i> Submit Vote
                    </button>

                    <!-- Results Section -->
                    <div class="results-section" id="resultsSection">
                        <h6>
                            <i class="fas fa-chart-bar me-1"></i> Live Results
                            <span class="live-badge ms-2">UPDATING</span>
                        </h6>
                        <div id="pollResults">
                            <div class="text-center py-3">
                                <div class="spinner-border spinner-border-sm text-primary"></div>
                                <span class="ms-2 text-muted" style="font-size: 0.85rem;">Loading results...</span>
                            </div>
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
        let selectedOptionId = null;
        let hasVoted = false;
        let resultInterval = null;

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function selectOption(card, optionId) {
            if (hasVoted) return;
            document.querySelectorAll('.option-card').forEach(c => c.classList.remove('selected'));
            card.classList.add('selected');
            card.querySelector('input[type="radio"]').checked = true;
            selectedOptionId = optionId;
        }

        function castVoteAction() {
            if (!selectedOptionId) {
                alert('Please select an option first');
                return;
            }
            const btn = document.getElementById('submitVoteBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';

            $.ajax({
                url: '/api/vote/cast',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ poll_id: pollId, option_id: selectedOptionId }),
                success: function(response) {
                    if (response.success) {
                        hasVoted = true;
                        btn.innerHTML = '<i class="fas fa-check-circle"></i> Vote Recorded';
                        btn.style.background = '#059669';
                        document.querySelectorAll('.option-card').forEach(c => {
                            c.style.pointerEvents = 'none';
                            c.style.opacity = '0.7';
                        });
                        loadResults();
                    } else {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-vote-yea me-1"></i> Submit Vote';
                        alert(response.message || 'Failed to cast vote');
                    }
                },
                error: function(xhr) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-vote-yea me-1"></i> Submit Vote';
                    const resp = xhr.responseJSON;
                    alert(resp?.message || 'Error casting vote');
                }
            });
        }

        function loadResults() {
            $.ajax({
                url: '/api/results?poll_id=' + pollId,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        let html = '';
                        response.options.forEach(option => {
                            const pct = (option.percentage || 0).toFixed(1);
                            html += '<div class="result-item">'
                                + '<div class="result-header">'
                                + '<span class="result-label">' + escapeHtml(option.option_text) + '</span>'
                                + '<span class="result-count">' + option.vote_count + ' vote' + (option.vote_count !== 1 ? 's' : '') + ' (' + pct + '%)</span>'
                                + '</div>'
                                + '<div class="progress"><div class="progress-bar" style="width: ' + pct + '%"></div></div>'
                                + '</div>';
                        });
                        html += '<p class="text-muted mt-2" style="font-size: 0.8rem;"><i class="fas fa-chart-pie me-1"></i> Total: <strong>' + response.total_votes + '</strong> votes</p>';
                        $('#pollResults').html(html);
                    }
                }
            });
        }

        function checkVoteStatus() {
            $.ajax({
                url: '/api/vote/status?poll_id=' + pollId,
                method: 'GET',
                success: function(response) {
                    if (response.hasVoted) {
                        hasVoted = true;
                        const btn = document.getElementById('submitVoteBtn');
                        btn.disabled = true;
                        btn.innerHTML = '<i class="fas fa-check-circle"></i> Already Voted';
                        btn.style.background = '#059669';
                        if (response.optionId) {
                            const radio = document.querySelector('input[value="' + response.optionId + '"]');
                            if (radio) {
                                radio.closest('.option-card').classList.add('selected');
                                radio.checked = true;
                            }
                        }
                        document.querySelectorAll('.option-card').forEach(c => {
                            c.style.pointerEvents = 'none';
                        });
                    }
                }
            });
        }

        $(document).ready(function() {
            checkVoteStatus();
            loadResults();
            resultInterval = setInterval(loadResults, 2000);
        });

        window.addEventListener('beforeunload', () => {
            if (resultInterval) clearInterval(resultInterval);
        });
    </script>
</body>
</html>
