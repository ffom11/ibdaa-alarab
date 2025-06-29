<?php
// نموذج تواصل مع المعلن
require_once '../includes/db.php';
$success = false;
$error = '';
$adv_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($adv_id <= 0) die('معلن غير محدد');
$stmt = $pdo->prepare("SELECT name, email FROM users WHERE id=? AND role='advertiser' LIMIT 1");
$stmt->execute([$adv_id]);
$advertiser = $stmt->fetch();
if (!$advertiser) die('المعلن غير موجود');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_name = trim($_POST['client_name']);
    $client_email = trim($_POST['client_email']);
    $message = trim($_POST['message']);
    if ($client_name && $client_email && $message) {
        // يمكنك هنا إرسال بريد إلكتروني فعلي أو حفظ الطلب في قاعدة البيانات
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
    <title>تواصل مع <?= htmlspecialchars($advertiser['name']); ?> | إبداع العرب</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .contact-form { max-width: 430px; margin: 40px auto; background: #fff; border-radius: 13px; box-shadow: 0 2px 16px #3f2fbb22; padding: 30px 27px; }
        .contact-form h2 { color: #3f2fbb; text-align: center; margin-bottom: 20px; }
        .form-group { margin-bottom: 16px; }
        label { display: block; margin-bottom: 6px; color: #5b0f4f; font-weight: bold; }
        input, textarea { width: 100%; padding: 10px 12px; border-radius: 8px; border: 1.5px solid #bcbcbc; font-size: 1rem; background: #f9f9f9; }
        textarea { min-height: 70px; resize: vertical; }
        .btn-send { background: linear-gradient(90deg, #5b0f4f 0%, #3f2fbb 100%); color: #fff; border: none; border-radius: 8px; padding: 12px 35px; font-size: 1.1rem; font-weight: bold; cursor: pointer; width: 100%; }
        .btn-send:hover { background: #dd5217; }
        .success-message, .error-message { padding: 10px; border-radius: 7px; margin-bottom: 13px; text-align: center; font-weight: bold; }
        .success-message { background: #eaffdd; color: #257c00; }
        .error-message { background: #ffdddd; color: #b70000; }
    </style>
</head>
<body>
    <div class="contact-form">
        <h2>تواصل مع <?= htmlspecialchars($advertiser['name']); ?></h2>
        <?php if ($success): ?>
            <div class="success-message">تم إرسال رسالتك بنجاح! سيتم التواصل معك قريباً.</div>
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
                <label for="message">رسالتك</label>
                <textarea id="message" name="message" required></textarea>
            </div>
            <button type="submit" class="btn-send">إرسال</button>
        </form>
    </div>
</body>
</html>
