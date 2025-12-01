<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/util.php';
require_once __DIR__ . '/db.php';

setup_cors();

check_auth();

$method = $_SERVER['REQUEST_METHOD'];
$path   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$parts  = array_values(array_filter(explode('/', trim($path, '/')), 'strlen'));

if (!empty($parts) && strtolower($parts[0]) === 'index.php') {
    array_shift($parts);
}
if (!empty($parts) && strtolower($parts[0]) === 'api') {
    array_shift($parts);
}

$resource = $parts[0] ?? null;

switch ($resource) {
    case API_PATH:
        $handler_file = __DIR__ . '/handlers/' . strtolower($method) . '.php';
        if (file_exists($handler_file)) {
            require_once $handler_file;
        } else {
            error_response('Method Not Allowed', 405);
        }
        break;
    default:
        error_response('Not Found', 404);
        break;
}