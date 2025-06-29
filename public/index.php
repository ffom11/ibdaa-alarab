<?php
// Simple welcome page
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إبداع العرب - الصفحة الرئيسية</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
            --success-color: #27ae60;
            --text-color: #333;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Tajawal', sans-serif;
        }
        
        body {
            background-color: #f9f9f9;
            color: var(--text-color);
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* الترويسة */
        header {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }
        
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
        }
        
        .logo {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .nav-links {
            display: flex;
            list-style: none;
        }
        
        .nav-links li {
            margin-right: 25px;
        }
        
        .nav-links a {
            text-decoration: none;
            color: var(--dark-color);
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .nav-links a:hover {
            color: var(--secondary-color);
        }
        
        .cta-button {
            background-color: var(--secondary-color);
            color: white !important;
            padding: 8px 20px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        
        .cta-button:hover {
            background-color: #c0392b;
        }
        
        /* القسم الرئيسي */
        .hero {
            height: 100vh;
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding-top: 80px;
        }
        
        .hero-content {
            max-width: 600px;
        }
        
        .hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
            color: var(--primary-color);
        }
        
        .hero p {
            font-size: 18px;
            margin-bottom: 30px;
            color: #555;
        }
        
        /* الخدمات */
        .services {
            padding: 80px 0;
            background-color: white;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 50px;
        }
        
        .section-title h2 {
            font-size: 36px;
            color: var(--primary-color);
            margin-bottom: 15px;
        }
        
        .section-title p {
            color: #777;
            max-width: 700px;
            margin: 0 auto;
        }
        
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
        
        .service-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s;
        }
        
        .service-card:hover {
            transform: translateY(-10px);
        }
        
        .service-icon {
            font-size: 40px;
            color: var(--secondary-color);
            margin-bottom: 20px;
        }
        
        .service-card h3 {
            font-size: 22px;
            margin-bottom: 15px;
            color: var(--primary-color);
        }
        
        /* التذييل */
        footer {
            background-color: var(--primary-color);
            color: white;
            padding: 50px 0 20px;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .footer-section h3 {
            font-size: 20px;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 10px;
        }
        
        .footer-section h3::after {
            content: '';
            position: absolute;
            right: 0;
            bottom: 0;
            width: 50px;
            height: 2px;
            background-color: var(--secondary-color);
        }
        
        .footer-section p, .footer-section a {
            color: #ddd;
            margin-bottom: 10px;
            display: block;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer-section a:hover {
            color: var(--secondary-color);
        }
        
        .social-links a {
            display: inline-block;
            margin-left: 15px;
            font-size: 20px;
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
            margin-top: 30px;
        }
        
        /* التجاوب */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }
            
            .hero h1 {
                font-size: 36px;
            }
            
            .hero p {
                font-size: 16px;
            }
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
