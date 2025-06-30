<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Set content type and encoding
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>متطلبات النظام - إبداع العرب</title>
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
        .requirement {
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
        <h1>فحص متطلبات النظام</h1>
        
        <h2>إصدار PHP</h2>
        <?php
        $phpVersion = phpversion();
        $phpCheck = version_compare($phpVersion, '8.0.0', '>=');
        $phpClass = $phpCheck ? 'success' : 'error';
        ?>
        <div class="requirement <?php echo $phpClass; ?>">
            <span>PHP 8.0+ (المثبت: <?php echo $phpVersion; ?>)</span>
            <span class="status"><?php echo $phpCheck ? '✅' : '❌'; ?></span>
        </div>

        <h2>إضافات PHP المطلوبة</h2>
        <?php
        $extensions = [
            'pdo' => 'PDO',
            'pdo_mysql' => 'PDO_MySQL',
            'openssl' => 'OpenSSL',
            'json' => 'JSON',
            'mbstring' => 'Multibyte String',
            'fileinfo' => 'Fileinfo',
            'curl' => 'cURL',
            'gd' => 'GD Library',
            'zip' => 'ZIP',
            'intl' => 'Internationalization (intl)'
        ];

        foreach ($extensions as $ext => $name) {
            $loaded = extension_loaded($ext);
            $class = $loaded ? 'success' : 'error';
            echo "<div class='requirement $class'>";
            echo "<span>$name ($ext)</span>";
            echo "<span class='status'>" . ($loaded ? '✅' : '❌') . "</span>";
            echo "</div>";
        }
        ?>

        <h2>صلاحيات المجلدات</h2>
        <?php
        $directories = [
            '/storage' => 'قابل للكتابة (755)',
            '/storage/framework' => 'قابل للكتابة (755)',
            '/storage/logs' => 'قابل للكتابة (755)',
            '/bootstrap/cache' => 'قابل للكتابة (755)',
        ];

        foreach ($directories as $dir => $description) {
            $path = __DIR__ . '/..' . $dir;
            $exists = file_exists($path);
            $writable = is_writable($path);
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
            
            echo "<div class='requirement $class'>";
            echo "<span>$dir ($description) - الصلاحيات: $perms</span>";
            echo "<span class='status'>$status</span>";
            echo "</div>";
        }
        ?>

        <h2>إعدادات PHP المهمة</h2>
        <?php
        $settings = [
            'memory_limit' => ['المستحسن: 128M+', '128M'],
            'upload_max_filesize' => ['المستحسن: 20M+', '20M'],
            'post_max_size' => ['يجب أن يكون أكبر من upload_max_filesize', '20M'],
            'max_execution_time' => ['المستحسن: 120+', '120'],
            'date.timezone' => ['يجب تعيين المنطقة الزمنية', '']
        ];

        foreach ($settings as $setting => $info) {
            $value = ini_get($setting);
            $recommended = $info[0];
            $minValue = $info[1] ?? null;
            
            $class = 'success';
            $status = '✅';
            
            if ($setting === 'date.timezone') {
                if (empty($value)) {
                    $class = 'error';
                    $status = '❌';
                }
            } elseif ($minValue) {
                $current = (int)$value;
                $min = (int)$minValue;
                
                if ($current < $min) {
                    $class = 'warning';
                    $status = '⚠️';
                }
            }
            
            echo "<div class='requirement $class'>";
            echo "<span>$setting: $value <small>($recommended)</small></span>";
            echo "<span class='status'>$status</span>";
            echo "</div>";
        }
        ?>
        
        <h2>معلومات إضافية</h2>
        <div class="requirement">
            <span>نظام التشغيل:</span>
            <span><?php echo php_uname(); ?></span>
        </div>
        <div class="requirement">
            <span>واجهة خادم الويب:</span>
            <span><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'غير معروف'; ?></span>
        </div>
        <div class="requirement">
            <span>المسار الجذري:</span>
            <span><?php echo __DIR__; ?></span>
        </div>
    </div>
</body>
</html>
