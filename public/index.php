<?php
// Simple welcome page
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إبداع العرب - الصفحة الرئيسية</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
            text-align: center;
        }
        .status {
            background: #e8f4f8;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-right: 4px solid #3498db;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>مرحباً بك في موقع إبداع العرب</h1>
        
        <div class="status">
            <h2>حالة التطبيق: ✅ يعمل بنجاح</h2>
            <p>تم نشر التطبيق بنجاح على Render.</p>
            <p>إصدار PHP: <?php echo phpversion(); ?></p>
        </div>

        <h2>الروابط المهمة:</h2>
        <ul>
            <li><a href="/login.php">تسجيل الدخول</a></li>
            <li><a href="/register.php">إنشاء حساب جديد</a></li>
        </ul>

        <h2>معلومات الخادم:</h2>
        <ul>
            <li>نظام التشغيل: <?php echo php_uname('s'); ?></li>
            <li>إصدار PHP: <?php echo phpversion(); ?></li>
            <li>المنفذ: <?php echo $_SERVER['SERVER_PORT']; ?></li>
        </ul>
    </div>
</body>
</html>
