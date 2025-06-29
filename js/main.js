/**
 * ملف JavaScript الرئيسي لموقع إبداع العرب
 * يحتوي على الشيفرات البرمجية الأساسية للموقع
 */

// تهيئة التطبيق عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', () => {
    initNavigation();
    initScrollEffects();
    initForms();
    initSmoothScroll();
    initImageOptimization();
    initScrollToTop();
    initCopyPhoneNumber();
    initCardEffects();
    initLightbox();
    initAnimateOnScroll();
});

/**
 * تهيئة القائمة والتنقل
 */
function initNavigation() {
    const menuToggle = document.getElementById('menu-toggle');
    const menu = document.getElementById('menu');
    const header = document.getElementById('header');

    // تفعيل زر القائمة المتنقلة
    if (menuToggle && menu) {
        menuToggle.addEventListener('click', () => {
            menuToggle.classList.toggle('active');
            menu.classList.toggle('active');
            document.body.classList.toggle('menu-open');
        });
    }

    // إغلاق القائمة عند النقر على رابط
    const menuLinks = document.querySelectorAll('.menu-link, .nav-links a');
    menuLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (menuToggle) menuToggle.classList.remove('active');
            if (menu) menu.classList.remove('active');
            document.body.classList.remove('menu-open');
        });
    });
}

/**
 * تأثيرات التمرير للهيدر
 */
function initScrollEffects() {
    const header = document.getElementById('header');
    if (!header) return;

    let lastScroll = 0;

    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;
        
        if (currentScroll <= 0) {
            header.classList.remove('scroll-up');
            return;
        }
        
        if (currentScroll > lastScroll && !header.classList.contains('scroll-down')) {
            header.classList.remove('scroll-up');
            header.classList.add('scroll-down');
        } else if (currentScroll < lastScroll && header.classList.contains('scroll-down')) {
            header.classList.remove('scroll-down');
            header.classList.add('scroll-up');
        }
        
        lastScroll = currentScroll;
    });
}

/**
 * تأثيرات البطاقات
 */
function initCardEffects() {
    const cards = document.querySelectorAll('.card');
    if (!cards.length) return;

    cards.forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            
            const rotateX = (y - centerY) / 20;
            const rotateY = (centerX - x) / 20;
            
            card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.02, 1.02, 1.02)`;
        });
        
        card.addEventListener('mouseleave', () => {
            card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) scale3d(1, 1, 1)';
        });
    });
}

/**
 * تأثيرات التمرير للعناصر
 */
function initAnimateOnScroll() {
    const animateOnScroll = () => {
        const elements = document.querySelectorAll('.animate-on-scroll');
        if (!elements.length) return;
        
        elements.forEach(element => {
            const elementPosition = element.getBoundingClientRect().top;
            const screenPosition = window.innerHeight / 1.3;
            
            if (elementPosition < screenPosition) {
                element.classList.add('animated');
            }
        });
    };

    window.addEventListener('scroll', animateOnScroll);
    animateOnScroll(); // تشغيل مرة واحدة عند التحميل
}

/**
 * تهيئة Lightbox
 */
function initLightbox() {
    if (typeof lightbox !== 'undefined') {
        lightbox.option({
            'resizeDuration': 200,
            'wrapAround': true,
            'disableScrolling': true,
            'albumLabel': 'صورة %1 من %2'
        });
    }
}

/**
 * تهيئة النماذج
 */
function initForms() {
    const contactForm = document.getElementById('contact-form');
    if (!contactForm) return;

    contactForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = this.querySelector('button[type="submit"]');
        if (!submitBtn) return;
        
        const originalText = submitBtn.textContent;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الإرسال...';
        
        // محاكاة إرسال النموذج
        setTimeout(() => {
            submitBtn.innerHTML = '<i class="fas fa-check"></i> تم الإرسال بنجاح';
            contactForm.reset();
            
            // إعادة تعيين الزر بعد 3 ثواني
            setTimeout(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }, 3000);
        }, 1500);
    });
}

/**
 * التمرير السلس للروابط الداخلية
 */
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                e.preventDefault();
                window.scrollTo({
                    top: targetElement.offsetTop - 100,
                    behavior: 'smooth'
                });
            }
        });
    });
}

/**
 * تحسين تحميل الصور
 */
function initImageOptimization() {
    // سيتم التعامل مع هذا في ملف image-optimizer.js
    if (window.ImageOptimizer && typeof window.ImageOptimizer.init === 'function') {
        window.ImageOptimizer.init();
    }
}

/**
 * زر العودة للأعلى
 */
function initScrollToTop() {
    const scrollToTopBtn = document.createElement('button');
    scrollToTopBtn.className = 'scroll-to-top';
    scrollToTopBtn.setAttribute('aria-label', 'العودة للأعلى');
    scrollToTopBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
    document.body.appendChild(scrollToTopBtn);

    scrollToTopBtn.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });

    const toggleScrollButton = () => {
        if (window.pageYOffset > 300) {
            scrollToTopBtn.classList.add('show');
        } else {
            scrollToTopBtn.classList.remove('show');
        }
    };

    window.addEventListener('scroll', toggleScrollButton);
    toggleScrollButton(); // التحقق من الحالة الأولية
}

/**
 * نسخ رقم الهاتف عند النقر
 */
function initCopyPhoneNumber() {
    const phoneLinks = document.querySelectorAll('a[href^="tel:"]');
    if (!phoneLinks.length) return;

    phoneLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const phoneNumber = this.getAttribute('href').replace('tel:', '');
            if (!phoneNumber) return;

            navigator.clipboard.writeText(phoneNumber).then(() => {
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-check"></i> تم النسخ';
                
                setTimeout(() => {
                    this.innerHTML = originalText;
                }, 2000);
            }).catch(err => {
                console.error('فشل في نسخ رقم الهاتف:', err);
            });
        });
    });
}

/**
 * تحسين تجربة المستخدم على الأجهزة التي تعمل باللمس
 */
function initViewportUnits() {
    const setVh = () => {
        const vh = window.innerHeight * 0.01;
        document.documentElement.style.setProperty('--vh', `${vh}px`);
    };
    
    setVh();
    window.addEventListener('resize', setVh);
    window.addEventListener('orientationchange', setVh);
}

/**
 * تحميل الصفحة
 */
function initPageLoad() {
    document.body.classList.add('loaded');
    
    const pageLoader = document.querySelector('.page-loader');
    if (!pageLoader) return;
    
    setTimeout(() => {
        pageLoader.style.opacity = '0';
        setTimeout(() => {
            pageLoader.style.display = 'none';
        }, 500);
    }, 500);
}

// تهيئة إضافية عند تحميل الصفحة بالكامل
window.addEventListener('load', () => {
    initViewportUnits();
    initPageLoad();
});
