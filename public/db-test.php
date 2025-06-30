<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Load environment variables
$envFile = __DIR__ . '/../.env';
$env = [];

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value, " \t\n\r\0\x0B\"'");
        
        if (!empty($name)) {
            $env[$name] = $value;
            putenv("$name=$value");
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

// Get database credentials
$dbHost = $env['DB_HOST'] ?? getenv('DB_HOST');
$dbName = $env['DB_NAME'] ?? getenv('DB_NAME');
$dbUser = $env['DB_USER'] ?? getenv('DB_USER');
$dbPass = $env['DB_PASS'] ?? getenv('DB_PASS');

// Test database connection
try {
    $dsn = "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
    
    // Get database info
    $version = $pdo->query('SELECT VERSION() as version')->fetch()['version'];
    $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    
    // Display results
    echo "<h2>✅ تم الاتصال بقاعدة البيانات بنجاح</h2>";
    echo "<p>إصدار MySQL: $version</p>";
    echo "<p>قاعدة البيانات: $dbName</p>";
    echo "<p>عدد الجداول: " . count($tables) . "</p>";
    
} catch (PDOException $e) {
    echo "<h2>❌ فشل الاتصال بقاعدة البيانات</h2>";
    echo "<p>الخطأ: " . $e->getMessage() . "</p>";
    echo "<h3>تفاصيل الاتصال:</h3>";
    echo "<pre>";
    echo "Host: " . htmlspecialchars($dbHost) . "\n";
    echo "Database: " . htmlspecialchars($dbName) . "\n";
    echo "User: " . htmlspecialchars($dbUser) . "\n";
    echo "Password: " . (!empty($dbPass) ? '*****' : 'غير محدد') . "\n";
    echo "</pre>";
}
