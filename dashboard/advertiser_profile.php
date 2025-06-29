<?php
// صفحة بروفايل المعلن مع عرض أيقونات وروابط التواصل الاجتماعي
session_start();
require_once '../includes/db.php';
if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'advertiser' && !isset($_GET['id'])) {
    $user_id = $_SESSION['user_id'];
} elseif (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $user_id = (int)$_GET['id'];
} else {
    die('لا يوجد معلن محدد للعرض.');
}
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
$stmt->execute([$user_id]);
$advertiser = $stmt->fetch();
if ($advertiser) {
    $stmt2 = $pdo->prepare("SELECT title FROM services WHERE user_id = ?");
    $stmt2->execute([$user_id]);
    $advertiser['services'] = array_column($stmt2->fetchAll(), 'title');
} else {
    die('المعلن غير موجود');
}
if (empty($advertiser['photo'])) {
    $advertiser['photo'] = '../uploads/default.png';
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بروفايل المعلن | إبداع العرب</title>
    <link rel="stylesheet" href="../style.css">
    <!-- أيقونات Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .profile-header {
            background: linear-gradient(90deg, #5b0f4f 0%, #3f2fbb 100%);
            color: #fff;
            padding: 40px 0 20px 0;
            text-align: center;
        }
        .profile-photo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #fff;
            margin-bottom: 15px;
            box-shadow: 0 2px 12px #3f2fbb44;
        }
        .profile-social {
            margin: 20px 0;
        }
        .profile-social a {
            display: inline-block;
            margin: 0 8px;
            font-size: 2rem;
            color: #fff;
            background: #3f2fbb;
            border-radius: 50%;
            width: 48px;
            height: 48px;
            line-height: 48px;
            text-align: center;
            transition: background 0.2s, color 0.2s;
        }
        .profile-social a:hover {
            background: #dd5217;
            color: #fff;
        }
        .profile-bio {
            max-width: 600px;
            margin: 0 auto 20px auto;
            font-size: 1.2rem;
            color: #333;
            background: #fcff74;
            padding: 15px 25px;
            border-radius: 16px;
            box-shadow: 0 2px 8px #0001;
        }
        .profile-services {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 1px 8px #3f2fbb22;
            padding: 25px 30px;
            margin: 30px auto;
            max-width: 700px;
        }
        .profile-services h3 {
            color: #5b0f4f;
            margin-bottom: 14px;
        }
        .profile-services ul {
            list-style: none;
            padding: 0;
        }
        .profile-services li {
            font-size: 1.1rem;
            margin-bottom: 8px;
            padding-right: 18px;
            position: relative;
        }
        .profile-services li:before {
            content: '\f058';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            color: #3f2fbb;
            position: absolute;
            right: 0;
        }
    </style>
</head>
<body>
    <div class="profile-header">
        <img src="<?= $advertiser['photo']; ?>" alt="صورة المعلن" class="profile-photo">
        <h1><?= $advertiser['name']; ?></h1>
        <div class="profile-social">
            <?php if($advertiser['snapchat']): ?>
                <a href="<?= $advertiser['snapchat']; ?>" target="_blank" title="سناب شات"><i class="fab fa-snapchat-ghost"></i></a>
            <?php endif; ?>
            <?php if($advertiser['tiktok']): ?>
                <a href="<?= $advertiser['tiktok']; ?>" target="_blank" title="تيك توك"><i class="fab fa-tiktok"></i></a>
            <?php endif; ?>
            <?php if($advertiser['instagram']): ?>
                <a href="<?= $advertiser['instagram']; ?>" target="_blank" title="إنستجرام"><i class="fab fa-instagram"></i></a>
            <?php endif; ?>
            <?php if($advertiser['google']): ?>
                <a href="<?= $advertiser['google']; ?>" target="_blank" title="جوجل"><i class="fab fa-google"></i></a>
            <?php endif; ?>
            <?php if($advertiser['twitter']): ?>
                <a href="<?= $advertiser['twitter']; ?>" target="_blank" title="تويتر"><i class="fab fa-twitter"></i></a>
            <?php endif; ?>
        </div>
    </div>
    <div class="profile-bio">
        <?= $advertiser['bio']; ?>
    </div>
    <div class="profile-services">
        <h3>خدماتنا:</h3>
        <ul>
            <?php foreach($advertiser['services'] as $service): ?>
                <li><?= htmlspecialchars($service); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- زر تواصل مع المعلن -->
    <div style="text-align:center;margin:18px 0;">
        <a href="contact_advertiser.php?id=<?= $advertiser['id']; ?>" style="background:#3f2fbb;color:#fff;padding:10px 28px;border-radius:9px;text-decoration:none;font-weight:bold;font-size:1.1rem;">تواصل مع المعلن</a>
    </div>

    <!-- استعراض التقييمات -->
    <div class="profile-services" style="margin-top:30px;">
        <h3>تقييمات العملاء:</h3>
        <?php
        // جلب التقييمات من قاعدة البيانات
        $stmt3 = $pdo->prepare("SELECT name, rating, comment, created_at FROM ratings WHERE advertiser_id=? ORDER BY created_at DESC");
        $stmt3->execute([$advertiser['id']]);
        $ratings = $stmt3->fetchAll();
        if (count($ratings) === 0): ?>
            <div style="color:#b70000;font-weight:bold;">لا توجد تقييمات بعد.</div>
        <?php else: ?>
            <?php foreach($ratings as $r): ?>
                <div style="background:#f9f9f9;border-radius:8px;padding:10px 14px;margin-bottom:10px;box-shadow:0 1px 4px #3f2fbb11;">
                    <div style="font-weight:bold;color:#3f2fbb;display:inline-block;min-width:90px;"> <?= htmlspecialchars($r['name']); ?> </div>
                    <span style="color:#dd5217;font-size:1.1em;">
                        <?php for($i=1;$i<=5;$i++): ?>
                            <i class="fa<?= $i<=$r['rating']? 's':'r' ?> fa-star"></i>
                        <?php endfor; ?>
                    </span>
                    <span style="color:#888;font-size:0.95em;float:left;"> <?= date('Y-m-d', strtotime($r['created_at'])); ?> </span>
                    <div style="margin-top:5px;"> <?= htmlspecialchars($r['comment']); ?> </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- نموذج إضافة تقييم جديد (للعملاء فقط) -->
    <?php
    $can_rate = false;
    if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'client') {
        // تحقق أن العميل لم يقيّم هذا المعلن من قبل
        $stmt4 = $pdo->prepare("SELECT id FROM ratings WHERE advertiser_id=? AND client_id=? LIMIT 1");
        $stmt4->execute([$advertiser['id'], $_SESSION['user_id']]);
        if (!$stmt4->fetch()) $can_rate = true;
    }
    if ($can_rate): ?>
    <div class="profile-services" style="margin-top:22px;">
        <h3>أضف تقييمك:</h3>
        <form method="POST" action="rate_advertiser.php?id=<?= $advertiser['id']; ?>">
            <input type="hidden" name="client_id" value="<?= $_SESSION['user_id']; ?>">
            <div class="form-group">
                <label>اسمك:</label>
                <input type="text" name="name" required value="<?= htmlspecialchars($_SESSION['name']); ?>">
            </div>
            <div class="form-group">
                <label>التقييم:</label>
                <select name="rating" required>
                    <option value="">اختر</option>
                    <?php for($i=5;$i>=1;$i--): ?>
                        <option value="<?= $i; ?>"> <?= $i; ?> نجوم </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="form-group">
                <label>تعليق:</label>
                <textarea name="comment" required></textarea>
            </div>
            <button type="submit" style="background:#5b0f4f;color:#fff;padding:8px 30px;border-radius:7px;font-size:1.1em;">إرسال التقييم</button>
        </form>
    </div>
    <?php endif; ?>
</body>
</html>
