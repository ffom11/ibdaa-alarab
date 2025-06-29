<?php
// نموذج طلب خدمة معينة من معلن
require_once '../includes/db.php';
$service_id = isset($_GET['service_id']) ? (int)$_GET['service_id'] : 0;
if ($service_id <= 0) die('خدمة غير محددة.');
$stmt = $pdo->prepare("SELECT s.id, s.title, s.description, s.category, u.name as adv_name, u.id as adv_id FROM services s INNER JOIN users u ON s.user_id = u.id WHERE s.id=?");
$stmt->execute([$service_id]);
$service = $stmt->fetch();
if (!$service) die('الخدمة غير موجودة.');
$success = false;
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_name = trim($_POST['client_name']);
    $client_email = trim($_POST['client_email']);
    $details = trim($_POST['details']);
    if ($client_name && $client_email && $details) {
        // حفظ الطلب في قاعدة البيانات (جدول requests)
        $stmt2 = $pdo->prepare("INSERT INTO requests (service_id, advertiser_id, client_name, client_email, details, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt2->execute([$service_id, $service['adv_id'], $client_name, $client_email, $details]);
        $success = true;
    } else {
        $error = 'جميع الحقول مطلوبة';
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>طلب خدمة | إبداع العرب</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .request-form { max-width: 430px; margin: 40px auto; background: #fff; border-radius: 13px; box-shadow: 0 2px 16px #3f2fbb22; padding: 30px 27px; }
        .request-form h2 { color: #3f2fbb; text-align: center; margin-bottom: 20px; }
        .form-group { margin-bottom: 16px; }
        label { display: block; margin-bottom: 6px; color: #5b0f4f; font-weight: bold; }
        input, textarea { width: 100%; padding: 10px 12px; border-radius: 8px; border: 1.5px solid #bcbcbc; font-size: 1rem; background: #f9f9f9; }
        textarea { min-height: 70px; resize: vertical; }
        .btn-send { background: linear-gradient(90deg, #5b0f4f 0%, #3f2fbb 100%); color: #fff; border: none; border-radius: 8px; padding: 12px 35px; font-size: 1.1rem; font-weight: bold; cursor: pointer; width: 100%; }
        .btn-send:hover { background: #dd5217; }
        .success-message, .error-message { padding: 10px; border-radius: 7px; margin-bottom: 13px; text-align: center; font-weight: bold; }
        .success-message { background: #eaffdd; color: #257c00; }
        .error-message { background: #ffdddd; color: #b70000; }
        .service-summary { background:#f7f7ff; border-radius:8px; padding:13px 14px; margin-bottom:18px; }
    </style>
</head>
<body>
    <div class="request-form">
        <div class="service-summary">
            <div style="font-weight:bold;color:#3f2fbb;"> <?= htmlspecialchars($service['title']); ?> </div>
            <?php if (!empty($service['category'])): ?>
                <div style="color:#5b0f4f;font-size:0.98em;"> <i class="fas fa-tag"></i> <?= htmlspecialchars($service['category']); ?> </div>
            <?php endif; ?>
            <div style="color:#333;"> <?= htmlspecialchars($service['description']); ?> </div>
            <div style="color:#888;font-size:0.97em;"> مقدم الخدمة: <?= htmlspecialchars($service['adv_name']); ?> </div>
        </div>
        <h2>طلب الخدمة</h2>
        <?php if ($success): ?>
            <div class="success-message">تم إرسال طلبك بنجاح! سيتم التواصل معك قريباً.</div>
        <?php elseif ($error): ?>
            <div class="error-message"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="client_name">اسمك</label>
                <input type="text" id="client_name" name="client_name" required>
            </div>
            <div class="form-group">
                <label for="client_email">بريدك الإلكتروني</label>
                <input type="email" id="client_email" name="client_email" required>
            </div>
            <div class="form-group">
                <label for="details">تفاصيل الطلب</label>
                <textarea id="details" name="details" required></textarea>
            </div>
            <button type="submit" class="btn-send">إرسال الطلب</button>
        </form>
    </div>
</body>
</html>
