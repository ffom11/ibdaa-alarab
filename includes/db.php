<?php
/**
 * ملف اتصال قاعدة البيانات
 * يستخدم PDO للاتصال بقاعدة البيانات مع معالجة الأخطاء المناسبة
 */

// تحميل ملف التكوين
require_once __DIR__ . '/../config/config.php';

// إعدادات PDO
$pdoOptions = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET . " COLLATE utf8mb4_unicode_ci",
    PDO::ATTR_PERSISTENT         => false,
    PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false
];

try {
    // إنشاء اتصال PDO
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $pdoOptions);
    
    // تعيين السمة ATTR_ERRMODE لتقارير الأخطاء
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    // تسجيل الخطأ في ملف السجل
    log_message('ERROR', 'فشل الاتصال بقاعدة البيانات: ' . $e->getMessage(), [
        'code' => $e->getCode(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
    
    // رسالة خطأ صديقة للمستخدم
    if (!headers_sent()) {
        header('HTTP/1.1 503 Service Unavailable');
        header('Content-Type: text/html; charset=utf-8');
    }
    
    $errorDetails = ENVIRONMENT === 'development' ? $e->getMessage() : '';
    
    die('<!DOCTYPE html>
    <html lang="ar" dir="rtl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>خطأ في الاتصال - ' . SITE_NAME . '</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; margin: 0; padding: 20px; background-color: #f8f9fa; }
            .error-container { max-width: 800px; margin: 50px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; }
            h1 { color: #dc3545; margin-top: 0; }
            .error-details { margin-top: 20px; padding: 15px; background: #f8d7da; color: #721c24; border-radius: 4px; text-align: right; display: ' . (ENVIRONMENT === 'development' ? 'block' : 'none') . '; }
            .contact { margin-top: 20px; color: #6c757d; font-size: 0.9em; }
        </style>
    </head>
    <body>
        <div class="error-container">
            <h1>عذراً، حدث خطأ في الاتصال بقاعدة البيانات</h1>
            <p>نواجه حالياً مشكلة فنية. يرجى المحاولة مرة أخرى لاحقاً.</p>
            <div class="error-details">' . htmlspecialchars($errorDetails) . '</div>
            <div class="contact">
                <p>إذا استمرت المشكلة، يرجى الاتصال بفريق الدعم على: <a href="mailto:' . ADMIN_EMAIL . '">' . ADMIN_EMAIL . '</a></p>
            </div>
            <p><a href="/">العودة للصفحة الرئيسية</a></p>
        </div>
    </body>
    </html>');
}

/**
 * دالة لتنفيذ استعلامات SQL مع معلمات آمنة
 *
 * @param string $sql استعلام SQL
 * @param array $params معلمات الاستعلام
 * @return PDOStatement كائن النتيجة
 */
function query($sql, $params = []) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        log_message('ERROR', 'فشل تنفيذ الاستعلام: ' . $e->getMessage(), [
            'sql' => $sql,
            'params' => $params,
            'error' => $e->getMessage()
        ]);
        throw $e;
    }
}

/**
 * دالة لجلب صف واحد من نتيجة الاستعلام
 *
 * @param string $sql استعلام SQL
 * @param array $params معلمات الاستعلام
 * @return array|false مصفوفة تحتوي على الصف أو false إذا لم يتم العثور على نتائج
 */
function fetch($sql, $params = []) {
    $stmt = query($sql, $params);
    return $stmt->fetch();
}

/**
 * دالة لجلب كل الصفوف من نتيجة الاستعلام
 *
 * @param string $sql استعلام SQL
 * @param array $params معلمات الاستعلام
 * @return array مصفوفة تحتوي على جميع الصفوف
 */
function fetchAll($sql, $params = []) {
    $stmt = query($sql, $params);
    return $stmt->fetchAll();
}

/**
 * دالة لإدخال سجل جديد في الجدول
 *
 * @param string $table اسم الجدول
 * @param array $data مصفوفة تحتوي على البيانات (الحقل => القيمة)
 * @return int معرف السجل المضاف
 */
function insert($table, $data) {
    global $pdo;
    
    $fields = array_keys($data);
    $values = array_values($data);
    $placeholders = array_fill(0, count($fields), '?');
    
    $sql = "INSERT INTO `$table` (`" . implode('`, `', $fields) . "`) VALUES (" . implode(', ', $placeholders) . ")";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($values);
        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        log_message('ERROR', 'فشل إدراج البيانات: ' . $e->getMessage(), [
            'table' => $table,
            'data' => $data,
            'error' => $e->getMessage()
        ]);
        throw $e;
    }
}

/**
 * دالة لتحديث سجل في الجدول
 *
 * @param string $table اسم الجدول
 * @param array $data مصفوفة تحتوي على البيانات (الحقل => القيمة)
 * @param string $where شرط WHERE
 * @param array $params معلمات شرط WHERE
 * @return int عدد الصفوف المتأثرة
 */
function update($table, $data, $where, $params = []) {
    global $pdo;
    
    $set = [];
    $values = [];
    
    foreach ($data as $field => $value) {
        $set[] = "`$field` = ?";
        $values[] = $value;
    }
    
    $sql = "UPDATE `$table` SET " . implode(', ', $set) . " WHERE $where";
    $values = array_merge($values, $params);
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($values);
        return $stmt->rowCount();
    } catch (PDOException $e) {
        log_message('ERROR', 'فشل تحديث البيانات: ' . $e->getMessage(), [
            'table' => $table,
            'data' => $data,
            'where' => $where,
            'params' => $params,
            'error' => $e->getMessage()
        ]);
        throw $e;
    }
}

/**
 * دالة لحذف سجل من الجدول
 *
 * @param string $table اسم الجدول
 * @param string $where شرط WHERE
 * @param array $params معلمات شرط WHERE
 * @return int عدد الصفوف المحذوفة
 */
function delete($table, $where, $params = []) {
    global $pdo;
    
    $sql = "DELETE FROM `$table` WHERE $where";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    } catch (PDOException $e) {
        log_message('ERROR', 'فشل حذف البيانات: ' . $e->getMessage(), [
            'table' => $table,
            'where' => $where,
            'params' => $params,
            'error' => $e->getMessage()
        ]);
        throw $e;
    }
}
