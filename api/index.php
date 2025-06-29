<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// معالجة طلبات OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = trim($path, '/');
$path_parts = explode('/', $path);
$endpoint = $path_parts[count($path_parts) - 1];

// معالجة طلبات النماذج
if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if ($endpoint === 'contact') {
        // معالجة نموذج الاتصال
        $response = [
            'success' => true,
            'message' => 'تم استلام رسالتك بنجاح! سنتواصل معك قريباً.'
        ];
        
        // هنا يمكنك إضافة إرسال البريد الإلكتروني أو حفظ البيانات في قاعدة البيانات
        
        echo json_encode($response);
        exit();
    }
    
    if ($endpoint === 'subscribe') {
        // معالجة الاشتراك في النشرة البريدية
        $response = [
            'success' => true,
            'message' => 'شكراً لاشتراكك في نشرتنا البريدية!'
        ];
        
        echo json_encode($response);
        exit();
    }
}

// إذا لم يتم التعرف على الطلب
http_response_code(404);
echo json_encode(['error' => 'Not Found']);
?>
