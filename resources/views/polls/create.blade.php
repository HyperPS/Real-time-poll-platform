<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Poll - Poll Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
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
                        <a class="nav-link" href="/dashboard">
                            <i class="fas fa-th-large me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/dashboard">
                            <i class="fas fa-shield-alt me-1"></i> Admin Panel
                        </a>
                    </li>
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

    <!-- Main Content -->
    <div class="container" style="max-width: 640px; padding-top: 32px;">
        <div class="card">
            <div class="card-body" style="padding: 28px;">
                <h4 class="fw-bold mb-4" style="font-size: 1.15rem;">
                    <i class="fas fa-plus-circle me-2" style="color: #2563eb;"></i>Create New Poll
                </h4>

                <form method="POST" action="/polls/store" id="createPollForm">
                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

                    <div class="mb-4">
                        <label for="question" class="form-label">Poll Question</label>
                        <input
                            type="text"
                            class="form-control"
                            id="question"
                            name="question"
                            placeholder="What would you like to ask?"
                            required
                        >
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Options</label>
                        <div id="optionsContainer"></div>
                        <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="addOptionBtn">
                            <i class="fas fa-plus me-1"></i> Add Option
                        </button>
                    </div>

                    <div class="d-flex gap-2 pt-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="fas fa-check me-1"></i> Create Poll
                        </button>
                        <a href="/admin/dashboard" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let optionCount = 0;

        $(document).ready(function() {
            addOption();
            addOption();

            $('#addOptionBtn').click(function() {
                addOption();
            });
        });

        function addOption() {
            optionCount++;
            const html = '<div class="input-group mb-2" id="option-' + optionCount + '">'
                + '<span class="input-group-text" style="font-size: 0.85rem; font-weight: 600; color: #6b7280; background: #f9fafb; border-color: #e5e7eb;">' + optionCount + '</span>'
                + '<input type="text" class="form-control" name="options[]" placeholder="Enter option text" required>'
                + '<button type="button" class="btn btn-outline-secondary" onclick="removeOption(' + optionCount + ')" style="border-color: #e5e7eb;">'
                + '<i class="fas fa-times" style="color: #dc2626;"></i>'
                + '</button>'
                + '</div>';
            $('#optionsContainer').append(html);
        }

        function removeOption(id) {
            if ($('#optionsContainer .input-group').length <= 2) {
                alert('At least 2 options are required.');
                return;
            }
            $('#option-' + id).remove();
        }
    </script>
</body>
</html>
