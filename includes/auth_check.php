<?php
/**
 * ملف التحقق من المصادقة والصلاحيات
 * يحتوي على دوال للتحقق من صلاحيات المستخدمين والتحكم في الوصول
 */

// تحميل ملفات التبعية
require_once __DIR__ . '/db.php';

// بدء الجلسة إذا لم تكن قد بدأت
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * التحقق من تسجيل الدخول
 * 
 * @param bool $redirect إذا كان true، سيتم إعادة التوجيه إلى صفحة تسجيل الدخول
 * @return bool true إذا كان المستخدم مسجل الدخول، false بخلاف ذلك
 */
function isLoggedIn($redirect = true) {
    $isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    
    if (!$isLoggedIn && $redirect) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        $_SESSION['error_message'] = 'يجب تسجيل الدخول للوصول إلى هذه الصفحة';
        header('Location: /login.php');
        exit;
    }
    
    return $isLoggedIn;
}

/**
 * التحقق من أن المستخدم مدير
 * 
 * @param bool $redirect إذا كان true، سيتم إعادة التوجيه إلى صفحة رفض الوصول
 * @return bool true إذا كان المستخدم مديراً، false بخلاف ذلك
 */
function isAdmin($redirect = true) {
    $isAdmin = isLoggedIn($redirect) && !empty($_SESSION['is_admin']);
    
    if (!$isAdmin && $redirect) {
        $_SESSION['error_message'] = 'ليست لديك صلاحية الوصول إلى هذه الصفحة';
        header('Location: /dashboard/access_denied.php');
        exit;
    }
    
    return $isAdmin;
}

/**
 * التحقق من صلاحية إدارة الخدمات
 * 
 * @param bool $redirect إذا كان true، سيتم إعادة التوجيه إلى صفحة رفض الوصول
 * @return bool true إذا كان المستخدم لديه الصلاحية، false بخلاف ذلك
 */
function canManageServices($redirect = true) {
    $canManage = isLoggedIn($redirect) && 
                (!empty($_SESSION['can_manage_services']) || !empty($_SESSION['is_admin']));
    
    if (!$canManage && $redirect) {
        $_SESSION['error_message'] = 'ليست لديك صلاحية إدارة الخدمات';
        header('Location: /dashboard/access_denied.php');
        exit;
    }
    
    return $canManage;
}

/**
 * التحقق من صلاحية إدارة الأسعار
 * 
 * @param bool $redirect إذا كان true، سيتم إعادة التوجيه إلى صفحة رفض الوصول
 * @return bool true إذا كان المستخدم لديه الصلاحية، false بخلاف ذلك
 */
function canManagePrices($redirect = true) {
    $canManage = isLoggedIn($redirect) && 
                (!empty($_SESSION['can_manage_prices']) || !empty($_SESSION['is_admin']));
    
    if (!$canManage && $redirect) {
        $_SESSION['error_message'] = 'ليست لديك صلاحية إدارة الأسعار';
        header('Location: /dashboard/access_denied.php');
        exit;
    }
    
    return $canManage;
}

/**
 * التحقق من أن المستخدم هو صاحب الحساب أو مدير
 * 
 * @param int $userId معرف المستخدم المراد التحقق منه
 * @return bool true إذا كان المستخدم هو صاحب الحساب أو مدير
 */
function isOwnerOrAdmin($userId) {
    return isLoggedIn() && ($_SESSION['user_id'] == $userId || !empty($_SESSION['is_admin']));
}

/**
 * الحصول على معلومات المستخدم الحالي
 * 
 * @param string $field حقل معين من معلومات المستخدم (اختياري)
 * @return mixed معلومات المستخدم أو الحقل المطلوب
 */
function currentUser($field = null) {
    if (!isLoggedIn(false)) {
        return null;
    }
    
    static $user = null;
    
    if ($user === null) {
        global $pdo;
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
        } catch (PDOException $e) {
            log_message('ERROR', 'فشل تحميل بيانات المستخدم: ' . $e->getMessage());
            return null;
        }
    }
    
    if ($field !== null) {
        return $user[$field] ?? null;
    }
    
    return $user;
}

/**
 * تحديث بيانات المستخدم في الجلسة
 * 
 * @param array $data بيانات المستخدم الجديدة
 * @return bool true إذا تم التحديث بنجاح
 */
