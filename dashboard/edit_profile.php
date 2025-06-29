<?php
// صفحة تعديل بيانات المعلن وإضافة/تعديل روابط التواصل الاجتماعي والخدمات
session_start();
require_once '../includes/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'advertiser') {
    header('Location: ../login.html');
    exit;
}
$user_id = $_SESSION['user_id'];
// جلب بيانات المعلن
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
$stmt->execute([$user_id]);
$advertiser = $stmt->fetch();
if ($advertiser) {
    // جلب الخدمات
    $stmt2 = $pdo->prepare("SELECT title FROM services WHERE user_id = ?");
    $stmt2->execute([$user_id]);
    $advertiser['services'] = array_column($stmt2->fetchAll(), 'title');
} else {
    // مستخدم غير موجود
    die('المعلن غير موجود');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // معالجة رفع الصورة
    $photo_path = $advertiser['photo'];
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $allowed_ext = ['jpg','jpeg','png','gif','webp'];
        $file_info = pathinfo($_FILES['photo']['name']);
        $ext = strtolower($file_info['extension']);
        $mime = mime_content_type($_FILES['photo']['tmp_name']);
        if (in_array($ext, $allowed_ext) && strpos($mime, 'image/') === 0) {
            $new_name = 'uploads/ad_' . $user_id . '_' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], '../' . $new_name)) {
                $photo_path = '../' . $new_name;
            }
        }
    }
    // تحديث بيانات المستخدم
    $stmt = $pdo->prepare("UPDATE users SET name=?, bio=?, snapchat=?, tiktok=?, instagram=?, google=?, twitter=?, photo=? WHERE id=?");
    $stmt->execute([
        $_POST['name'],
        $_POST['bio'],
        $_POST['snapchat'],
        $_POST['tiktok'],
        $_POST['instagram'],
        $_POST['google'],
        $_POST['twitter'],
        $photo_path,
        $user_id
    ]);
    // تحديث الخدمات: حذف القديمة ثم إدخال الجديدة
    $pdo->prepare("DELETE FROM services WHERE user_id = ?")->execute([$user_id]);
    $services = array_filter(array_map('trim', explode("\n", $_POST['services'])));
    foreach ($services as $srv) {
        $pdo->prepare("INSERT INTO services (user_id, title) VALUES (?, ?)")->execute([$user_id, $srv]);
    }
    $success = true;
    // إعادة جلب البيانات بعد التحديث
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
    $stmt->execute([$user_id]);
    $advertiser = $stmt->fetch();
    $stmt2 = $pdo->prepare("SELECT title FROM services WHERE user_id = ?");
    $stmt2->execute([$user_id]);
    $advertiser['services'] = array_column($stmt2->fetchAll(), 'title');
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل البروفايل | إبداع العرب</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .edit-profile-container {
            max-width: 600px;
            margin: 40px auto;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 16px #3f2fbb22;
            padding: 30px 30px 20px 30px;
        }
        .edit-profile-container h2 {
            color: #3f2fbb;
            margin-bottom: 22px;
            text-align: center;
        }
        .form-group {
            margin-bottom: 18px;
        }
        label {
            display: block;
            margin-bottom: 6px;
            color: #5b0f4f;
            font-weight: bold;
        }
        input[type="text"], input[type="url"], textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1.5px solid #bcbcbc;
            border-radius: 8px;
            font-size: 1rem;
            background: #f9f9f9;
            transition: border 0.2s;
        }
        input[type="text"]:focus, input[type="url"]:focus, textarea:focus {
            border-color: #3f2fbb;
            outline: none;
        }
        .social-icons {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }
        .social-icons i {
            font-size: 1.3rem;
            color: #3f2fbb;
        }
        .form-group textarea {
            min-height: 70px;
            resize: vertical;
        }
        .btn-save {
            background: linear-gradient(90deg, #5b0f4f 0%, #3f2fbb 100%);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 12px 35px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s;
            width: 100%;
        }
        .btn-save:hover {
            background: #dd5217;
        }
        .success-message {
            background: #fcff74;
            color: #3f2fbb;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="edit-profile-container">
        <div style="text-align:left;margin-bottom:10px;">
            <a href="../logout.php" style="background:#dd5217;color:#fff;padding:7px 18px;border-radius:7px;text-decoration:none;font-weight:bold;">تسجيل الخروج</a>
        </div>
        <h2>تعديل بيانات البروفايل</h2>
        <?php if (!empty($success)): ?>
            <div class="success-message">تم حفظ التعديلات بنجاح!</div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="photo">الصورة الشخصية</label>
                <input type="file" id="photo" name="photo" accept="image/*">
                <?php if (!empty($advertiser['photo'])): ?>
                    <div style="margin-top:7px;"><img src="<?= htmlspecialchars($advertiser['photo']); ?>" alt="صورة المعلن" style="width:70px;border-radius:50%;border:2px solid #3f2fbb;"></div>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="name">اسم المعلن</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($advertiser['name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="bio">نبذة تعريفية</label>
                <textarea id="bio" name="bio" required><?= htmlspecialchars($advertiser['bio']); ?></textarea>
            </div>
            <div class="form-group">
                <label>روابط حسابات التواصل الاجتماعي</label>
                <div class="social-icons">
                    <i class="fab fa-snapchat-ghost"></i>
                    <input type="url" name="snapchat" placeholder="رابط سناب شات" value="<?= htmlspecialchars($advertiser['snapchat']); ?>">
                </div>
                <div class="social-icons">
                    <i class="fab fa-tiktok"></i>
                    <input type="url" name="tiktok" placeholder="رابط تيك توك" value="<?= htmlspecialchars($advertiser['tiktok']); ?>">
                </div>
                <div class="social-icons">
                    <i class="fab fa-instagram"></i>
                    <input type="url" name="instagram" placeholder="رابط إنستجرام" value="<?= htmlspecialchars($advertiser['instagram']); ?>">
                </div>
                <div class="social-icons">
                    <i class="fab fa-google"></i>
                    <input type="url" name="google" placeholder="رابط جوجل" value="<?= htmlspecialchars($advertiser['google']); ?>">
                </div>
                <div class="social-icons">
                    <i class="fab fa-twitter"></i>
                    <input type="url" name="twitter" placeholder="رابط تويتر" value="<?= htmlspecialchars($advertiser['twitter']); ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="services">الخدمات (كل خدمة في سطر منفصل)</label>
                <textarea id="services" name="services" placeholder="مثال:\nتصوير فوتوغرافي احترافي\nمونتاج فيديو إبداعي\nتصميم جرافيك عصري" required><?= htmlspecialchars(implode("\n", $advertiser['services'])); ?></textarea>
            </div>
            <button type="submit" class="btn-save">حفظ التعديلات</button>
        </form>
    </div>
</body>
</html>
