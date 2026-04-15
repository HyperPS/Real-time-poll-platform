<?php

/**
 * Database Configuration
 */

return [
    'host' => env('DB_HOST', 'localhost'),
    'database' => env('DB_NAME', 'polling_system'),
    'username' => env('DB_USER', 'root'),
    'password' => env('DB_PASS', ''),
    'charset' => env('DB_CHARSET', 'utf8mb4'),
];
