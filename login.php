<?php
/**
 * login.php - معالجة وعرض نموذج تسجيل الدخول
 * 
 * هذا الملف مسؤول عن معالجة طلبات تسجيل الدخول وعرض نموذج تسجيل الدخول
 */

// تمكين عرض الأخطاء للأغراض التنموية فقط
if (getenv('APP_ENV') !== 'production') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// بدء الجلسة مع الإعدادات المحددة
session_start($session_params);

// =============================================
// 2. تعيين رؤوس الأمان
// =============================================
// منع التخزين المؤقت للصفحات الحساسة
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

// رؤوس أمان HTTP
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
header('X-Permitted-Cross-Domain-Policies: none');
header('Cross-Origin-Resource-Policy: same-origin');
header('Cross-Origin-Embedder-Policy: require-corp');
header('Cross-Origin-Opener-Policy: same-origin');

// سياسة أمان المحتوى (CSP)
$csp = [
    "default-src 'self';",
    "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdnjs.cloudflare.com https://www.google.com https://www.gstatic.com https://connect.facebook.net;",
    "style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://fonts.googleapis.com;",
    "img-src 'self' data: https:;",
    "font-src 'self' https://cdnjs.cloudflare.com https://fonts.gstatic.com data:;",
    "connect-src 'self' https://www.google-analytics.com https://stats.g.doubleclick.net;",
    "frame-src 'self' https://www.google.com https://www.facebook.com;",
    "media-src 'self' https:;",
    "object-src 'none';",
    "frame-ancestors 'none';",
    "form-action 'self';",
    "base-uri 'self';",
    "block-all-mixed-content;",
    "upgrade-insecure-requests;"
];

header("Content-Security-Policy: " . implode(" ", $csp));

// تعيين المنطقة الزمنية
date_default_timezone_set('Asia/Riyadh');

