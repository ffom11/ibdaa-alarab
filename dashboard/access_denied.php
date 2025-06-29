<?php
require_once __DIR__ . '/../includes/auth_check.php';
$page_title = 'تم رفض الوصول';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - لوحة التحكم</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3f2fbb;
            --danger-color: #dc3545;
            --light-gray: #f8f9fa;
            --dark-gray: #343a40;
            --border-radius: 8px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Tajawal', sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .error-container {
            text-align: center;
            max-width: 500px;
            width: 100%;
            padding: 40px;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .error-icon {
            font-size: 5rem;
            color: var(--danger-color);
            margin-bottom: 20px;
        }
        
        h1 {
            color: var(--danger-color);
            margin-bottom: 15px;
        }
        
        p {
            margin-bottom: 25px;
            color: #666;
            font-size: 1.1rem;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            padding: 10px 20px;
            background: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: var(--border-radius);
            transition: all 0.3s;
        }
        
        .btn i {
            margin-left: 8px;
        }
        
        .btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }
        
        .btn-outline {
            background: transparent;
            border: 1px solid var(--primary-color);
            color: var(--primary-color);
            margin-right: 10px;
        }
        
        .btn-outline:hover {
            background: var(--primary-color);
            color: white;
        }
        
        .btn-group {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="fas fa-ban"></i>
        </div>
        <h1>تم رفض الوصول</h1>
        <p>عذراً، ليس لديك الصلاحيات الكافية للوصول إلى هذه الصفحة.</p>
        <p>إذا كنت تعتقد أن هذا خطأ، يرجى التواصل مع مدير النظام.</p>
        
        <div class="btn-group">
            <a href="/dashboard/" class="btn btn-outline">
                <i class="fas fa-home"></i> العودة للرئيسية
            </a>
            <a href="/contact.php" class="btn">
                <i class="fas fa-headset"></i> تواصل مع الدعم
            </a>
        </div>
    </div>
</body>
</html>
