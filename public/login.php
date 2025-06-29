<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - إبداع العرب</title>
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
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            padding: 40px;
            margin: 20px;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo h1 {
            color: var(--primary-color);
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .logo p {
            color: #666;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark-color);
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
        }
        
        .form-control {
            width: 100%;
            padding: 12px 20px 12px 45px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(44, 62, 80, 0.1);
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
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #1a252f;
        }
        
        .btn-primary {
            background-color: var(--secondary-color);
        }
        
        .btn-primary:hover {
            background-color: #c0392b;
        }
        
        .forgot-password {
            text-align: left;
            margin: 15px 0 25px;
        }
        
        .forgot-password a {
            color: var(--secondary-color);
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s;
        }
        
        .forgot-password a:hover {
            text-decoration: underline;
        }
        
        .register-link {
            text-align: center;
            margin-top: 25px;
            font-size: 14px;
        }
        
        .register-link a {
            color: var(--secondary-color);
            text-decoration: none;
            font-weight: 500;
            margin-right: 5px;
        }
        
        .social-login {
            margin-top: 30px;
            text-align: center;
        }
        
        .social-login p {
            position: relative;
            color: #777;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .social-login p::before,
        .social-login p::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 30%;
            height: 1px;
            background-color: #ddd;
        }
        
        .social-login p::before {
            right: 0;
        }
        
        .social-login p::after {
            left: 0;
        }
        
        .social-icons {
            display: flex;
            justify-content: center;
            gap: 15px;
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
        
        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
                margin: 15px;
            }
            
            .logo h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h1>إبداع العرب</h1>
            <p>مرحبًا بعودتك! يرجى تسجيل الدخول إلى حسابك</p>
        </div>
        
        <form action="process_login.php" method="POST">
            <div class="form-group">
                <label for="email">البريد الإلكتروني</label>
                <div class="input-with-icon">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" class="form-control" placeholder="أدخل بريدك الإلكتروني" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="password">كلمة المرور</label>
                <div class="input-with-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" class="form-control" placeholder="أدخل كلمة المرور" required>
                </div>
            </div>
            
            <div class="forgot-password">
                <a href="forgot-password.php">هل نسيت كلمة المرور؟</a>
            </div>
            
            <button type="submit" class="btn btn-primary">تسجيل الدخول</button>
            
            <div class="register-link">
                ليس لديك حساب؟ <a href="register.php">إنشاء حساب جديد</a>
            </div>
            
            <div class="social-login">
                <p>أو سجل الدخول باستخدام</p>
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
            </div>
        </form>
    </div>
    
    <script>
        // إضافة تأثيرات تفاعلية
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.querySelector('i').style.color = '#e74c3c';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.querySelector('i').style.color = '#999';
            });
        });
    </script>
</body>
</html>
