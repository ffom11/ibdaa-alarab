<?php
// صفحة عرض المعلنين للعملاء
require_once __DIR__ . '/includes/db.php';

// جلب جميع التصنيفات المتاحة من الخدمات
$cat_stmt = $pdo->query("SELECT DISTINCT category FROM services WHERE category IS NOT NULL AND category <> '' ORDER BY category ASC");
$categories = $cat_stmt->fetchAll(PDO::FETCH_COLUMN);

// معالجة البحث والتصفية
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$selected_cat = isset($_GET['category']) ? trim($_GET['category']) : '';

// بناء استعلام المعلنين مع التصفية
$sql = "SELECT DISTINCT u.id, u.name, u.photo, u.bio, u.phone, u.email FROM users u ";
$params = [];

if ($selected_cat !== '') {
    $sql .= "INNER JOIN services s ON s.user_id = u.id AND s.category = ? ";
    $params[] = $selected_cat;
}

$sql .= "WHERE u.role = 'advertiser' AND u.is_active = 1";

if ($search !== '') {
    $sql .= " AND (u.name LIKE ? OR u.bio LIKE ? OR u.skills LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

$sql .= " ORDER BY u.name ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$advertisers = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دليل المعلنين - إبداع العرب</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3f2fbb;
            --secondary-color: #f8f9fa;
            --text-color: #333;
            --light-gray: #f1f1f1;
            --border-radius: 8px;
            --box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Tajawal', sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
            color: var(--text-color);
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 15px 0;
            margin-bottom: 30px;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .logo {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: var(--primary-color);
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .logo img {
            height: 40px;
            margin-left: 10px;
        }
        
        .search-container {
            background: white;
            padding: 30px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 30px;
        }
        
        .search-box {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .search-input {
            flex: 1;
            min-width: 250px;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-size: 1rem;
        }
        
        .category-select {
            min-width: 200px;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            background: white;
            font-size: 1rem;
        }
        
        .search-btn {
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            padding: 0 25px;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.3s;
        }
        
        .search-btn:hover {
            background: #34248f;
        }
        
        .advertisers-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }
        
        .advertiser-card {
            background: white;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--box-shadow);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .advertiser-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        }
        
        .advertiser-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .advertiser-info {
            padding: 20px;
        }
        
        .advertiser-name {
            color: var(--primary-color);
            margin-bottom: 10px;
            font-size: 1.3rem;
        }
        
        .advertiser-bio {
            color: #666;
            margin-bottom: 15px;
            font-size: 0.95rem;
            line-height: 1.5;
        }
        
        .advertiser-contacts {
            margin-top: 15px;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
        
        .contact-item {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            color: #555;
            font-size: 0.9rem;
        }
        
        .contact-item i {
            margin-left: 8px;
            color: var(--primary-color);
            width: 20px;
            text-align: center;
        }
        
        .view-profile {
            display: inline-block;
            background: var(--primary-color);
            color: white;
            text-decoration: none;
            padding: 8px 20px;
            border-radius: 5px;
            margin-top: 15px;
            transition: background 0.3s;
            font-size: 0.9rem;
        }
        
        .view-profile:hover {
            background: #34248f;
            color: white;
        }
        
        .no-results {
            text-align: center;
            padding: 50px 20px;
            grid-column: 1 / -1;
            color: #666;
        }
        
        @media (max-width: 768px) {
            .search-box {
                flex-direction: column;
            }
            
            .search-input, .category-select, .search-btn {
                width: 100%;
            }
            
            .advertisers-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <a href="index.html" class="logo">
                    <img src="https://creativitymarkting.com/_assets/images/3ba59ca21cfefc394b16ee5042a922d4.png" alt="شعار إبداع العرب">
                    <span>إبداع العرب</span>
                </a>
                <nav>
                    <a href="index.html" class="back-home">
                        <i class="fas fa-arrow-right"></i> العودة للرئيسية
                    </a>
                </nav>
            </div>
        </div>
    </header>
    
    <div class="container">
        <h1 style="text-align: center; margin-bottom: 30px; color: var(--primary-color);">دليل المعلنين المعتمدين</h1>
        
        <div class="search-container">
            <form method="get" action="" class="search-form">
                <div class="search-box">
                    <input 
                        type="text" 
                        name="q" 
                        class="search-input" 
                        placeholder="ابحث عن معلن بالاسم أو التخصص..."
                        value="<?php echo htmlspecialchars($search); ?>"
                    >
                    
                    <select name="category" class="category-select">
                        <option value="">جميع التصنيفات</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo htmlspecialchars($category); ?>" 
                                <?php echo ($selected_cat === $category) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <button type="submit" class="search-btn">
                        <i class="fas fa-search"></i> بحث
                    </button>
                </div>
            </form>
        </div>
        
        <div class="advertisers-grid">
            <?php if (count($advertisers) > 0): ?>
                <?php foreach ($advertisers as $advertiser): ?>
                    <div class="advertiser-card">
                        <img 
                            src="<?php echo !empty($advertiser['photo']) ? htmlspecialchars($advertiser['photo']) : 'https://via.placeholder.com/300x200?text=' . urlencode($advertiser['name']); ?>" 
                            alt="<?php echo htmlspecialchars($advertiser['name']); ?>" 
                            class="advertiser-image"
                        >
                        <div class="advertiser-info">
                            <h3 class="advertiser-name"><?php echo htmlspecialchars($advertiser['name']); ?></h3>
                            <p class="advertiser-bio">
                                <?php echo !empty($advertiser['bio']) ? nl2br(htmlspecialchars($advertiser['bio'])) : 'لا يوجد وصف.'; ?>
                            </p>
                            
                            <div class="advertiser-contacts">
                                <?php if (!empty($advertiser['phone'])): ?>
                                    <div class="contact-item">
                                        <span><?php echo htmlspecialchars($advertiser['phone']); ?></span>
                                        <i class="fas fa-phone"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($advertiser['email'])): ?>
                                    <div class="contact-item">
                                        <a href="mailto:<?php echo htmlspecialchars($advertiser['email']); ?>" style="color: #555; text-decoration: none;">
                                            <?php echo htmlspecialchars($advertiser['email']); ?>
                                        </a>
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <a href="advertiser_profile.php?id=<?php echo $advertiser['id']; ?>" class="view-profile">
                                عرض الملف الشخصي <i class="fas fa-user"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-results">
                    <i class="fas fa-search" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.5;"></i>
                    <h3>لا توجد نتائج</h3>
                    <p>لم نتمكن من العثور على أي معلنين يتطابقون مع معايير البحث الخاصة بك.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <footer style="background: #333; color: #fff; padding: 40px 0; margin-top: 50px;">
        <div class="container" style="text-align: center;">
            <p>© 2024 إبداع العرب. جميع الحقوق محفوظة</p>
        </div>
    </footer>
</body>
</html>
