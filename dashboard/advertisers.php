<?php
// صفحة دليل جميع المعلنين مع إمكانية البحث
require_once __DIR__ . '/../includes/db.php';

// جلب جميع التصنيفات المتاحة من الخدمات
$cat_stmt = $pdo->query("SELECT DISTINCT category FROM services WHERE category IS NOT NULL AND category <> '' ORDER BY category ASC");
$categories = $cat_stmt->fetchAll(PDO::FETCH_COLUMN);

// معالجة البحث والتصفية
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$selected_cat = isset($_GET['category']) ? trim($_GET['category']) : '';

// بناء استعلام المعلنين مع التصفية
$sql = "SELECT DISTINCT u.id, u.name, u.photo, u.bio FROM users u ";
$params = [];
if ($selected_cat !== '') {
    $sql .= "INNER JOIN services s ON s.user_id = u.id AND s.category = ? ";
    $params[] = $selected_cat;
}
$sql .= "WHERE u.role = 'advertiser'";
if ($search !== '') {
    $sql .= " AND (u.name LIKE ? OR u.bio LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
$sql .= " ORDER BY u.id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$advertisers = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دليل المعلنين | إبداع العرب</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .directory-container { max-width: 900px; margin: 40px auto; }
        .search-box { margin-bottom: 25px; text-align: center; }
        .search-box input[type='text'] {
            width: 60%; max-width: 350px; padding: 10px 14px; border-radius: 8px; border: 1.5px solid #bcbcbc; font-size: 1rem;
        }
        .search-box button {
            background: #3f2fbb; color: #fff; border: none; border-radius: 8px; padding: 10px 22px; font-size: 1rem; margin-right: 7px; cursor: pointer;
        }
        .advertisers-list { display: flex; flex-wrap: wrap; gap: 26px; justify-content: center; }
        .advertiser-card {
            background: #fff; border-radius: 14px; box-shadow: 0 2px 12px #3f2fbb22; width: 270px; padding: 24px 18px 18px 18px; text-align: center; transition: box-shadow 0.2s;
        }
        .advertiser-card:hover { box-shadow: 0 4px 24px #3f2fbb44; }
        .advertiser-photo {
            width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin-bottom: 10px; border: 3px solid #3f2fbb;
        }
        .advertiser-name { color: #5b0f4f; font-size: 1.2rem; font-weight: bold; margin-bottom: 6px; }
        .advertiser-bio { color: #333; font-size: 1rem; margin-bottom: 13px; min-height: 36px; }
        .profile-link { background: #fcff74; color: #3f2fbb; padding: 7px 20px; border-radius: 8px; text-decoration: none; font-weight: bold; transition: background 0.2s; }
        .profile-link:hover { background: #dd5217; color: #fff; }
        @media (max-width: 700px) { .advertisers-list { flex-direction: column; align-items: center; } .directory-container { padding: 0 7px; } }
    </style>
</head>
<body>
    <div class="directory-container">
        <h2 style="text-align:center;color:#3f2fbb;margin-bottom:22px;">دليل المعلنين</h2>
        <div class="search-box">
            <form method="GET" style="display:flex;flex-wrap:wrap;gap:10px;justify-content:center;align-items:center;">
                <input type="text" name="q" placeholder="ابحث باسم المعلن أو نبذته..." value="<?= htmlspecialchars($search); ?>">
                <select name="category" style="padding:10px 12px;border-radius:8px;border:1.5px solid #bcbcbc;font-size:1rem;min-width:120px;">
                    <option value="">كل التصنيفات</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat); ?>" <?= $selected_cat===$cat?'selected':''; ?>><?= htmlspecialchars($cat); ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit"><i class="fas fa-search"></i> بحث</button>
            </form>
        </div>
        <div class="advertisers-list">
            <?php if (count($advertisers) === 0): ?>
                <div style="color:#b70000;font-weight:bold;">لا يوجد معلنون مطابقون للبحث.</div>
            <?php else: ?>
                <?php foreach ($advertisers as $adv): ?>
                    <div class="advertiser-card">
                        <img src="<?= !empty($adv['photo']) ? htmlspecialchars($adv['photo']) : '../uploads/default.png'; ?>" class="advertiser-photo" alt="صورة المعلن">
                        <div class="advertiser-name"><?= htmlspecialchars($adv['name']); ?></div>
                        <div class="advertiser-bio"><?= htmlspecialchars(mb_substr($adv['bio'],0,60)) . (mb_strlen($adv['bio'])>60?'...':''); ?></div>
                        <a class="profile-link" href="advertiser_profile.php?id=<?= $adv['id']; ?>">عرض البروفايل</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
