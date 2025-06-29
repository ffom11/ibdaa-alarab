<?php
// register.php: معالجة إنشاء الحساب الجديد

try {
    // تمكين عرض الأخطاء للأغراض التنموية
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    // التحقق من وجود ملف db.php
    $dbFile = __DIR__ . '/includes/db.php';
    if (!file_exists($dbFile)) {
        throw new Exception('ملف قاعدة البيانات غير موجود');
    }
    
    require_once $dbFile;

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: /register.html');
        exit;
    }

    // التحقق من توفر جميع الحقول المطلوبة
    $required_fields = ['name', 'email', 'password', 'confirm_password', 'role'];
    $missing_fields = [];
    
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $missing_fields[] = $field;
        }
    }
    
    if (!empty($missing_fields)) {
        header('Location: /register.html?error=جميع الحقول مطلوبة: ' . implode(', ', $missing_fields));
        exit;
    }
    
    // تنظيف المدخلات
    $name = trim(htmlspecialchars($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = in_array($_POST['role'], ['advertiser', 'client']) ? $_POST['role'] : 'client';
    
    // التحقق من صحة البريد الإلكتروني
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: /register.html?error=البريد الإلكتروني غير صالح');
        exit;
    }
    
    // التحقق من تطابق كلمتي المرور
    if ($password !== $confirm_password) {
        header('Location: /register.html?error=كلمتا المرور غير متطابقتين');
        exit;
    }
    
    // التحقق من قوة كلمة المرور
    if (strlen($password) < 6) {
        header('Location: /register.html?error=كلمة المرور يجب أن تكون 6 أحرف على الأقل');
        exit;
    }
    
    // التحقق من عدم استخدام البريد الإلكتروني مسبقاً
    try {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            header('Location: /register.html?error=البريد الإلكتروني مستخدم بالفعل');
            exit;
        }
    } catch (PDOException $e) {
        error_log('خطأ في التحقق من البريد الإلكتروني: ' . $e->getMessage());
        header('Location: /register.html?error=حدث خطأ في التحقق من البيانات');
        exit;
    }
    
    // تشفير كلمة المرور
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    
    // إدخال المستخدم الجديد
    try {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
        $stmt->execute([$name, $email, $hashed, $role]);
        
        // بدء الجلسة
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION['success_message'] = 'تم إنشاء الحساب بنجاح! يمكنك تسجيل الدخول الآن.';
        header('Location: /login.php');
        exit;
        
    } catch (PDOException $e) {
        error_log('خطأ في إنشاء الحساب: ' . $e->getMessage());
        header('Location: /register.html?error=حدث خطأ أثناء إنشاء الحساب');
        exit;
    }
    
} catch (Exception $e) {
    // تسجيل الخطأ في سجل الأخطاء
    error_log('خطأ في ملف register.php: ' . $e->getMessage());
    
    // إعادة توجيه المستخدم مع رسالة خطأ
    header('Location: /register.html?error=حدث خطأ غير متوقع. يرجى المحاولة مرة أخرى لاحقاً.');
    exit;
}
