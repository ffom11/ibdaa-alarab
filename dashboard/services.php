<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

// التحقق من صلاحيات المستخدم
if (!isset($_SESSION['user_id']) || !$_SESSION['can_manage_services']) {
    header('Location: /login.php?error=unauthorized');
    exit;
}

$message = '';
$error = '';

// معالجة إضافة/تعديل فئة خدمة
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_category'])) {
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        
        if (empty($name)) {
            $error = 'اسم الفئة مطلوب';
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO service_categories (name, description) VALUES (?, ?)");
                $stmt->execute([$name, $description]);
                $message = 'تمت إضافة الفئة بنجاح';
            } catch (PDOException $e) {
                $error = 'حدث خطأ أثناء إضافة الفئة: ' . $e->getMessage();
            }
        }
    }
    
    // معالجة تحديث سعر الخدمة
    if (isset($_POST['update_prices'])) {
        if (isset($_POST['services']) && is_array($_POST['services'])) {
            try {
                $pdo->beginTransaction();
                
                foreach ($_POST['services'] as $serviceId => $serviceData) {
                    $price = filter_var($serviceData['price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                    $isActive = isset($serviceData['is_active']) ? 1 : 0;
                    
                    $stmt = $pdo->prepare("UPDATE services SET price = ?, is_active = ? WHERE id = ?");
                    $stmt->execute([$price, $isActive, $serviceId]);
                }
                
                $pdo->commit();
                $message = 'تم تحديث الأسعار بنجاح';
            } catch (Exception $e) {
                $pdo->rollBack();
                $error = 'حدث خطأ أثناء تحديث الأسعار: ' . $e->getMessage();
            }
        }
    }
}

// جلب جميع الفئات مع خدماتها
$categories = $pdo->query("SELECT * FROM service_categories WHERE is_active = 1 ORDER BY name")->fetchAll();

// جلب جميع الخدمات مع معلومات الفئة
$services = [];
if (!empty($categories)) {
    $categoryIds = array_column($categories, 'id');
    $placeholders = str_repeat('?,', count($categoryIds) - 1) . '?';
    
    $stmt = $pdo->prepare("SELECT s.*, sc.name as category_name 
                           FROM services s 
                           JOIN service_categories sc ON s.category_id = sc.id 
                           WHERE s.category_id IN ($placeholders) 
                           ORDER BY sc.name, s.name");
    $stmt->execute($categoryIds);
    $services = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الخدمات والأسعار - لوحة التحكم</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <style>
        :root {
            --primary-color: #3f2fbb;
            --secondary-color: #f8f9fa;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --light-gray: #f1f1f1;
            --dark-gray: #343a40;
            --border-radius: 8px;
            --box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
        }
        
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 250px;
            background: var(--dark-gray);
            color: white;
            padding: 20px 0;
            position: fixed;
            height: 100%;
            overflow-y: auto;
        }
        
        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }
        
        .sidebar-menu {
            padding: 0;
            list-style: none;
        }
        
        .sidebar-menu li {
            margin-bottom: 5px;
        }
        
        .sidebar-menu a {
            display: block;
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .sidebar-menu a:hover, 
        .sidebar-menu a.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border-right: 3px solid var(--primary-color);
        }
        
        .sidebar-menu i {
            margin-left: 10px;
            width: 20px;
            text-align: center;
        }
        
        .main-content {
            flex: 1;
            margin-right: 250px;
            padding: 20px;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .page-title {
            color: var(--primary-color);
            margin: 0;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            padding: 8px 15px;
            border: none;
            border-radius: var(--border-radius);
            background: var(--primary-color);
            color: white;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn i {
            margin-left: 5px;
        }
        
        .btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }
        
        .btn-success {
            background: var(--success-color);
        }
        
        .btn-danger {
            background: var(--danger-color);
        }
        
        .btn-warning {
            background: var(--warning-color);
            color: #000;
        }
        
        .card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 20px;
            overflow: hidden;
        }
        
        .card-header {
            padding: 15px 20px;
            background: #f8f9fa;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-title {
            margin: 0;
            font-size: 1.1rem;
            color: var(--dark-gray);
        }
        
        .card-body {
            padding: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-family: 'Tajawal', sans-serif;
            font-size: 1rem;
        }
        
        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }
        
        .table th,
        .table td {
            padding: 12px 15px;
            text-align: right;
            border-bottom: 1px solid #eee;
        }
        
        .table th {
            background: #f8f9fa;
            font-weight: 600;
            color: var(--dark-gray);
        }
        
        .table tbody tr:hover {
            background: #f8f9fa;
        }
        
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .badge-success {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }
        
        .alert {
            padding: 12px 20px;
            border-radius: var(--border-radius);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert i {
            margin-left: 10px;
        }
        
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }
        
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            right: 0;
            left: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }
        
        .slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        
        input:checked + .slider {
            background-color: var(--success-color);
        }
        
        input:checked + .slider:before {
            transform: translateX(-26px);
        }
        
        .price-input {
            max-width: 120px;
            text-align: left;
            direction: ltr;
        }
        
        @media (max-width: 992px) {
            .sidebar {
                right: -250px;
                z-index: 1000;
            }
            
            .sidebar.active {
                right: 0;
            }
            
            .main-content {
                margin-right: 0;
            }
            
            .mobile-menu-toggle {
                display: block;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- القائمة الجانبية -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h3>لوحة التحكم</h3>
            </div>
            <ul class="sidebar-menu">
                <li><a href="index.php"><i class="fas fa-home"></i> الرئيسية</a></li>
                <li><a href="profile.php"><i class="fas fa-user"></i> الملف الشخصي</a></li>
                <li><a href="services.php" class="active"><i class="fas fa-cogs"></i> الخدمات والأسعار</a></li>
                <li><a href="bookings.php"><i class="fas fa-calendar-check"></i> الحجوزات</a></li>
                <li><a href="reports.php"><i class="fas fa-chart-bar"></i> التقارير</a></li>
                <li><a href="settings.php"><i class="fas fa-cog"></i> الإعدادات</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a></li>
            </ul>
        </div>

        <!-- المحتوى الرئيسي -->
        <div class="main-content">
            <div class="page-header">
                <h1 class="page-title">إدارة الخدمات والأسعار</h1>
                <button class="btn" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                    <i class="fas fa-plus"></i> إضافة فئة جديدة
                </button>
            </div>

            <!-- رسائل النجاح والخطأ -->
            <?php if ($message): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <!-- نموذج إضافة فئة جديدة -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">إضافة فئة خدمة جديدة</h2>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="name">اسم الفئة</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="description">الوصف (اختياري)</label>
                                    <textarea class="form-control" id="description" name="description" rows="1"></textarea>
                                </div>
                            </div>
                        </div>
                        <button type="submit" name="add_category" class="btn btn-success">
                            <i class="fas fa-save"></i> حفظ الفئة
                        </button>
                    </form>
                </div>
            </div>

            <!-- جدول الفئات والخدمات -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">الخدمات والأسعار</h2>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <?php if (empty($categories)): ?>
                            <div class="text-center py-4">
                                <p>لا توجد فئات خدمات مضافة حتى الآن</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($categories as $category): ?>
                                <div class="category-section mb-4">
                                    <h4 class="mb-3">
                                        <i class="fas fa-folder"></i> <?php echo htmlspecialchars($category['name']); ?>
                                        <?php if (!empty($category['description'])): ?>
                                            <small class="text-muted">- <?php echo htmlspecialchars($category['description']); ?></small>
                                        <?php endif; ?>
                                    </h4>
                                    
                                    <?php 
                                    $categoryServices = array_filter($services, function($service) use ($category) {
                                        return $service['category_id'] == $category['id'];
                                    });
                                    ?>
                                    
                                    <?php if (empty($categoryServices)): ?>
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i> لا توجد خدمات مضافة لهذه الفئة
                                        </div>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>اسم الخدمة</th>
                                                        <th>السعر (ر.س)</th>
                                                        <th>الحالة</th>
                                                        <th>تاريخ التحديث</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($categoryServices as $service): ?>
                                                        <tr>
                                                            <td>
                                                                <strong><?php echo htmlspecialchars($service['name']); ?></strong>
                                                                <?php if (!empty($service['description'])): ?>
                                                                    <p class="text-muted mb-0" style="font-size: 0.85rem;">
                                                                        <?php echo nl2br(htmlspecialchars($service['description'])); ?>
                                                                    </p>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <input 
                                                                    type="number" 
                                                                    class="form-control price-input" 
                                                                    name="services[<?php echo $service['id']; ?>][price]" 
                                                                    value="<?php echo number_format($service['price'], 2, '.', ''); ?>" 
                                                                    step="0.01"
                                                                    min="0"
                                                                    required
                                                                >
                                                            </td>
                                                            <td>
                                                                <label class="toggle-switch">
                                                                    <input 
                                                                        type="checkbox" 
                                                                        name="services[<?php echo $service['id']; ?>][is_active]" 
                                                                        value="1" 
                                                                        <?php echo $service['is_active'] ? 'checked' : ''; ?>
                                                                    >
                                                                    <span class="slider"></span>
                                                                </label>
                                                            </td>
                                                            <td>
                                                                <?php echo date('Y/m/d', strtotime($service['updated_at'])); ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="text-left mt-2 mb-4">
                                        <a href="add_service.php?category_id=<?php echo $category['id']; ?>" class="btn btn-sm btn-success">
                                            <i class="fas fa-plus"></i> إضافة خدمة جديدة
                                        </a>
                                    </div>
                                </div>
                                <hr>
                            <?php endforeach; ?>
                            
                            <div class="text-left mt-4">
                                <button type="submit" name="update_prices" class="btn btn-primary">
                                    <i class="fas fa-save"></i> حفظ التغييرات
                                </button>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // تفعيل القائمة الجانبية على الأجهزة المحمولة
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mobileMenuToggle = document.createElement('button');
            
            mobileMenuToggle.className = 'btn btn-primary mobile-menu-toggle d-md-none';
            mobileMenuToggle.style.position = 'fixed';
            mobileMenuToggle.style.bottom = '20px';
            mobileMenuToggle.style.right = '20px';
            mobileMenuToggle.style.zIndex = '1000';
            mobileMenuToggle.innerHTML = '<i class="fas fa-bars"></i>';
            
            document.body.appendChild(mobileMenuToggle);
            
            mobileMenuToggle.addEventListener('click', function() {
                sidebar.classList.toggle('active');
            });
            
            // إغلاق القائمة عند النقر خارجها
            document.addEventListener('click', function(event) {
                if (!sidebar.contains(event.target) && event.target !== mobileMenuToggle) {
                    sidebar.classList.remove('active');
                }
            });
        });
    </script>
</body>
</html>
