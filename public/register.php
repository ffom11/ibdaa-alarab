<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إنشاء حساب جديد - إبداع العرب</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
            --success-color: #27ae60;
            --text-color: #333;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Tajawal', sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: var(--text-color);
            line-height: 1.6;
            min-height: 100vh;
            padding: 40px 0;
        }
        
        .register-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            overflow: hidden;
        }
        
        .register-header {
            background: linear-gradient(135deg, var(--primary-color), #1a252f);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .register-header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .register-header p {
            opacity: 0.9;
            font-size: 15px;
        }
        
        .register-body {
            padding: 30px;
            display: grid;
            grid-template-columns: 1fr 1px 1fr;
            gap: 30px;
        }
        
        .divider {
            background-color: #eee;
            position: relative;
        }
        
        .divider span {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 0 15px;
            color: #777;
            font-size: 14px;
        }
        
        .register-form {
            padding: 0 15px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark-color);
            font-size: 14px;
        }
        
        .input-with-icon {
            position: relative;
        }
        
        .input-with-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 16px;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 20px 12px 45px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 15px;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(44, 62, 80, 0.1);
        }
        
        .name-fields {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .btn {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }
        
        .btn:hover {
            background-color: #1a252f;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .btn-primary {
            background-color: var(--secondary-color);
        }
        
        .btn-primary:hover {
            background-color: #c0392b;
        }
        
        .terms {
            font-size: 13px;
            color: #666;
            margin: 20px 0;
            text-align: center;
        }
        
        .terms a {
            color: var(--secondary-color);
            text-decoration: none;
        }
        
        .login-link {
            text-align: center;
            margin-top: 25px;
            font-size: 14px;
            color: #666;
        }
        
        .login-link a {
            color: var(--secondary-color);
            text-decoration: none;
            font-weight: 500;
            margin-right: 5px;
        }
        
        .social-register {
            text-align: center;
            margin-top: 30px;
        }
        
        .social-register p {
            color: #777;
            margin-bottom: 15px;
            font-size: 14px;
            position: relative;
        }
        
        .social-register p::before,
        .social-register p::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 30%;
            height: 1px;
            background-color: #eee;
        }
        
        .social-register p::before {
            right: 0;
        }
        
        .social-register p::after {
            left: 0;
        }
        
        .social-icons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
        }
        
        .social-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background-color: #f5f5f5;
            color: #555;
            font-size: 20px;
            text-decoration: none;
            transition: all 0.3s;
            border: 1px solid #eee;
        }
        
        .social-icon:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .facebook {
            color: #1877f2;
        }
        
        .twitter {
            color: #1da1f2;
        }
        
        .google {
            color: #db4437;
        }
        
        @media (max-width: 768px) {
            .register-body {
                grid-template-columns: 1fr;
            }
            
            .divider {
                display: none;
            }
            
            .register-form {
                padding: 0;
            }
        }
        
        @media (max-width: 480px) {
            .register-container {
                margin: 0 15px;
            }
            
            .register-header {
                padding: 25px 20px;
            }
            
            .register-body {
                padding: 25px 20px;
            }
            
            .name-fields {
                grid-template-columns: 1fr;
                gap: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h1>أنشئ حسابًا جديدًا</h1>
            <p>انضم إلى مجتمع إبداع العرب وتمتع بمزايا حصرية</p>
        </div>
        
        <div class="register-body">
            <div class="register-form">
                <h3>معلومات الحساب</h3>
                <form action="process_register.php" method="POST">
                    <div class="form-group">
                        <div class="name-fields">
                            <div>
                                <label for="first_name">الاسم الأول</label>
                                <div class="input-with-icon">
                                    <i class="fas fa-user"></i>
                                    <input type="text" id="first_name" name="first_name" class="form-control" placeholder="الاسم الأول" required>
                                </div>
                            </div>
                            <div>
                                <label for="last_name">اسم العائلة</label>
                                <div class="input-with-icon">
                                    <i class="fas fa-user"></i>
                                    <input type="text" id="last_name" name="last_name" class="form-control" placeholder="اسم العائلة" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">البريد الإلكتروني</label>
                        <div class="input-with-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="email" name="email" class="form-control" placeholder="أدخل بريدك الإلكتروني" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">رقم الجوال</label>
                        <div class="input-with-icon">
                            <i class="fas fa-phone"></i>
                            <input type="tel" id="phone" name="phone" class="form-control" placeholder="أدخل رقم جوالك" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">كلمة المرور</label>
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password" name="password" class="form-control" placeholder="اختر كلمة مرور قوية" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">تأكيد كلمة المرور</label>
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="أعد إدخال كلمة المرور" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">إنشاء حساب</button>
                    </div>
                    
                    <div class="terms">
                        بإنشائك للحساب فإنك توافق على <a href="terms.php">شروط الاستخدام</a> و <a href="privacy.php">سياسة الخصوصية</a> الخاصة بنا
                    </div>
                </form>
                
                <div class="login-link">
                    لديك حساب بالفعل؟ <a href="login.php">تسجيل الدخول</a>
                </div>
            </div>
            
            <div class="divider">
                <span>أو</span>
            </div>
            
            <div class="social-register">
                <h3>سجل الدخول باستخدام</h3>
                <p>يمكنك تسجيل الدخول باستخدام حسابات التواصل الاجتماعي</p>
                
                <div class="social-icons">
                    <a href="#" class="social-icon facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="social-icon twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="social-icon google">
                        <i class="fab fa-google"></i>
                    </a>
                </div>
                
                <div class="benefits" style="margin-top: 40px; text-align: right;">
                    <h4 style="margin-bottom: 15px; color: var(--primary-color);">مميزات الانضمام إلينا:</h4>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 10px; position: relative; padding-right: 25px;">
                            <i class="fas fa-check" style="color: var(--success-color); position: absolute; right: 0; top: 5px;"></i>
                            <span>وصول إلى جميع الخدمات المميزة</span>
                        </li>
                        <li style="margin-bottom: 10px; position: relative; padding-right: 25px;">
                            <i class="fas fa-check" style="color: var(--success-color); position: absolute; right: 0; top: 5px;"></i>
                            <span>تحديثات فورية على المشاريع</span>
                        </li>
                        <li style="margin-bottom: 10px; position: relative; padding-right: 25px;">
                            <i class="fas fa-check" style="color: var(--success-color); position: absolute; right: 0; top: 5px;"></i>
                            <span>دعم فني على مدار الساعة</span>
                        </li>
                        <li style="position: relative; padding-right: 25px;">
                            <i class="fas fa-check" style="color: var(--success-color); position: absolute; right: 0; top: 5px;"></i>
                            <span>خصومات حصرية للأعضاء</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // التحقق من تطابق كلمتي المرور
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        
        function validatePassword() {
            if (password.value !== confirmPassword.value) {
                confirmPassword.setCustomValidity('كلمتا المرور غير متطابقتين');
            } else {
                confirmPassword.setCustomValidity('');
            }
        }
        
        password.onchange = validatePassword;
        confirmPassword.onkeyup = validatePassword;
        
        // إضافة تأثيرات تفاعلية للحقول
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                const icon = this.parentElement.querySelector('i');
                if (icon) {
                    icon.style.color = '#e74c3c';
                }
            });
            
            input.addEventListener('blur', function() {
                const icon = this.parentElement.querySelector('i');
                if (icon) {
                    icon.style.color = '#999';
                }
            });
        });
    </script>
</body>
</html>
