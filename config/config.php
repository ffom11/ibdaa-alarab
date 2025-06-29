<?php
/**
 * ملف الإعدادات الرئيسي
 * يحتوي على إعدادات التطبيق والثوابت الهامة
 */

// تحميل ملف البيئة
$envPath = __DIR__ . '/../.env';
$dotenv = [];

if (file_exists($envPath)) {
    $dotenv = parse_ini_file($envPath);
}

// إعدادات البيئة
define('ENVIRONMENT', $dotenv['APP_ENV'] ?? 'production');
define('APP_DEBUG', filter_var($dotenv['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN));

// إعدادات قاعدة البيانات
define('DB_HOST', $dotenv['DB_HOST'] ?? 'localhost');
define('DB_NAME', $dotenv['DB_NAME'] ?? 'ibdaa_alarab');
define('DB_USER', $dotenv['DB_USER'] ?? 'root');
define('DB_PASS', $dotenv['DB_PASS'] ?? '');
define('DB_CHARSET', $dotenv['DB_CHARSET'] ?? 'utf8mb4');

// إعدادات البريد الإلكتروني
define('MAIL_HOST', $dotenv['MAIL_HOST'] ?? 'smtp.mailtrap.io');
define('MAIL_PORT', $dotenv['MAIL_PORT'] ?? 2525);
define('MAIL_USERNAME', $dotenv['MAIL_USERNAME'] ?? '');
define('MAIL_PASSWORD', $dotenv['MAIL_PASSWORD'] ?? '');
define('MAIL_FROM_EMAIL', $dotenv['MAIL_FROM_EMAIL'] ?? 'noreply@ibdaa-alarab.com');
define('MAIL_FROM_NAME', $dotenv['MAIL_FROM_NAME'] ?? 'إبداع العرب');

// إعدادات الجلسة
define('SESSION_NAME', 'ebdaa_sess');
define('SESSION_LIFETIME', 3600); // ثانية (ساعة واحدة)
define('SESSION_PATH', '/');
define('SESSION_DOMAIN', $_SERVER['HTTP_HOST'] ?? 'localhost');
define('SESSION_SECURE', isset($_SERVER['HTTPS']));
define('SESSION_HTTP_ONLY', true);

// إعدادات التطبيق
define('SITE_NAME', 'إبداع العرب');
define('SITE_URL', $dotenv['APP_URL'] ?? 'https://ibdaa-alarab.com');
define('ADMIN_EMAIL', $dotenv['ADMIN_EMAIL'] ?? 'admin@ibdaa-alarab.com');

// إعدادات التحميل
define('UPLOAD_DIR', __DIR__ . '/../assets/uploads/');
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}

define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5 ميجابايت
define('ALLOWED_FILE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'application/pdf']);

// إعدادات البريد الإلكتروني (تم نقلها للأعلى مع القيم الافتراضية)
// تعريف SITE_NAME إذا لم يكن معرفاً
define('SITE_NAME', $dotenv['SITE_NAME'] ?? 'إبداع العرب');

// مفاتيح API
define('RECAPTCHA_SITE_KEY', $dotenv['RECAPTCHA_SITE_KEY']);
define('RECAPTCHA_SECRET_KEY', $dotenv['RECAPTCHA_SECRET_KEY']);
define('GOOGLE_MAPS_API_KEY', $dotenv['GOOGLE_MAPS_API_KEY']);

// إعدادات التخزين المؤقت
const CACHE_ENABLED = true;
const CACHE_DIR = __DIR__ . '/../cache/';
const CACHE_LIFETIME = 3600; // ثانية (ساعة واحدة)

// إعدادات السجلات
const LOG_DIR = __DIR__ . '/../logs/';
const LOG_LEVEL = ENVIRONMENT === 'production' ? 3 : 0; // 0: none, 1: error, 2: warning, 3: info, 4: debug

// تحميل ملفات التكوين الإضافية
$envConfig = __DIR__ . '/config.' . ENVIRONMENT . '.php';
if (file_exists($envConfig)) {
    require_once $envConfig;
}

// ضبط مستوى الإبلاغ عن الأخطاء
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// تعيين المنطقة الزمنية
date_default_timezone_set('Asia/Riyadh');

// تعيين ترميز المحتوى
header('Content-Type: text/html; charset=utf-8');

// تعيين صلاحيات الملفات والمجلدات
umask(0022);

// تعيين مسار التطبيق
const APP_PATH = __DIR__ . '/..';

// تحميل ملفات التبعيات
require_once __DIR__ . '/../vendor/autoload.php';

// بدء الجلسة
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_set_cookie_params([
        'lifetime' => SESSION_LIFETIME,
        'path' => SESSION_PATH,
        'domain' => SESSION_DOMAIN,
        'secure' => SESSION_SECURE,
        'httponly' => SESSION_HTTP_ONLY,
        'samesite' => 'Lax'
    ]);
    session_start();
}

// تحديث وقت انتهاء صلاحية الجلسة مع كل طلب
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
    // إذا مرت أكثر من 30 دقيقة من آخر طلب، يتم تدمير الجلسة
    session_unset();
    session_destroy();
    session_start();
}
$_SESSION['LAST_ACTIVITY'] = time();

// التحقق من هجمات CSRF
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// دالة لإنشاء رمز CSRF
function csrf_token() {
    return $_SESSION['csrf_token'];
}

// دالة للتحقق من رمز CSRF
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// دالة لإعادة التوجيه
function redirect($url, $statusCode = 303) {
    header('Location: ' . $url, true, $statusCode);
    exit();
}

// دالة لتسجيل الأخطاء
function log_message($level, $message, $context = []) {
    $levels = [
        0 => 'DEBUG',
        1 => 'INFO',
        2 => 'WARNING',
        3 => 'ERROR',
        4 => 'CRITICAL'
    ];
    
    $level = strtoupper($level);
    if (!in_array($level, $levels)) {
        $level = 'INFO';
    }
    
    $log = sprintf(
        "[%s] %s: %s %s" . PHP_EOL,
        date('Y-m-d H:i:s'),
        $level,
        $message,
        !empty($context) ? json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : ''
    );
    
    // إنشاء مجلد السجلات إذا لم يكن موجوداً
    if (!is_dir(LOG_DIR)) {
        mkdir(LOG_DIR, 0755, true);
    }
    
    // كتابة السجل في الملف
    error_log($log, 3, LOG_DIR . 'app-' . date('Y-m-d') . '.log');
}

// معالجة الأخطاء غير الملتقطة
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    log_message('ERROR', sprintf('%s in %s on line %d', $errstr, $errfile, $errline));
    if (ENVIRONMENT === 'development') {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
    return true;
});