// =============================================
// 3. معالجة إرسال النموذج
// =============================================
// معالجة إرسال النموذج
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // الحصول على عنوان IP الحقيقي للعميل
    $ip_address = $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $ip_address = filter_var($ip_address, FILTER_VALIDATE_IP) ? $ip_address : '0.0.0.0';
    
    // التحقق من رمز CSRF
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) {
        $error_message = 'رمز الأمان غير صالح. يرجى تحديث الصفحة والمحاولة مرة أخرى.';
        http_response_code(400);
        
        // تسجيل محاولة تسجيل دخول فاشلة
        error_log(sprintf(
            'CSRF token validation failed - IP: %s, User-Agent: %s',
            $ip_address,
            $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
        ));
    } else {
        // التحقق من وجود ملف قاعدة البيانات
        $dbFile = __DIR__ . '/includes/db.php';
        if (!file_exists($dbFile)) {
            error_log('Error: Database file not found: ' . $dbFile);
            $error_message = 'خطأ في النظام. يرجى إبلاغ المسؤول.';
            http_response_code(500);
        } else {
            // تنظيف وتأمين المدخلات
            $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING) ?? '';
            $password = $_POST['password'] ?? ''; // كلمة المرور لا يتم تصفيتها
            $remember = isset($_POST['remember']);
            
            // التحقق من صحة المدخلات
            if (empty($username) || empty($password)) {
                $error_message = 'يرجى إدخال اسم المستخدم/البريد الإلكتروني وكلمة المرور';
            } elseif (strlen($password) < 8) {
                $error_message = 'يجب أن تكون كلمة المرور 8 أحرف على الأقل';
            } else {
                // تحميل ملف قاعدة البيانات
                require_once $dbFile;
                
                try {
                    // الاتصال بقاعدة البيانات
                    $pdo = new PDO(
                        'mysql:host=localhost;dbname=ibdaa_alarab;charset=utf8mb4',
                        'db_username', // يجب تغييرها
                        'db_password',  // يجب تغييرها
                        [
                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                            PDO::ATTR_EMULATE_PREPARES => false,
                            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                        ]
                    );
                    
                    // التحقق من عدد محاولات تسجيل الدخول الفاشلة
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM login_attempts 
                                         WHERE ip_address = :ip 
                                         AND attempt_time > DATE_SUB(NOW(), INTERVAL 15 MINUTE) 
                                         AND success = 0");
                    $stmt->execute([':ip' => $ip_address]);
                    $failed_attempts = $stmt->fetchColumn();
                    
                    $max_attempts = 5;
                    if ($failed_attempts >= $max_attempts) {
                        $error_message = 'لقد تجاوزت الحد المسموح لمحاولات تسجيل الدخول. يرجى المحاولة بعد 15 دقيقة.';
                    } else {
                        // البحث عن المستخدم مع التحقق من الحالة
                        $stmt = $pdo->prepare('SELECT * FROM users WHERE (username = :username OR email = :email) AND status = :status LIMIT 1');
                        $stmt->execute([
                            ':username' => $username,
                            ':email' => $username,
                            ':status' => 'active'
                        ]);
                        $user = $stmt->fetch();
                        
                        if ($user) {
                            // التحقق من حالة القفل المؤقت للحساب
                            if (!empty($user['locked_until']) && strtotime($user['locked_until']) > time()) {
                                $error_message = 'تم تعطيل حسابك مؤقتًا. يرجى المحاولة لاحقًا.';
                            } 
                            // التحقق من كلمة المرور
                            elseif (password_verify($password, $user['password'])) {
                                // التحقق مما إذا كانت كلمة المرور بحاجة إلى تحديث
                                if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
                                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                                    $stmt = $pdo->prepare('UPDATE users SET password = :password WHERE id = :id');
                                    $stmt->execute([':password' => $newHash, ':id' => $user['id']]);
                                }
                                
                                // تسجيل بيانات الجلسة
                                $_SESSION['user_id'] = $user['id'];
                                $_SESSION['username'] = $user['username'];
                                $_SESSION['email'] = $user['email'];
                                $_SESSION['role'] = $user['role'] ?? 'user';
                                $_SESSION['last_activity'] = time();
                                
                                // إنشاء معرف جلسة جديد لمنع هجمات تثبيت الجلسة
                                session_regenerate_id(true);
                                
                                // تعيين ملف تعريف الارتباط "تذكرني" إذا طلب المستخدم ذلك
                                if ($remember) {
                                    $token = bin2hex(random_bytes(32));
                                    $expires = date('Y-m-d H:i:s', strtotime('+30 days'));
                                    
                                    // تخزين التوكن في قاعدة البيانات
                                    $stmt = $pdo->prepare('INSERT INTO user_tokens (user_id, token, type, expires_at) 
                                                         VALUES (:user_id, :token, :type, :expires)');
                                    $stmt->execute([
                                        ':user_id' => $user['id'],
                                        ':token' => password_hash($token, PASSWORD_DEFAULT),
                                        ':type' => 'remember_me',
                                        ':expires' => $expires
                                    ]);
                                    
                                    // تعيين ملف تعريف الارتباط
                                    setcookie(
                                        'remember_me',
                                        $user['id'] . ':' . $token,
                                        [
                                            'expires' => strtotime('+30 days'),
                                            'path' => '/',
                                            'domain' => '',
                                            'secure' => isset($_SERVER['HTTPS']),
                                            'httponly' => true,
                                            'samesite' => 'Lax'
                                        ]
                                    );
                                }
                                
                                // تسجيل محاولة تسجيل الدخول الناجحة
                                $stmt = $pdo->prepare('INSERT INTO login_attempts 
                                                     (user_id, username, ip_address, user_agent, success) 
                                                     VALUES (:user_id, :username, :ip, :user_agent, 1)');
                                $stmt->execute([
                                    ':user_id' => $user['id'],
                                    ':username' => $user['username'],
                                    ':ip' => $ip_address,
                                    ':user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
                                ]);
                                
                                // إعادة تعيين عداد المحاولات الفاشلة
                                $stmt = $pdo->prepare('UPDATE users SET login_attempts = 0, last_login = NOW(), last_ip = :ip WHERE id = :id');
                                $stmt->execute([
                                    ':ip' => $ip_address,
                                    ':id' => $user['id']
                                ]);
                                
                                // إعادة التوجيه إلى الصفحة المطلوبة
                                if (isset($_SESSION['redirect_after_login'])) {
                                    $redirect = $_SESSION['redirect_after_login'];
                                    unset($_SESSION['redirect_after_login']);
                                    header('Location: ' . $redirect);
                                } else {
                                    header('Location: /dashboard/');
                                }
                                exit();
                            } else {
                                // كلمة المرور غير صحيحة
                                $stmt = $pdo->prepare('INSERT INTO login_attempts 
                                                     (user_id, username, ip_address, user_agent, success) 
                                                     VALUES (:user_id, :username, :ip, :user_agent, 0)');
                                $stmt->execute([
                                    ':user_id' => $user['id'],
                                    ':username' => $user['username'],
                                    ':ip' => $ip_address,
                                    ':user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
                                ]);
                                
                                // زيادة عداد المحاولات الفاشلة
                                $stmt = $pdo->prepare('UPDATE users SET login_attempts = login_attempts + 1, last_attempt = NOW() WHERE id = :id');
                                $stmt->execute([':id' => $user['id']]);
                                
                                // التحقق مما إذا كان يجب قفل الحساب
                                $stmt = $pdo->prepare('SELECT login_attempts FROM users WHERE id = :id');
                                $stmt->execute([':id' => $user['id']]);
                                $attempts = $stmt->fetchColumn();
                                
                                if ($attempts >= $max_attempts) {
                                    $lock_until = date('Y-m-d H:i:s', strtotime("+15 minutes"));
                                    $stmt = $pdo->prepare('UPDATE users SET locked_until = :lock_until WHERE id = :id');
                                    $stmt->execute([
                                        ':lock_until' => $lock_until,
                                        ':id' => $user['id']
                                    ]);
                                    
                                    $error_message = 'لقد تجاوزت الحد المسموح لمحاولات تسجيل الدخول. تم تعطيل حسابك مؤقتًا. يرجى المحاولة بعد 15 دقيقة.';
                                } else {
                                    $remaining_attempts = $max_attempts - $attempts;
                                    $error_message = 'اسم المستخدم/البريد الإلكتروني أو كلمة المرور غير صحيحة. لديك ' . $remaining_attempts . ' محاولات متبقية.';
                                }
                                
                                // إضافة تأخير للحد من هجمات القوة الغاشمة
                                sleep(2);
                            }
                        } else {
                            // المستخدم غير موجود
                            $stmt = $pdo->prepare('INSERT INTO login_attempts 
                                                 (username, ip_address, user_agent, success) 
                                                 VALUES (:username, :ip, :user_agent, 0)');
                            $stmt->execute([
                                ':username' => $username,
                                ':ip' => $ip_address,
                                ':user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
                            ]);
                            
                            $error_message = 'اسم المستخدم/البريد الإلكتروني أو كلمة المرور غير صحيحة';
                            
                            // إضافة تأخير للحد من هجمات القوة الغاشمة
                            sleep(2);
                        }
                    }
                } catch (PDOException $e) {
                    error_log('Database Error: ' . $e->getMessage());
                    $error_message = 'حدث خطأ أثناء معالجة طلبك. يرجى المحاولة مرة أخرى لاحقًا.';
                    http_response_code(500);
                }
            }
        }
    }
}    // عرض رسالة النجاح إذا كانت موجودة
        if (empty($error_message) && !empty($_SESSION['success_message'])) {
            $success_message = $_SESSION['success_message'];
            unset($_SESSION['success_message']);
        }
    }
