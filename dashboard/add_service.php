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

// جلب الفئات المتاحة
$categories = $pdo->query("SELECT * FROM service_categories WHERE is_active = 1 ORDER BY name")->fetchAll();

// معالجة إضافة خدمة جديدة
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_service'])) {
    $name = trim($_POST['name']);
    $category_id = (int)$_POST['category_id'];
    $description = trim($_POST['description']);
    $price = filter_var($_POST['price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $duration = !empty($_POST['duration']) ? (int)$_POST['duration'] : null;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // التحقق من صحة البيانات
    if (empty($name)) {
        $error = 'اسم الخدمة مطلوب';
    } elseif ($category_id <= 0) {
        $error = 'يرجى اختيار فئة صالحة';
    } elseif ($price === false || $price < 0) {
        $error = 'يرجى إدخال سعر صحيح';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO services (name, category_id, description, price, duration, is_active) 
                                  VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $category_id, $description, $price, $duration, $is_active]);
            
            $message = 'تمت إضافة الخدمة بنجاح';
            
            // إعادة تعيين القيم لإفراغ النموذج
            $name = $description = '';
            $price = 0;
            $duration = null;
            $is_active = 1;
        } catch (PDOException $e) {
            $error = 'حدث خطأ أثناء إضافة الخدمة: ' . $e->getMessage();
        }
    }
}

// إذا تم تمرير معرف الفئة في الرابط
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة خدمة جديدة - لوحة التحكم</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <style>
        /* نفس الأنماط المستخدمة في صفحة services.php */
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
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
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
            max-width: 200px;
            text-align: left;
            direction: ltr;
        }
        
        .duration-input {
            max-width: 150px;
        }
        
        .form-actions {
            margin-top: 30px;
            display: flex;
            gap: 10px;
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
                <h1 class="page-title">إضافة خدمة جديدة</h1>
                <a href="services.php" class="btn">
                    <i class="fas fa-arrow-right"></i> العودة للقائمة
                </a>
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

            <!-- نموذج إضافة خدمة جديدة -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">معلومات الخدمة</h2>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="name">اسم الخدمة *</label>
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        id="name" 
                                        name="name" 
                                        value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" 
                                        required
                                    >
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="category_id">الفئة *</label>
                                    <select class="form-control" id="category_id" name="category_id" required>
                                        <option value="">اختر الفئة</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo $category['id']; ?>" 
                                                <?php echo (isset($category_id) && $category_id == $category['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($category['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="price">السعر (ر.س) *</label>
                                    <div class="input-group">
                                        <input 
                                            type="number" 
                                            class="form-control price-input" 
                                            id="price" 
                                            name="price" 
                                            step="0.01"
                                            min="0"
                                            value="<?php echo isset($price) ? number_format($price, 2, '.', '') : '0.00'; ?>" 
                                            required
                                        >
                                        <span class="input-group-text">ر.س</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="duration">مدة الخدمة (بالدقائق) - اختياري</label>
                                    <div class="input-group">
                                        <input 
                                            type="number" 
                                            class="form-control duration-input" 
                                            id="duration" 
                                            name="duration" 
                                            min="1"
                                            value="<?php echo isset($duration) ? $duration : ''; ?>"
                                        >
                                        <span class="input-group-text">دقيقة</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="description">وصف الخدمة</label>
                            <textarea 
                                class="form-control" 
                                id="description" 
                                name="description" 
                                rows="4"
                            ><?php echo isset($description) ? htmlspecialchars($description) : ''; ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">حالة الخدمة</label>
                            <div>
                                <label class="toggle-switch">
                                    <input 
                                        type="checkbox" 
                                        name="is_active" 
                                        value="1" 
                                        <?php echo (!isset($is_active) || $is_active) ? 'checked' : ''; ?>
                                    >
                                    <span class="slider"></span>
                                </label>
                                <span class="ms-2">تفعيل الخدمة</span>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" name="add_service" class="btn btn-success">
                                <i class="fas fa-save"></i> حفظ الخدمة
                            </button>
                            <a href="services.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> إلغاء
                            </a>
                        </div>
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
            
            // تنسيق السعر عند فقدان التركيز
            const priceInput = document.getElementById('price');
            if (priceInput) {
                priceInput.addEventListener('blur', function() {
                    const value = parseFloat(this.value);
                    if (!isNaN(value)) {
                        this.value = value.toFixed(2);
                    }
                });
            }
        });
    </script>
</body>
</html>