// معالجة الاستثناءات غير الملتقطة
set_exception_handler(function($e) {
    log_message('CRITICAL', sprintf(
        'Uncaught Exception: %s in %s on line %d',
        $e->getMessage(),
        $e->getFile(),
        $e->getLine()
    ), ['trace' => $e->getTraceAsString()]);
    
    if (ENVIRONMENT === 'development') {
        echo '<h1>خطأ في التطبيق</h1>';
        echo '<p>' . $e->getMessage() . '</p>';
        echo '<pre>' . $e->getTraceAsString() . '</pre>';
    } else {
        header('HTTP/1.1 500 Internal Server Error');
        echo '<h1>عذراً، حدث خطأ ما</h1>';
        echo '<p>لقد واجهنا مشكلة فنية. يرجى المحاولة مرة أخرى لاحقاً.</p>';
    }
    exit(1);
});

// معالجة الأخطاء القاتلة
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
        log_message('CRITICAL', sprintf(
            'Fatal Error: %s in %s on line %d',
            $error['message'],
            $error['file'],
            $error['line']
        ));
        
        if (ENVIRONMENT !== 'development') {
            header('HTTP/1.1 500 Internal Server Error');
            echo '<h1>عذراً، حدث خطأ فادح</h1>';
            echo '<p>لقد واجهنا مشكلة فنية خطيرة. يرجى إبلاغ المسؤول.</p>';
        }
    }
});
