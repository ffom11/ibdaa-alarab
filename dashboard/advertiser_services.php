<?php
// صفحة استعراض خدمات معلن معين مع تفاصيل الخدمة وإمكانية طلب الخدمة
require_once '../includes/db.php';
$adv_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($adv_id <= 0) die('معلن غير محدد.');
$stmt = $pdo->prepare("SELECT name, photo, bio FROM users WHERE id=? AND role='advertiser' LIMIT 1");
$stmt->execute([$adv_id]);
$adv = $stmt->fetch();
if (!$adv) die('المعلن غير موجود.');
$stmt2 = $pdo->prepare("SELECT id, title, description, category FROM services WHERE user_id=? ORDER BY id DESC");
$stmt2->execute([$adv_id]);
$services = $stmt2->fetchAll();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>خدمات <?= htmlspecialchars($adv['name']); ?> | إبداع العرب</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .adv-services-container { max-width: 900px; margin: 40px auto; }
        .adv-header { display:flex;align-items:center;gap:18px;margin-bottom:30px; }
        .adv-header img { width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid #3f2fbb; }
        .adv-header .name { font-size:1.3rem;font-weight:bold;color:#3f2fbb; }
        .adv-header .bio { color:#555;margin-top:7px; }
        .service-list { display: flex; flex-wrap: wrap; gap: 30px; }
        .service-card { background:#fff;border-radius:12px;box-shadow:0 2px 12px #3f2fbb22;width:270px;padding:20px 15px 15px 15px; }
        .service-title { color:#5b0f4f;font-size:1.1rem;font-weight:bold;margin-bottom:7px; }
        .service-category { color:#3f2fbb;font-size:0.98rem;margin-bottom:7px; }
        .service-desc { color:#333;font-size:1rem;min-height:38px;margin-bottom:12px; }
        .request-btn { background:#dd5217;color:#fff;padding:7px 18px;border-radius:7px;text-decoration:none;font-weight:bold;transition:background 0.2s;display:inline-block; }
        .request-btn:hover { background:#3f2fbb; }
        @media (max-width:700px) { .adv-header {flex-direction:column;align-items:center;} .service-list {flex-direction:column;align-items:center;} }
    </style>
</head>
<body>
    <div class="adv-services-container">
        <div class="adv-header">
            <img src="<?= !empty($adv['photo']) ? htmlspecialchars($adv['photo']) : '../uploads/default.png'; ?>" alt="صورة المعلن">
            <div>
                <div class="name"> <?= htmlspecialchars($adv['name']); ?> </div>
                <div class="bio"> <?= htmlspecialchars($adv['bio']); ?> </div>
            </div>
        </div>
        <h2 style="color:#3f2fbb;text-align:center;margin-bottom:22px;">خدمات المعلن</h2>
        <div class="service-list">
            <?php if (count($services) === 0): ?>
                <div style="color:#b70000;font-weight:bold;">لا توجد خدمات لهذا المعلن بعد.</div>
            <?php else: ?>
                <?php foreach($services as $srv): ?>
                    <div class="service-card">
                        <div class="service-title"> <?= htmlspecialchars($srv['title']); ?> </div>
                        <?php if (!empty($srv['category'])): ?>
                            <div class="service-category"><i class="fas fa-tag"></i> <?= htmlspecialchars($srv['category']); ?></div>
                        <?php endif; ?>
                        <div class="service-desc"> <?= htmlspecialchars($srv['description']); ?> </div>
                        <a href="request_service.php?service_id=<?= $srv['id']; ?>" class="request-btn">طلب الخدمة</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
