<?php

/**
 * Helper Functions for Real-Time Poll Platform
 */

// ===== SECURITY HELPERS =====

/**
 * Generate CSRF token
 */
function csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verify_csrf_token($token)
{
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

/**
 * Escape HTML special characters
 */
function escape($string)
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Generate secure random string
 */
function generate_token($length = 32)
{
    return bin2hex(random_bytes($length));
}

// ===== AUTHENTICATION HELPERS =====

/**
 * Check if user is authenticated
 */
function is_authenticated()
{
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if user is admin
 */
function is_admin()
{
    return is_authenticated() && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Get current user ID
 */
function current_user_id()
{
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user info
 */
function current_user()
{
    return [
        'id' => $_SESSION['user_id'] ?? null,
        'name' => $_SESSION['user_name'] ?? null,
        'email' => $_SESSION['user_email'] ?? null,
        'role' => $_SESSION['user_role'] ?? null,
    ];
}

/**
 * Redirect to URL
 */
function redirect($url)
{
    header("Location: {$url}");
    exit;
}

// ===== REQUEST HELPERS =====

/**
 * Get request method
 */
function request_method()
{
    return $_SERVER['REQUEST_METHOD'];
}

// ===== ACTIVITY LOGGING =====

/**
 * Log user activity with forensic data
 */
function log_activity($pdo, $userId, $action, $description = '', $extraData = [])
{
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $ipAddress = $_SERVER['HTTP_CLIENT_IP']
        ?? explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '')[0]
        ?? $_SERVER['REMOTE_ADDR']
        ?? '127.0.0.1';
    $ipAddress = trim($ipAddress);
    if (!filter_var($ipAddress, FILTER_VALIDATE_IP)) {
        $ipAddress = '127.0.0.1';
    }

    $deviceType = parse_device_type($userAgent);
    $browser = parse_browser($userAgent);
    $osPlatform = parse_os($userAgent);
    $referrer = $_SERVER['HTTP_REFERER'] ?? '';
    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
    $requestMethod = $_SERVER['REQUEST_METHOD'] ?? '';
    $sessionId = session_id() ?: '';

    $stmt = $pdo->prepare("
        INSERT INTO activity_logs
            (user_id, action, description, ip_address, user_agent, device_type, browser, os_platform, referrer, request_uri, request_method, session_id, extra_data, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([
        $userId,
        $action,
        $description,
        $ipAddress,
        $userAgent,
        $deviceType,
        $browser,
        $osPlatform,
        $referrer,
        $requestUri,
        $requestMethod,
        $sessionId,
        json_encode($extraData)
    ]);
}

/**
 * Parse device type from user agent string
 */
function parse_device_type($ua)
{
    $ua = strtolower($ua);
    if (preg_match('/mobile|android.*mobile|iphone|ipod|blackberry|iemobile|opera mini/', $ua)) {
        return 'mobile';
    }
    if (preg_match('/tablet|ipad|android(?!.*mobile)|kindle|silk/', $ua)) {
        return 'tablet';
    }
    return 'desktop';
}

/**
 * Parse browser name from user agent string
 */
function parse_browser($ua)
{
    if (preg_match('/Edg\//i', $ua)) return 'Edge';
    if (preg_match('/OPR\//i', $ua)) return 'Opera';
    if (preg_match('/Chrome\//i', $ua) && !preg_match('/Edg\//i', $ua)) return 'Chrome';
    if (preg_match('/Safari\//i', $ua) && !preg_match('/Chrome\//i', $ua)) return 'Safari';
    if (preg_match('/Firefox\//i', $ua)) return 'Firefox';
    if (preg_match('/MSIE|Trident/i', $ua)) return 'Internet Explorer';
    return 'Unknown';
}

/**
 * Parse OS platform from user agent string
 */
function parse_os($ua)
{
    if (preg_match('/Windows NT/i', $ua)) return 'Windows';
    if (preg_match('/Macintosh|Mac OS X/i', $ua)) return 'macOS';
    if (preg_match('/Linux/i', $ua) && !preg_match('/Android/i', $ua)) return 'Linux';
    if (preg_match('/Android/i', $ua)) return 'Android';
    if (preg_match('/iPhone|iPad|iPod/i', $ua)) return 'iOS';
    if (preg_match('/CrOS/i', $ua)) return 'Chrome OS';
    return 'Unknown';
}

/**
 * Check if POST request
 */
function is_post()
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Check if GET request
 */
function is_get()
{
    return $_SERVER['REQUEST_METHOD'] === 'GET';
}

/**
 * Check if AJAX request
 */
function is_ajax()
{
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Get old input value
 */
function old($key, $default = '')
{
    return $_POST[$key] ?? $_GET[$key] ?? $default;
}

// ===== IP HELPERS =====

/**
 * Get client IP address
 */
function get_client_ip()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    $ip = trim($ip);

    // Validate IP
    if (filter_var($ip, FILTER_VALIDATE_IP)) {
        return $ip;
    }

    return '127.0.0.1';
}

/**
 * Check if IP is valid
 */
function is_valid_ip($ip)
{
    return filter_var($ip, FILTER_VALIDATE_IP) !== false;
}

// ===== SESSION HELPERS =====

/**
 * Set session message
 */
function session_flash($type, $message)
{
    $_SESSION[$type] = $message;
}

/**
 * Get and clear session message
 */
function get_session_message($type)
{
    $message = $_SESSION[$type] ?? null;
    unset($_SESSION[$type]);
    return $message;
}

/**
 * Check if session message exists
 */
function has_session_message($type)
{
    return isset($_SESSION[$type]);
}

// ===== VALIDATION HELPERS =====

/**
 * Validate email
 */
function validate_email($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate URL
 */
function validate_url($url)
{
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

/**
 * Validate integer
 */
function validate_integer($value)
{
    return filter_var($value, FILTER_VALIDATE_INT) !== false;
}

/**
 * Validate JSON
 */
function validate_json($json)
{
    json_decode($json);
    return json_last_error() === JSON_ERROR_NONE;
}

// ===== STRING HELPERS =====

/**
 * Truncate string
 */
function truncate($string, $length = 100, $suffix = '...')
{
    if (strlen($string) <= $length) {
        return $string;
    }
    return substr($string, 0, $length) . $suffix;
}

/**
 * Format bytes to human readable
 */
function format_bytes($bytes)
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    
    return round($bytes, 2) . ' ' . $units[$pow];
}

/**
 * Generate slug
 */
function generate_slug($string)
{
    $string = strtolower(trim($string));
    $string = preg_replace('/[^a-z0-9]+/i', '-', $string);
    $string = trim($string, '-');
    return $string;
}

// ===== DATE HELPERS =====

/**
 * Format date
 */
function format_date($date, $format = 'Y-m-d H:i:s')
{
    if (is_string($date)) {
        $date = strtotime($date);
    }
    return date($format, $date);
}

/**
 * Get time ago
 */
function time_ago($timestamp)
{
    $time = time() - strtotime($timestamp);
    $units = array(
        31536000 => 'year',
        2592000 => 'month',
        604800 => 'week',
        86400 => 'day',
        3600 => 'hour',
        60 => 'minute',
        1 => 'second'
    );

    foreach ($units as $unit_time => $unit_name) {
        if ($time < $unit_time) {
            continue;
        }
        $time = round($time / $unit_time);
        return $time . ' ' . $unit_name . ($time > 1 ? 's' : '') . ' ago';
    }
    return 'now';
}

// ===== JSON HELPERS =====

/**
 * Send JSON response
 */
function json_response($data, $statusCode = 200)
{
    header('Content-Type: application/json');
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Send success JSON response
 */
function json_success($data, $message = 'Success')
{
    json_response(['success' => true, 'message' => $message, 'data' => $data], 200);
}

/**
 * Send error JSON response
 */
function json_error($message, $statusCode = 400)
{
    json_response(['success' => false, 'message' => $message], $statusCode);
}

// ===== ARRAY HELPERS =====

/**
 * Get array value safely
 */
function array_get($array, $key, $default = null)
{
    return $array[$key] ?? $default;
}

/**
 * Filter array by keys
 */
function array_only($array, $keys)
{
    return array_intersect_key($array, array_flip($keys));
}

// ===== DEBUGGING =====

/**
 * Log message to file
 */
function log_message($message, $level = 'INFO')
{
    $logFile = __DIR__ . '/../logs/app.log';
    
    if (!is_dir(dirname($logFile))) {
        mkdir(dirname($logFile), 0777, true);
    }

    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] [$level] $message\n";
    
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}

/**
 * Dump variable for debugging
 */
function dump($variable)
{
    echo '<pre>';
    var_dump($variable);
    echo '</pre>';
}

/**
 * Dump and die
 */
function dd($variable)
{
    dump($variable);
    exit;
}
