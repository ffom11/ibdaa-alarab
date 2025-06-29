// كود جافاسكريبت للتفاعلية

// عند تحميل الصفحة
window.addEventListener('DOMContentLoaded', function() {
    // تنشيط القوائم المنسدلة
    initDropdowns();
    
    // إضافة تأثير التمرير السلس للروابط
    initSmoothScroll();
    
    // إضافة تأثير التمرير للعناصر
    initScrollAnimations();
    
    // تهيئة نموذج الحجز
    initBookingForm();
});

// تهيئة القوائم المنسدلة
function initDropdowns() {
    const dropdowns = document.querySelectorAll('.dropdown');
    
    dropdowns.forEach(dropdown => {
        const toggle = dropdown.querySelector('.dropdown-toggle');
        const menu = dropdown.querySelector('.dropdown-menu');
        
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            menu.classList.toggle('show');
            this.classList.toggle('active');
        });
        
        // إغلاق القائمة عند النقر خارجها
        document.addEventListener('click', function(e) {
            if (!dropdown.contains(e.target)) {
                menu.classList.remove('show');
                toggle.classList.remove('active');
            }
        });
    });
}

// إضافة تأثير التمرير السلس للروابط
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 80,
                    behavior: 'smooth'
                });
                
                // إغلاق القائمة المنسدلة إذا كانت مفتوحة
                const navbarToggler = document.querySelector('.navbar-toggler');
                if (navbarToggler && !navbarToggler.classList.contains('collapsed')) {
                    navbarToggler.click();
                }
            }
        });
    });
}

// إضافة تأثيرات التمرير للعناصر
function initScrollAnimations() {
    const animateElements = document.querySelectorAll('.animate-on-scroll');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animated');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1
    });
    
    animateElements.forEach(element => {
        observer.observe(element);
    });
}

// تهيئة نموذج الحجز
function initBookingForm() {
    const bookingButtons = document.querySelectorAll('.package-button, .booking-button');
    const bookingModal = document.getElementById('bookingModal');
    const closeModal = document.querySelector('.close-modal');
    const bookingForm = document.getElementById('bookingForm');
    const packageSelect = document.getElementById('packageSelect');
    
    // فتح نافذة الحجز
    bookingButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const packageName = this.getAttribute('data-package') || '';
            
            if (packageSelect && packageName) {
                for (let i = 0; i < packageSelect.options.length; i++) {
                    if (packageSelect.options[i].value === packageName) {
                        packageSelect.selectedIndex = i;
                        break;
                    }
                }
            }
            
            bookingModal.classList.add('show');
            document.body.style.overflow = 'hidden';
        });
    });
    
    // إغلاق نافذة الحجز
    if (closeModal) {
        closeModal.addEventListener('click', function() {
            bookingModal.classList.remove('show');
            document.body.style.overflow = '';
        });
    }
    
    // إغلاق النافذة عند النقر خارجها
    window.addEventListener('click', function(e) {
        if (e.target === bookingModal) {
            bookingModal.classList.remove('show');
            document.body.style.overflow = '';
        }
    });
    
    // إرسال نموذج الحجز
    if (bookingForm) {
        bookingForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // هنا يمكنك إضافة كود إرسال النموذج
            const formData = new FormData(this);
            
            // عرض رسالة نجاح
            alert('تم استلام طلبك بنجاح! سنتواصل معك قريباً.');
            
            // إغلاق النافذة وإعادة تعيين النموذج
            bookingModal.classList.remove('show');
            document.body.style.overflow = '';
            this.reset();
        });
    }
}

// إضافة تأثير التمرير للشريط العلوي
window.addEventListener('scroll', function() {
    const header = document.querySelector('header');
    const navbar = document.querySelector('.navbar');
    const scrollPosition = window.scrollY;
    
    if (scrollPosition > 100) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
    
    // تأثير التعتيم للهيدر
    if (header) {
        const opacity = 1 - (scrollPosition / 500);
        header.style.opacity = opacity > 0.4 ? opacity : 0.4;
    }
});

// تهيئة عداد الزوار (مثال)
function initVisitorCounter() {
    const counterElement = document.getElementById('visitorCounter');
    if (counterElement) {
        // هنا يمكنك إضافة كود لجلب عدد الزوار الفعلي من الخادم
        let count = localStorage.getItem('visitorCount') || 1000;
        count = parseInt(count) + 1;
        localStorage.setItem('visitorCount', count);
        
        // تأثير العد
        let current = 0;
        const target = count;
        const increment = target / 50;
        
        const updateCounter = () => {
            if (current < target) {
                current += increment;
                counterElement.textContent = Math.ceil(current).toLocaleString();
                requestAnimationFrame(updateCounter);
            } else {
                counterElement.textContent = target.toLocaleString();
            }
        };
        
        updateCounter();
    }
}

// تهيئة شريط التقدم
function initScrollProgress() {
    const progressBar = document.querySelector('.scroll-progress');
    
    if (progressBar) {
        window.addEventListener('scroll', function() {
            const windowHeight = window.innerHeight;
            const documentHeight = document.documentElement.scrollHeight - windowHeight;
            const scrolled = window.scrollY;
            const progress = (scrolled / documentHeight) * 100;
            progressBar.style.width = progress + '%';
        });
    }
}

// تهيئة جميع الوظائف عند تحميل الصفحة
function initAll() {
    initDropdowns();
    initSmoothScroll();
    initScrollAnimations();
    initBookingForm();
    initVisitorCounter();
    initScrollProgress();
}

// تشغيل التهيئة عند تحميل الصفحة بالكامل
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAll);
} else {
    initAll();
}