?>
}

/**
 * تسجيل محاولة تسجيل الدخول
 */
function logLoginAttempt($pdo, $username, $ip, $userAgent, $success, $userId = null) {
    try {
        $stmt = $pdo->prepare("INSERT INTO login_attempts 
                              (user_id, username, ip_address, user_agent, success) 
                              VALUES (:user_id, :username, :ip, :user_agent, :success)");
        $stmt->execute([
            ':user_id' => $userId,
            ':username' => $username,
            ':ip' => $ip,
            ':user_agent' => $userAgent,
            ':success' => $success ? 1 : 0
        ]);

        // زيادة عداد المحاولات الفاشلة
        if (!$success) {
            $stmt = $pdo->prepare("UPDATE users 
                                  SET login_attempts = login_attempts + 1 
                                  WHERE username = :username OR email = :email");
            $stmt->execute([':username' => $username, ':email' => $username]);
        }
        return true;
    } catch (PDOException $e) {
        error_log('Error logging login attempt: ' . $e->getMessage());
        return false;
    }
}

/**
 * تعيين ملف تعريف الارتباط لتذكر بيانات المستخدم
 */
function setRememberMeCookie($pdo, $userId) {
    try {
        // إنشاء توكن فريد
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+30 days'));
        
        // حفظ التوكن في قاعدة البيانات
        $stmt = $pdo->prepare("INSERT INTO user_tokens 
                              (user_id, token, type, expires_at) 
                              VALUES (:user_id, :token, 'remember_me', :expires)");
        $result = $stmt->execute([
            ':user_id' => $userId,
            ':token' => password_hash($token, PASSWORD_DEFAULT),
            ':expires' => $expires
        ]);
        
        if ($result) {
            // تعيين ملف تعريف الارتباط
            $cookieValue = $userId . ':' . $token;
            $expireTime = time() + (30 * 24 * 60 * 60); // 30 يوم
            return setcookie(
                'remember_me',
                $cookieValue,
                $expireTime,
                '/',
                '',
                true,  // آمن (HTTPS فقط)
                true   // HTTP Only
            );
        }
        return false;
    } catch (Exception $e) {
        error_log('Error setting remember me cookie: ' . $e->getMessage());
        return false;
    }
}

/**
 * حذف ملفات تعريف الارتباط لتذكر بيانات المستخدم
 */
function clearRememberMe($pdo, $userId) {
    try {
        // حذف التوكنات القديمة من قاعدة البيانات
        $stmt = $pdo->prepare("DELETE FROM user_tokens 
                              WHERE user_id = :user_id AND type = 'remember_me'");
        $result = $stmt->execute([':user_id' => $userId]);
        
        // حذف ملف تعريف الارتباط
        setcookie('remember_me', '', time() - 3600, '/', '', true, true);
        return $result;
    } catch (Exception $e) {
        error_log('Error clearing remember me: ' . $e->getMessage());
        return false;
    }
}

/**
 * إعادة التوجيه بعد تسجيل الدخول
 */
function redirectAfterLogin() {
    $redirect = '/dashboard/';
    
    // إذا كان هناك رابط إعادة توجيه مسبق
    if (!empty($_SESSION['redirect_after_login'])) {
        $redirect = $_SESSION['redirect_after_login'];
        unset($_SESSION['redirect_after_login']);
    } 
    // إعادة التوجيه حسب صلاحيات المستخدم
    elseif (isset($_SESSION['role'])) {
        switch ($_SESSION['role']) {
            case 'admin':
                $redirect = '/admin/dashboard.php';
                break;
            case 'advertiser':
                $redirect = '/advertiser/dashboard.php';
                break;
            case 'user':
            default:
                $redirect = '/profile.php';
                break;
        }
    }
    
    // إعادة التوجيه مع منع التخزين المؤقت
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: 0');
    header('Location: ' . $redirect);
    exit();
}

// عرض رسالة النجاح إذا كانت موجودة
if (empty($error_message) && !empty($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

// إذا كان المستخدم مسجل الدخول بالفعل، قم بإعادة توجيهه
if (!empty($_SESSION['user_id'])) {
    $redirect = '/dashboard/';
    if ($_SESSION['role'] === 'advertiser') {
        $redirect = '/dashboard/advertiser_dashboard.php';
    } elseif ($_SESSION['role'] === 'admin') {
        $redirect = '/dashboard/admin/';
    }
    header('Location: ' . $redirect);
    exit;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="تسجيل الدخول إلى لوحة تحكم إبداع العرب">
    <meta name="author" content="إبداع العرب">
    <meta name="theme-color" content="#4a6fa5">
    <title>تسجيل الدخول - إبداع العرب</title>
    
    <!-- Favicon -->
    <link rel="icon" href="/assets/images/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/images/apple-touch-icon.png">
    
    <!-- CSS -->
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    
    <!-- Preload Critical CSS -->
    <link rel="preload" href="/css/critical.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="/css/critical.css"></noscript>
    
    <!-- Preload Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <style>
        /* CSS للشاشة الأولي */
        body {
            font-family: 'Tajawal', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #2c3e50;
            line-height: 1.6;
        }
        
        .auth-container {
            width: 100%;
            max-width: 420px;
            padding: 20px;
            margin: 20px auto;
        }
        
        .auth-box {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .auth-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
        }
        
        .auth-header {
            background: linear-gradient(135deg, #4a6fa5 0%, #3a5a80 100%);
            color: white;
            padding: 25px 30px;
            text-align: center;
            position: relative;
        }
        
        .auth-header h1 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 700;
        }
        
        .auth-body {
            padding: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #4a5568;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            font-family: 'Tajawal', sans-serif;
        }
        
        .form-control:focus {
            border-color: #4a6fa5;
            box-shadow: 0 0 0 3px rgba(74, 111, 165, 0.2);
            outline: none;
        }
        
        .input-with-icon {
            position: relative;
        }
        
        .input-with-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
        }
        
        .input-with-icon input {
            padding-right: 15px;
            padding-left: 45px;
        }
        
        .btn {
            display: inline-block;
            background: #4a6fa5;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            text-align: center;
            transition: all 0.3s ease;
            width: 100%;
            font-family: 'Tajawal', sans-serif;
        }
        
        .btn:hover {
            background: #3a5a80;
            transform: translateY(-2px);
        }
        
        .btn-block {
            display: block;
            width: 100%;
        }
        
        .auth-footer {
            text-align: center;
            margin-top: 20px;
            color: #718096;
            font-size: 0.9rem;
        }
        
        .auth-footer a {
            color: #4a6fa5;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        
        .auth-footer a:hover {
            color: #3a5a80;
            text-decoration: underline;
        }
        
        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
        
        .alert-danger {
            background-color: #fff5f5;
            color: #e53e3e;
            border: 1px solid #fed7d7;
        }
        
        .alert-success {
            background-color: #f0fff4;
            color: #2f855a;
            border: 1px solid #c6f6d5;
        }
        
        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 15px 0;
        }
        
        .remember-me {
            display: flex;
            align-items: center;
        }
        
        .remember-me input {
            margin-left: 8px;
            width: 18px;
            height: 18px;
        }
        
        .forgot-password a {
            color: #4a6fa5;
            text-decoration: none;
            font-size: 0.9rem;
        }
        
        .forgot-password a:hover {
            text-decoration: underline;
        }
        
        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 20px 0;
            color: #a0aec0;
            font-size: 0.9rem;
        }
        
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .divider::before {
            margin-left: 10px;
        }
        
        .divider::after {
            margin-right: 10px;
        }
        
        .social-login {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 20px;
        }
        
        .social-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: white;
            color: #4a5568;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .social-btn i {
            margin-left: 10px;
            font-size: 1.2rem;
        }
        
        .social-btn.google {
            border-color: #e53e3e;
            color: #e53e3e;
        }
        
        .social-btn.facebook {
            border-color: #3182ce;
            color: #3182ce;
        }
        
        .social-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        @media (max-width: 480px) {
            .auth-container {
                padding: 10px;
            }
            
            .auth-body {
                padding: 20px;
            }
            
            .auth-header h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <h1>مرحباً بعودتك!</h1>
                <p>من فضلك سجل الدخول للمتابعة إلى لوحة التحكم</p>
            </div>
            <div class="auth-body">
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>
                <div class="form-group">
                    <label for="email">البريد الإلكتروني</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">كلمة المرور</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn">تسجيل الدخول</button>
                </div>
            </form>
            
            <div class="auth-footer">
                <p>ليس لديك حساب؟ <a href="/register.php">إنشاء حساب جديد</a></p>
                <p><a href="/">العودة للصفحة الرئيسية</a></p>
            </div>
        </div>
    </div>
</body>
</html>
