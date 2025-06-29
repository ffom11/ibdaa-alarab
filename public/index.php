<?php
// Front Controller

// Show errors in development environment
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define base path
define('BASE_PATH', realpath(__DIR__ . '/..'));

// Load configuration files
require_once __DIR__ . '/../config/config.php';

// Get the requested URL path
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$script_name = dirname($_SERVER['SCRIPT_NAME']);

// Remove the script name from the request URI
$path = substr($request_uri, strlen($script_name));
$path = trim($path, '/');

// Route the request
switch ($path) {
    case '':
    case '/':
        // Home page
        include __DIR__ . '/home.php';
        break;
        
    case 'login':
    case 'login/':
        include __DIR__ . '/login.php';
        break;
        
    case 'register':
    case 'register/':
        include __DIR__ . '/register.php';
        break;
        
    // Add more routes as needed
        
    default:
        // Check if the file exists
        $file = __DIR__ . '/' . $path;
        if (file_exists($file) && is_file($file)) {
            // Serve the file directly
            return false;
        } else {
            // 404 Not Found
            http_response_code(404);
            echo '<!DOCTYPE html>
            <html dir="rtl" lang="ar">
            <head>
                <title>404 الصفحة غير موجودة</title>
                <style>
                    body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
                    h1 { font-size: 50px; }
                    body { font: 20px Helvetica, sans-serif; color: #333; }
                    article { display: block; text-align: center; max-width: 650px; margin: 0 auto; }
                    a { color: #e74c3c; text-decoration: none; }
                    a:hover { color: #c0392b; text-decoration: underline; }
                </style>
            </head>
            <body>
                <article>
                    <h1>404</h1>
                    <h2>الصفحة غير موجودة</h2>
                    <div>
                        <p>عذراً، الصفحة التي تبحث عنها غير موجودة أو تم نقلها.</p>
                        <p><a href="/">العودة للصفحة الرئيسية</a></p>
                    </div>
                </article>
            </body>
            </html>';
            exit();
        }
        break;
}
