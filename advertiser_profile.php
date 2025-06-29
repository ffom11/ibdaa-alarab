<?php
// صفحة الملف الشخصي للمعلن
require_once __DIR__ . '/includes/db.php';

// التحقق من وجود معرف المعلن
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: advertisers.php');
    exit;
}

$advertiser_id = (int)$_GET['id'];

// جلب بيانات المعلن
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'advertiser' AND is_active = 1");
$stmt->execute([$advertiser_id]);
$advertiser = $stmt->fetch();

// إذا لم يتم العثور على المعلن
if (!$advertiser) {
    header('Location: advertisers.php?error=not_found');
    exit;
}

// جلب خدمات المعلن
$services_stmt = $pdo->prepare("SELECT * FROM services WHERE user_id = ? ORDER BY created_at DESC");
$services_stmt->execute([$advertiser_id]);
$services = $services_stmt->fetchAll();

// جلب آراء العملاء
$reviews_stmt = $pdo->prepare("
    SELECT r.*, u.name as client_name, u.photo as client_photo 
    FROM reviews r 
    JOIN users u ON r.client_id = u.id 
    WHERE r.advertiser_id = ? 
    ORDER BY r.created_at DESC
");
$reviews_stmt->execute([$advertiser_id]);
$reviews = $reviews_stmt->fetchAll();

// حساب متوسط التقييم
$avg_rating_stmt = $pdo->prepare("SELECT AVG(rating) as avg_rating FROM reviews WHERE advertiser_id = ?");
$avg_rating_stmt->execute([$advertiser_id]);
$avg_rating = $avg_rating_stmt->fetch()['avg_rating'];
$avg_rating = $avg_rating ? round($avg_rating, 1) : 0;
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الملف الشخصي - <?php echo htmlspecialchars($advertiser['name']); ?> - إبداع العرب</title>
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
        
        .back-btn {
            display: inline-flex;
            align-items: center;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            padding: 8px 15px;
            border: 1px solid var(--primary-color);
            border-radius: 5px;
            transition: all 0.3s;
        }
        
        .back-btn:hover {
            background: var(--primary-color);
            color: white;
        }
        
        .profile-header {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 30px;
            margin-bottom: 30px;
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            align-items: center;
        }
        
        .profile-image {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid #f5f5f5;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .profile-info {
            flex: 1;
            min-width: 250px;
        }
        
        .profile-name {
            color: var(--primary-color);
            margin-bottom: 10px;
            font-size: 1.8rem;
        }
        
        .profile-title {
            color: #666;
            margin-bottom: 15px;
            font-size: 1.1rem;
        }
        
        .rating {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .rating-stars {
            color: #ffc107;
            margin-left: 10px;
        }
        
        .rating-value {
            font-weight: bold;
            margin-right: 5px;
        }
        
        .profile-contacts {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 20px;
        }
        
        .contact-btn {
            display: inline-flex;
            align-items: center;
            padding: 8px 15px;
            background: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }
        
        .contact-btn i {
            margin-left: 8px;
        }
        
        .contact-btn.whatsapp {
            background: #25D366;
        }
        
        .contact-btn.phone {
            background: #34B7F1;
        }
        
        .contact-btn:hover {
            opacity: 0.9;
        }
        
        .section {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 25px;
            margin-bottom: 30px;
        }
        
        .section-title {
            color: var(--primary-color);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f5f5f5;
            font-size: 1.4rem;
        }
        
        .section-content p {
            line-height: 1.8;
            color: #555;
        }
        
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .service-card {
            border: 1px solid #eee;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .service-image {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }
        
        .service-info {
            padding: 15px;
        }
        
        .service-title {
            color: var(--primary-color);
            margin-bottom: 10px;
            font-size: 1.2rem;
        }
        
        .service-price {
            color: #e44d26;
            font-weight: bold;
            margin: 10px 0;
            font-size: 1.1rem;
        }
        
        .service-desc {
            color: #666;
            font-size: 0.95rem;
            line-height: 1.5;
        }
        
        .reviews-list {
            margin-top: 20px;
        }
        
        .review-card {
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background: #f9f9f9;
        }
        
        .review-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }
        
        .reviewer {
            display: flex;
            align-items: center;
        }
        
        .reviewer-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            margin-left: 15px;
        }
        
        .reviewer-name {
            font-weight: bold;
            color: #333;
        }
        
        .review-date {
            color: #888;
            font-size: 0.9rem;
        }
        
        .review-rating {
            color: #ffc107;
            margin: 5px 0 10px;
        }
        
        .review-text {
            color: #555;
            line-height: 1.6;
        }
        
        .no-content {
            text-align: center;
            padding: 40px 20px;
            color: #888;
            font-size: 1.1rem;
        }
        
        @media (max-width: 768px) {
            .profile-header {
                text-align: center;
                justify-content: center;
            }
            
            .profile-info {
                text-align: center;
            }
            
            .rating {
                justify-content: center;
            }
            
            .profile-contacts {
                justify-content: center;
            }
            
            .services-grid {
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
                    <a href="advertisers.php" class="back-btn">
                        <i class="fas fa-arrow-right"></i> العودة لقائمة المعلنين
                    </a>
                </nav>
            </div>
        </div>
    </header>
    
    <div class="container">
        <div class="profile-header">
            <img 
                src="<?php echo !empty($advertiser['photo']) ? htmlspecialchars($advertiser['photo']) : 'https://via.placeholder.com/200x200?text=' . urlencode($advertiser['name']); ?>" 
                alt="<?php echo htmlspecialchars($advertiser['name']); ?>" 
                class="profile-image"
            >
            <div class="profile-info">
                <h1 class="profile-name"><?php echo htmlspecialchars($advertiser['name']); ?></h1>
                <?php if (!empty($advertiser['title'])): ?>
                    <p class="profile-title"><?php echo htmlspecialchars($advertiser['title']); ?></p>
                <?php endif; ?>
                
                <div class="rating">
                    <span class="rating-value"><?php echo $avg_rating; ?></span>
                    <div class="rating-stars">
                        <?php
                        $full_stars = floor($avg_rating);
                        $has_half_star = ($avg_rating - $full_stars) >= 0.5;
                        
                        for ($i = 1; $i <= 5; $i++):
                            if ($i <= $full_stars) {
                                echo '<i class="fas fa-star"></i>';
                            } elseif ($i == $full_stars + 1 && $has_half_star) {
                                echo '<i class="fas fa-star-half-alt"></i>';
                            } else {
                                echo '<i class="far fa-star"></i>';
                            }
                        endfor;
                        ?>
                    </div>
                    <span>(<?php echo count($reviews); ?> تقييم)</span>
                </div>
                
                <div class="profile-contacts">
                    <?php if (!empty($advertiser['phone'])): ?>
                        <a href="tel:<?php echo htmlspecialchars($advertiser['phone']); ?>" class="contact-btn phone">
                            <i class="fas fa-phone"></i>
                            <span>اتصل بنا</span>
                        </a>
                    <?php endif; ?>
                    
                    <?php if (!empty($advertiser['whatsapp'])): ?>
                        <a href="https://wa.me/<?php echo htmlspecialchars($advertiser['whatsapp']); ?>" target="_blank" class="contact-btn whatsapp">
                            <i class="fab fa-whatsapp"></i>
                            <span>واتساب</span>
                        </a>
                    <?php endif; ?>
                    
                    <?php if (!empty($advertiser['email'])): ?>
                        <a href="mailto:<?php echo htmlspecialchars($advertiser['email']); ?>" class="contact-btn">
                            <i class="fas fa-envelope"></i>
                            <span>بريد إلكتروني</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <?php if (!empty($advertiser['bio'])): ?>
        <div class="section">
            <h2 class="section-title">نبذة عني</h2>
            <div class="section-content">
                <p><?php echo nl2br(htmlspecialchars($advertiser['bio'])); ?></p>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($services)): ?>
        <div class="section">
            <h2 class="section-title">الخدمات المقدمة</h2>
            <div class="services-grid">
                <?php foreach ($services as $service): ?>
                    <div class="service-card">
                        <?php if (!empty($service['image'])): ?>
                            <img src="<?php echo htmlspecialchars($service['image']); ?>" alt="<?php echo htmlspecialchars($service['title']); ?>" class="service-image">
                        <?php endif; ?>
                        <div class="service-info">
                            <h3 class="service-title"><?php echo htmlspecialchars($service['title']); ?></h3>
                            <?php if (!empty($service['price'])): ?>
                                <div class="service-price">
                                    السعر: <?php echo number_format($service['price']); ?> ريال سعودي
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($service['description'])): ?>
                                <p class="service-desc">
                                    <?php echo nl2br(htmlspecialchars(mb_substr($service['description'], 0, 150) . (mb_strlen($service['description']) > 150 ? '...' : ''))); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="section">
            <h2 class="section-title">آراء العملاء</h2>
            <div class="reviews-list">
                <?php if (!empty($reviews)): ?>
                    <?php foreach ($reviews as $review): ?>
                        <div class="review-card">
                            <div class="review-header">
                                <div class="reviewer">
                                    <img 
                                        src="<?php echo !empty($review['client_photo']) ? htmlspecialchars($review['client_photo']) : 'https://via.placeholder.com/50x50?text=' . urlencode($review['client_name'][0]); ?>" 
                                        alt="<?php echo htmlspecialchars($review['client_name']); ?>" 
                                        class="reviewer-avatar"
                                    >
                                    <div>
                                        <div class="reviewer-name"><?php echo htmlspecialchars($review['client_name']); ?></div>
                                        <div class="review-date"><?php echo date('Y/m/d', strtotime($review['created_at'])); ?></div>
                                    </div>
                                </div>
                                <div class="review-rating">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star<?php echo $i <= $review['rating'] ? '' : '-o'; ?>"></i>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <?php if (!empty($review['comment'])): ?>
                                <div class="review-text">
                                    <?php echo nl2br(htmlspecialchars($review['comment'])); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-content">
                        <i class="far fa-comment-dots" style="font-size: 3rem; opacity: 0.3; margin-bottom: 15px;"></i>
                        <p>لا توجد آراء حتى الآن.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <footer style="background: #333; color: #fff; padding: 40px 0; margin-top: 50px;">
        <div class="container" style="text-align: center;">
            <p>© 2024 إبداع العرب. جميع الحقوق محفوظة</p>
        </div>
    </footer>
</body>
</html>
