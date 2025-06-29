<?php
// إعدادات قاعدة البيانات
$db_host = 'localhost'; // عادةً ما يكون localhost
$db_name = 'YOUR_DB_NAME';      // اسم قاعدة البيانات
$db_user = 'YOUR_DB_USER';      // اسم مستخدم قاعدة البيانات
$db_pass = 'YOUR_DB_PASS';      // كلمة مرور قاعدة البيانات

try {
    // إنشاء اتصال PDO
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // تعيين المنطقة الزمنية
    date_default_timezone_set('Asia/Riyadh');
    
} catch(PDOException $e) {
    // في حالة حدوث خطأ في الاتصال
    die("خطأ في الاتصال بقاعدة البيانات: " . $e->getMessage());
}

// إعدادات الموقع
$site_name = "إبداع العرب";
$site_url = "https://ebdaa.shop/"; // استبدل باسم نطاقك
$admin_email = "admin@ebdaa.shop"; // استبدل ببريدك الإلكتروني

// بدء الجلسة إذا لم تبدأ
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// دالة لإعادة التوجيه
function redirect($url) {
    header("Location: $url");
    exit();
}

// دالة للتحقق من تسجيل الدخول
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// دالة للتحقق من صلاحيات المشرف
function isAdmin() {
    return (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin');
}

// دالة للتحقق من صلاحيات المعلن
function isAdvertiser() {
    return (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'advertiser');
}
?>
