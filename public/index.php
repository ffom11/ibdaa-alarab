<?php
// Front Controller

// Define base path
define('BASE_PATH', realpath(__DIR__ . '/..'));

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors', '1');
ini_set('error_log', BASE_PATH . '/storage/logs/php_errors.log');

// Create necessary directories if they don't exist
$directories = [
    BASE_PATH . '/storage/logs',
    BASE_PATH . '/storage/cache',
    BASE_PATH . '/storage/sessions',
    BASE_PATH . '/storage/views'
];

foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        if (!mkdir($dir, 0755, true)) {
            error_log("Failed to create directory: $dir");
        } else {
            error_log("Created directory: $dir");
        }
    }
}

// Log request details
error_log("\n" . str_repeat("=", 80));
error_log(sprintf(
    "[%s] %s %s\nUser-Agent: %s\nPHP Version: %s\nDocument Root: %s",
    date('Y-m-d H:i:s'),
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI'],
    $_SERVER['HTTP_USER_AGENT'] ?? 'No User Agent',
    phpversion(),
    $_SERVER['DOCUMENT_ROOT'] ?? 'Not set'
));

// Log environment variables
error_log("Environment variables: " . print_r($_ENV, true));

// Set custom error handler
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    $error_type = [
        E_ERROR             => 'Error',
        E_WARNING           => 'Warning',
        E_PARSE             => 'Parse Error',
        E_NOTICE            => 'Notice',
        E_CORE_ERROR        => 'Core Error',
        E_CORE_WARNING      => 'Core Warning',
        E_COMPILE_ERROR     => 'Compile Error',
        E_COMPILE_WARNING   => 'Compile Warning',
        E_USER_ERROR        => 'User Error',
        E_USER_WARNING      => 'User Warning',
        E_USER_NOTICE       => 'User Notice',
        E_STRICT            => 'Runtime Notice',
        E_RECOVERABLE_ERROR => 'Catchable Fatal Error',
        E_DEPRECATED        => 'Deprecated',
        E_USER_DEPRECATED   => 'User Deprecated',
    ];
    
    $errname = $error_type[$errno] ?? 'Unknown Error';
    
    $error_msg = sprintf(
        "PHP %s: %s in %s on line %d\nStack Trace:\n%s",
        $errname,
        $errstr,
        $errfile,
        $errline,
        json_encode(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), JSON_PRETTY_PRINT)
    );
    
    error_log($error_msg);
    
    // If it's a fatal error, we should exit
    if (in_array($errno, [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
        http_response_code(500);
        if (file_exists(BASE_PATH . '/public/500.html')) {
            readfile(BASE_PATH . '/public/500.html');
        } else {
            echo '<h1>500 Internal Server Error</h1>';
            echo '<p>An error occurred while processing your request.</p>';
            if (ini_get('display_errors')) {
                echo '<pre>' . htmlspecialchars($error_msg) . '</pre>';
            }
        }
        exit(1);
    }
    
    return true; // Don't execute PHP internal error handler
});

// Set exception handler
set_exception_handler(function($e) {
    error_log(sprintf(
        "Uncaught Exception: %s in %s on line %d\nStack Trace:\n%s",
        $e->getMessage(),
        $e->getFile(),
        $e->getLine(),
        $e->getTraceAsString()
    ));
    
    // Show error page
    http_response_code(500);
    if (file_exists(__DIR__ . '/500.html')) {
        include __DIR__ . '/500.html';
    } else {
        echo '<h1>حدث خطأ في الخادم</h1>';
        echo '<p>نعتذر عن حدوث خطأ. يرجى المحاولة مرة أخرى لاحقاً.</p>';
        if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        }
    }
    exit(1);
});

try {
    // Load configuration files
    $configFile = __DIR__ . '/../config/config.php';
    if (!file_exists($configFile)) {
        throw new Exception("ملف التكوين غير موجود: " . $configFile);
    }
    
    error_log("Loading config file: " . $configFile);
    require_once $configFile;
    
    if (!defined('ENVIRONMENT')) {
        throw new Exception('فشل تحميل ملف التكوين: لم يتم تعريف ثابت ENVIRONMENT');
    }
    
    error_log("Config loaded successfully. Environment: " . ENVIRONMENT);

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

} catch (Throwable $e) {
    // Log the error
    error_log("Error: " . $e->getMessage());
    error_log("File: " . $e->getFile() . ":" . $e->getLine());
    error_log("Trace: " . $e->getTraceAsString());
    
    // Show error page
    if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        echo '<h1>Error: ' . htmlspecialchars($e->getMessage()) . '</h1>';
        echo '<p>File: ' . htmlspecialchars($e->getFile()) . ' on line ' . $e->getLine() . '</p>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    } else {
        http_response_code(500);
        echo '<h1>حدث خطأ في الخادم</h1>';
        echo '<p>نعتذر عن حدوث خطأ. يرجى المحاولة مرة أخرى لاحقاً.</p>';
    }
}