function updateSessionUser($data) {
    if (!isLoggedIn(false)) {
        return false;
    }
    
    foreach ($data as $key => $value) {
        if (in_array($key, ['id', 'username', 'email', 'name', 'role', 'is_admin', 'can_manage_services', 'can_manage_prices'])) {
            $_SESSION[$key] = $value;
        }
    }
    
    return true;
}

/**
 * تسجيل دخول المستخدم
 * 
 * @param array $user بيانات المستخدم
 * @return bool true إذا تم تسجيل الدخول بنجاح
 */
function loginUser($user) {
    // تدمير الجلسة الحالية
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_unset();
        session_destroy();
    }
    
    // بدء جلسة جديدة
    session_start();
    
    // تعيين بيانات الجلسة
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'] ?? '';
    $_SESSION['email'] = $user['email'];
    $_SESSION['name'] = $user['name'] ?? '';
    $_SESSION['role'] = $user['role'] ?? 'user';
    $_SESSION['is_admin'] = !empty($user['is_admin']);
    $_SESSION['can_manage_services'] = !empty($user['can_manage_services']);
    $_SESSION['can_manage_prices'] = !empty($user['can_manage_prices']);
    $_SESSION['last_activity'] = time();
    
    // تحديث وقت آخر تسجيل دخول
    try {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        $stmt->execute([$user['id']]);
    } catch (PDOException $e) {
        log_message('ERROR', 'فشل تحديث وقت آخر تسجيل دخول: ' . $e->getMessage());
    }
    
    return true;
}

/**
 * تسجيل خروج المستخدم
 * 
 * @return bool true إذا تم تسجيل الخروج بنجاح
 */
function logoutUser() {
    // تسجيل حدث تسجيل الخروج
    if (isset($_SESSION['user_id'])) {
        log_message('INFO', 'تم تسجيل خروج المستخدم', ['user_id' => $_SESSION['user_id']]);
    }
    
    // تدمير الجلسة
    $_SESSION = [];
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], 
            $params["domain"],
            $params["secure"], 
            $params["httponly"]
        );
    }
    
    session_destroy();
    
    return true;
}

// دوال توافقية مع الإصدارات السابقة
function requireLogin() { return isLoggedIn(); }
function requireAdmin() { return isAdmin(); }

// تعيين بيانات المستخدم في الجلسة بعد تسجيل الدخول
function setUserSession($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['is_admin'] = $user['is_admin'] ?? 0;
    $_SESSION['can_manage_services'] = $user['can_manage_services'] ?? 0;
    $_SESSION['can_manage_prices'] = $user['can_manage_prices'] ?? 0;
    
    // تحديث وقت آخر نشاط
    $_SESSION['last_activity'] = time();
}

// التحقق من انتهاء الجلسة
function checkSessionTimeout() {
    $timeout_duration = 1800; // 30 دقيقة من عدم النشاط
    
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
        // انتهت الجلسة بسبب عدم النشاط
        session_unset();
        session_destroy();
        header('Location: /login.php?error=session_expired');
        exit;
    }
    
    // تحديث وقت آخر نشاط
    $_SESSION['last_activity'] = time();
}

// التحقق من أن المستخدم مسجل دخوله
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// التحقق من أن المستخدم مدير
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
}

// الحصول على معرف المستخدم الحالي
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

// تسجيل الخروج
function logout() {
    // إلغاء جميع متغيرات الجلسة
    $_SESSION = array();
    
    // إذا تم تعيين كوكي الجلسة، قم بحذفه
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // تدمير الجلسة
    session_destroy();
    
    // إعادة التوجيه إلى صفحة تسجيل الدخول
    header('Location: /login.php');
    exit;
}

// التحقق من الصلاحيات المطلوبة
function requirePermission($permission) {
    switch ($permission) {
        case 'admin':
            return requireAdmin();
        case 'manage_services':
            return canManageServices();
        case 'manage_prices':
            return canManagePrices();
        default:
            return requireLogin();
    }
}

// التحقق من أن المستخدم الحالي هو صاحب المحتوى أو لديه صلاحيات إدارية
function isOwnerOrAdmin($ownerId) {
    if (!isLoggedIn()) {
        return false;
    }
    
    $currentUserId = getCurrentUserId();
    return ($currentUserId == $ownerId || isAdmin());
}
?>
