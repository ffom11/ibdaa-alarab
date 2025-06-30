<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>فحص الصلاحيات - إبداع العرب</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            margin: 20px;
            background-color: #f9f9f9;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1, h2 {
            color: #2c3e50;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .check {
            padding: 10px;
            margin: 5px 0;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .success {
            background-color: #e8f8f0;
            border-right: 4px solid #2ecc71;
        }
        .error {
            background-color: #fde8e8;
            border-right: 4px solid #e74c3c;
        }
        .warning {
            background-color: #fef9e7;
            border-right: 4px solid #f1c40f;
        }
        .status {
            font-weight: bold;
        }
        .success .status { color: #27ae60; }
        .error .status { color: #e74c3c; }
        .warning .status { color: #f39c12; }
    </style>
</head>
<body>
    <div class="container">
        <h1>فحص صلاحيات الملفات والمجلدات</h1>
        
        <h2>المجلدات الأساسية</h2>
        <?php
        $baseDir = dirname(__DIR__);
        $directories = [
            '/storage' => 'يجب أن يكون قابلًا للكتابة (775)',
            '/storage/logs' => 'يجب أن يكون قابلًا للكتابة (775)',
            '/bootstrap/cache' => 'يجب أن يكون قابلًا للكتابة (775)',
            '/public' => 'يجب أن يكون قابلًا للقراءة (755)',
        ];

        foreach ($directories as $dir => $description) {
            $path = $baseDir . $dir;
            $exists = file_exists($path);
            $writable = is_writable($path);
            $readable = is_readable($path);
            $perms = $exists ? substr(sprintf('%o', fileperms($path)), -4) : '0000';
            
            $class = 'error';
            $status = '❌';
            
            if ($exists && $writable) {
                $class = 'success';
                $status = '✅';
            } elseif ($exists && !$writable) {
                $class = 'warning';
                $status = '⚠️';
            }
            
            echo "<div class='check $class'>";
            echo "<span>$dir ($description) - الصلاحيات: $perms</span>";
            echo "<span class='status'>$status</span>";
            echo "</div>";
        }
        ?>

        <h2>الملفات المهمة</h2>
        <?php
        $files = [
            '/.env' => 'يجب أن يكون قابلًا للقراءة (644)',
            '/public/index.php' => 'يجب أن يكون قابلًا للتنفيذ (755)',
        ];

        foreach ($files as $file => $description) {
            $path = $baseDir . $file;
            $exists = file_exists($path);
            $readable = $exists ? is_readable($path) : false;
            $perms = $exists ? substr(sprintf('%o', fileperms($path)), -4) : '0000';
            
            $class = 'error';
            $status = '❌';
            
            if ($exists && $readable) {
                $class = 'success';
                $status = '✅';
            } elseif ($exists && !$readable) {
                $class = 'warning';
                $status = '⚠️';
            }
            
            echo "<div class='check $class'>";
            echo "<span>$file ($description) - الصلاحيات: $perms</span>";
            echo "<span class='status'>$status</span>";
            echo "</div>";
        }
        ?>

        <h2>معلومات النظام</h2>
        <div class="check">
            <span>نظام التشغيل:</span>
            <span><?php echo php_uname(); ?></span>
        </div>
        <div class="check">
            <span>إصدار PHP:</span>
            <span><?php echo phpversion(); ?></span>
        </div>
        <div class="check">
            <span>واجهة خادم الويب:</span>
            <span><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'غير معروف'; ?></span>
        </div>
        <div class="check">
            <span>المسار الجذري:</span>
            <span><?php echo $baseDir; ?></span>
        </div>
    </div>
</body>
</html>
