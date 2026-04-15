<?php
/**
 * Installation Status Checker
 * Verifies that all components are properly configured
 * Access via: http://localhost/Mohit/public/check-status.php
 */

// Get project root
$projectRoot = dirname(dirname(__DIR__));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Status Check - Real-Time Poll Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 30px;
            max-width: 800px;
        }
        .status-section {
            margin-bottom: 25px;
        }
        .status-item {
            display: flex;
            align-items: center;
            padding: 15px;
            margin-bottom: 10px;
            background: #f8f9fa;
            border-radius: 5px;
            border-left: 4px solid #dee2e6;
        }
        .status-item.success {
            background: #d4edda;
            border-left-color: #28a745;
        }
        .status-item.warning {
            background: #fff3cd;
            border-left-color: #ffc107;
        }
        .status-item.error {
            background: #f8d7da;
            border-left-color: #dc3545;
        }
        .status-icon {
            font-size: 24px;
            margin-right: 15px;
            min-width: 30px;
        }
        .status-text h6 {
            margin: 0;
            font-weight: 600;
        }
        .status-text p {
            margin: 5px 0 0 0;
            font-size: 14px;
            color: #666;
        }
        .section-header {
            font-size: 20px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }
        .btn-group-custom {
            margin-top: 20px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .quick-links {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .localhost-url {
            background: #e7f3ff;
            border: 2px solid #0066cc;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            text-align: center;
        }
        .localhost-url a {
            font-size: 18px;
            font-weight: bold;
            color: #0066cc;
            text-decoration: none;
        }
        .localhost-url a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: #667eea; margin-bottom: 10px;">Real-Time Poll Platform</h1>
            <h4 style="color: #666;">Installation Status Checker</h4>
        </div>

        <?php
        // Check PHP Version
        echo '<div class="status-section">';
        echo '<div class="section-header">PHP Environment</div>';
        
        $phpVersion = phpversion();
        $requiredVersion = '8.0.0';
        if (version_compare($phpVersion, $requiredVersion) >= 0) {
            echo '<div class="status-item success">';
            echo '<span class="status-icon">OK</span>';
            echo '<div class="status-text"><h6>PHP Version</h6><p>PHP ' . $phpVersion . ' (Required: 8.0+)</p></div>';
            echo '</div>';
        } else {
            echo '<div class="status-item error">';
            echo '<span class="status-icon">ERROR</span>';
            echo '<div class="status-text"><h6>PHP Version</h6><p>PHP ' . $phpVersion . ' (Required: 8.0+)</p></div>';
            echo '</div>';
        }
        
        // Check PDO Extension
        echo '<div class="status-item ' . (extension_loaded('pdo') ? 'success' : 'error') . '">';
        echo '<span class="status-icon">' . (extension_loaded('pdo') ? 'OK' : 'X') . '</span>';
        echo '<div class="status-text"><h6>PDO Extension</h6><p>' . (extension_loaded('pdo') ? 'Installed' : 'Missing - Required for database') . '</p></div>';
        echo '</div>';
        
        // Check PDO MySQL
        echo '<div class="status-item ' . (extension_loaded('pdo_mysql') ? 'success' : 'error') . '">';
        echo '<span class="status-icon">' . (extension_loaded('pdo_mysql') ? 'OK' : 'X') . '</span>';
        echo '<div class="status-text"><h6>PDO MySQL</h6><p>' . (extension_loaded('pdo_mysql') ? 'Installed' : 'Missing - Required for MySQL') . '</p></div>';
        echo '</div>';
        
        echo '</div>';
        
        // Check File Structure
        echo '<div class="status-section">';
        echo '<div class="section-header">Project Files</div>';
        
        $requiredFiles = [
            'bootstrap/app.php' => 'Bootstrap Configuration',
            'routes/web.php' => 'Route Configuration',
            'app/Core/VotingEngine.php' => 'Core Voting Logic',
            'app/Models/User.php' => 'User Model',
            'database/schema.sql' => 'Database Schema',
            'public/index.php' => 'Application Entry Point',
            '.htaccess' => '.htaccess Configuration'
        ];
        
        foreach ($requiredFiles as $file => $description) {
            $filepath = $projectRoot . '/' . $file;
            $exists = file_exists($filepath);
            echo '<div class="status-item ' . ($exists ? 'success' : 'error') . '">';
            echo '<span class="status-icon">' . ($exists ? 'OK' : 'X') . '</span>';
            echo '<div class="status-text"><h6>' . $description . '</h6><p>' . $file . '</p></div>';
            echo '</div>';
        }
        
        echo '</div>';
        
        // Check Database Configuration
        echo '<div class="status-section">';
        echo '<div class="section-header">Database Configuration</div>';
        
        $appPath = $projectRoot . '/bootstrap/app.php';
        if (file_exists($appPath)) {
            $content = file_get_contents($appPath);
            preg_match("/define\('DB_HOST',\s*'([^']+)'\)/", $content, $host);
            preg_match("/define\('DB_NAME',\s*'([^']+)'\)/", $content, $name);
            preg_match("/define\('DB_USER',\s*'([^']+)'\)/", $content, $user);
            
            if (!empty($host) && !empty($name) && !empty($user)) {
                echo '<div class="status-item success">';
                echo '<span class="status-icon">OK</span>';
                echo '<div class="status-text">';
                echo '<h6>Configuration Found</h6>';
                echo '<p>Host: ' . $host[1] . ' | Database: ' . $name[1] . ' | User: ' . $user[1] . '</p>';
                echo '</div>';
                echo '</div>';
            }
        }
        
        echo '</div>';
        
        // Test Database Connection
        echo '<div class="status-section">';
        echo '<div class="section-header">Database Connection Test</div>';
        
        if (extension_loaded('pdo_mysql')) {
            try {
                require $projectRoot . '/bootstrap/app.php';
                
                // Check if connection was successful
                if (isset($pdo) && $pdo instanceof PDO) {
                    $result = $pdo->query("SELECT COUNT(*) FROM polls");
                    $pollCount = $result ? $result->fetchColumn() : null;
                    
                    echo '<div class="status-item success">';
                    echo '<span class="status-icon">OK</span>';
                    echo '<div class="status-text">';
                    echo '<h6>Database Connected</h6>';
                    echo '<p>Successfully connected to polling_system database</p>';
                    if ($pollCount !== null) {
                        echo '<p style="margin-top: 5px;"><strong>Sample Data:</strong> ' . $pollCount . ' polls in database</p>';
                    }
                    echo '</div>';
                    echo '</div>';
                }
            } catch (Exception $e) {
                echo '<div class="status-item error">';
                echo '<span class="status-icon">X</span>';
                echo '<div class="status-text">';
                echo '<h6>Database Connection Failed</h6>';
                echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
                echo '<p style="margin-top: 5px; font-size: 12px;">Make sure: 1) MySQL is running 2) polling_system database is created 3) Database credentials are correct in bootstrap/app.php</p>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo '<div class="status-item warning">';
            echo '<span class="status-icon">!</span>';
            echo '<div class="status-text"><h6>PDO MySQL Not Available</h6><p>Cannot test database connection without PDO MySQL extension</p></div>';
            echo '</div>';
        }
        
        echo '</div>';
        
        // Quick Start
        echo '<div class="status-section">';
        echo '<div class="section-header">Next Steps</div>';
        
        echo '<ol style="font-size: 16px; line-height: 1.8;">';
        echo '<li>If all checks show OK: Your system is ready!</li>';
        echo '<li>If database is not connected: Import database/schema.sql into your MySQL database</li>';
        echo '<li>Make sure Apache/PHP server is running via XAMPP</li>';
        echo '<li>Access the application using the link below</li>';
        echo '</ol>';
        
        echo '<div class="localhost-url">';
        echo '<p style="margin-bottom: 10px;">Access Application Here:</p>';
        echo '<a href="http://localhost/Mohit/public/">http://localhost/Mohit/public/</a>';
        echo '</div>';
        
        echo '</div>';
        
        // Test Credentials
        echo '<div class="status-section">';
        echo '<div class="section-header">Test Credentials</div>';
        echo '<div style="background: #f0f7ff; padding: 15px; border-radius: 5px; border-left: 4px solid #0066cc;">';
        echo '<p style="margin-bottom: 10px;"><strong>Admin Account:</strong></p>';
        echo '<p style="margin: 5px 0;">Email: <code>admin@polling.test</code></p>';
        echo '<p style="margin: 5px 0;">Password: <code>admin123</code></p>';
        echo '<hr style="margin: 15px 0;">';
        echo '<p style="margin-bottom: 10px;"><strong>Regular User Account:</strong></p>';
        echo '<p style="margin: 5px 0;">Email: <code>user@polling.test</code></p>';
        echo '<p style="margin: 5px 0;">Password: <code>user123</code></p>';
        echo '</div>';
        echo '</div>';
        
        // Help Section
        echo '<div class="status-section">';
        echo '<div style="background: #f9f9f9; padding: 15px; border-radius: 5px; border-left: 4px solid #999;">';
        echo '<h6>Troubleshooting</h6>';
        echo '<ul style="margin: 10px 0; padding-left: 20px;">';
        echo '<li><strong>404 Not Found:</strong> Make sure you\'re accessing /public/ and .htaccess is in the public folder</li>';
        echo '<li><strong>Blank Page:</strong> Check PHP error logs in XAMPP</li>';
        echo '<li><strong>Database Error:</strong> Verify MySQL is running and import schema.sql</li>';
        echo '<li><strong>Permission Denied:</strong> Check file permissions in Windows</li>';
        echo '</ul>';
        echo '</div>';
        echo '</div>';
        ?>

        <div class="status-section" style="text-align: center; color: #666;">
            <small>Real-Time Live Poll Platform | Installation Status Checker</small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
