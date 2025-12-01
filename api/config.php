<?php

(function () {
    $envFile = __DIR__ . '/.env';
    if (!is_readable($envFile)) {
        return;
    }
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') {
            continue;
        }
        $pos = strpos($line, '=');
        if ($pos === false) {
            continue;
        }
        $key = trim(substr($line, 0, $pos));
        $val = trim(substr($line, $pos + 1));
        $val = trim($val, "\"' ");
        if ($key !== '') {
            putenv($key . '=' . $val);
            $_ENV[$key] = $val;
            $_SERVER[$key] = $val;
        }
    }
})();

// Database configuration
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'my_database');
define('DB_USER', getenv('DB_USER') ?: 'db_user');
define('DB_PASSWORD', getenv('DB_PASSWORD') ?: 'db_password');
define('DB_CHARSET', 'utf8mb4');

// Table name
define('VOCAB_TABLE', 'vocabulary');

// API configuration
define('API_PATH', 'vocab');

// CORS configuration
define('CORS_ENABLED', true);
define('CORS_ORIGIN', '*'); // Change to specific domain in production
define('CORS_METHODS', 'GET, POST, PUT, DELETE, OPTIONS');
define('CORS_HEADERS', 'Content-Type, Authorization');

// Authentication
define('AUTH_ENABLED', false); // Set to true to enable authentication
define('API_KEY', getenv('API_KEY') ?: 'your-secret-api-key-here');

// Error reporting
define('DEBUG_MODE', strtolower((string)getenv('DEBUG_MODE')) === 'true');
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
