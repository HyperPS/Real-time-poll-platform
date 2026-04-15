<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Poll - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 30px 0;
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

        .card {
            border: none;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background: #4f46e5;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: #4338ca;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
        }

        .btn-danger {
            background: #ef4444;
            border: none;
            border-radius: 8px;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .form-control, .form-label {
            border-radius: 8px;
        }

        .form-control:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .container {
            max-width: 700px;
        }

        h1 {
            color: #fff;
            font-weight: bold;
        }

        .form-label {
            color: #333;
            font-weight: 600;
        }

        .option-item {
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
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
                        <a class="nav-link" href="/admin/dashboard">Admin Panel</a>
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
        <div class="card">
            <div class="card-body p-4">
                <h3 class="card-title mb-4">
                    <i class="fas fa-poll"></i> Create New Poll
                </h3>

                <form method="POST" action="/polls/store">
                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

                    <div class="mb-4">
                        <label for="question" class="form-label">Poll Question</label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="question" 
                            name="question"
                            placeholder="Enter your poll question"
                            required
                        >
                    </div>

                    <div class="mb-4">
                        <label class="form-label mb-3">Poll Options</label>
                        <div id="optionsContainer"></div>
                        <button type="button" class="btn btn-outline-primary mt-3" id="addOptionBtn">
                            <i class="fas fa-plus"></i> Add Option
                        </button>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary py-2">
                            <i class="fas fa-check-circle"></i> Create Poll
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
            // Add initial options
            addOption();
            addOption();

            $('#addOptionBtn').click(function() {
                addOption();
            });
        });

        function addOption() {
            optionCount++;
            const html = `
                <div class="input-group mb-2 option-item" id="option-${optionCount}">
                    <span class="input-group-text">${optionCount}</span>
                    <input type="text" class="form-control" name="options[]" placeholder="Enter option text" required>
                    <button type="button" class="btn btn-danger" onclick="removeOption(${optionCount})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
            $('#optionsContainer').append(html);
        }

        function removeOption(id) {
            $('#option-' + id).remove();
        }
    </script>
</body>
</html>
