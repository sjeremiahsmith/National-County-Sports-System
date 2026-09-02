<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'ncsm_portal');
define('DB_USER', 'root');
define('DB_PASS', '');

// Application configuration
define('APP_NAME', 'National County Sports System');
define('APP_SHORT_NAME', 'National County Sports System');

// Robust Base URL Configuration
if (!defined('APP_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    // TIP: If you have a custom domain or subfolder on InfinityFree, 
    // you can uncomment the line below and hardcode it to avoid any server-side detection issues:
    // define('APP_URL', 'localhost');
    
    // Auto-detect the full base path including any subfolder
    // __DIR__ is always the includes/ folder, so go one level up to get the project root
    $appRoot = dirname(__DIR__);
    $docRoot = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/\\');
    $relativePath = str_replace('\\', '/', substr($appRoot, strlen($docRoot)));
    $relativePath = trim($relativePath, '/\\');
    define('APP_URL', $protocol . '://' . $host . '/' . $relativePath . '/');
}

// Upload paths relative to this config file
define('UPLOAD_PATH', dirname(__DIR__) . '/uploads/');
define('PHOTO_PATH', UPLOAD_PATH . 'photos/');
define('DOCUMENT_PATH', UPLOAD_PATH . 'documents/');
define('CARD_PATH', UPLOAD_PATH . 'cards/');
define('GALLERY_PATH', UPLOAD_PATH . 'gallery/');
define('VIDEO_PATH', UPLOAD_PATH . 'videos/');

define('MAX_PHOTO_SIZE', 2 * 1024 * 1024); // 2MB
define('ALLOWED_PHOTO_TYPES', ['image/jpeg', 'image/png', 'image/gif']);
define('MAX_DOCUMENT_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_DOCUMENT_TYPES', [
    'application/pdf', 
    'application/msword', 
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 
    'application/vnd.ms-excel', 
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 
    'text/plain', 
    'image/jpeg', 
    'image/png'
]);

define('MAX_VIDEO_SIZE', 200 * 1024 * 1024); // 200MB
define('ALLOWED_VIDEO_TYPES', ['video/mp4', 'video/webm', 'video/ogg', 'video/quicktime']);

// Timezone
date_default_timezone_set('Africa/Monrovia');

// Error reporting (set to 0 in production once everything is verified working)
$isLocal = in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1', '::1']);
error_reporting($isLocal ? E_ALL : 0);
ini_set('display_errors', $isLocal ? 1 : 0);
ini_set('log_errors', 1);

// Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}