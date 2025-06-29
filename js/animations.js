// مكتبة التأثيرات الحركية المخصصة
class ScrollAnimations {
    constructor() {
        this.animatedElements = [];
        this.scrollPosition = 0;
        this.init();
    }

    init() {
        // تهيئة العناصر المتحركة
        this.cacheElements();
        
        // إضافة مستمعات الأحداث
        window.addEventListener('scroll', this.throttle(this.handleScroll.bind(this), 16));
        window.addEventListener('resize', this.debounce(this.handleResize.bind(this), 100));
        
        // تشغيل التحقق الأولي
        this.checkElements();
    }

    // تخزين العناصر المتحركة
    cacheElements() {
        this.animatedElements = [];
        
        // العثور على جميع العناصر ذات السمة data-animate
        document.querySelectorAll('[data-animate]').forEach(element => {
            const options = {
                element: element,
                type: element.dataset.animate || 'fadeIn',
                delay: parseInt(element.dataset.delay) || 0,
                offset: parseFloat(element.dataset.offset) || 0.8,
                duration: parseFloat(element.dataset.duration) || 0.6,
                once: element.dataset.once !== 'false',
                animated: false
            };
            
            this.animatedElements.push(options);
            
            // تعيين الخصائص الأولية للعنصر
            this.setInitialStyles(element, options);
        });
    }

    // تعيين الأنماط الأولية للعناصر
    setInitialStyles(element, options) {
        element.style.opacity = '0';
        element.style.transition = `all ${options.duration}s cubic-bezier(0.4, 0, 0.2, 1) ${options.delay}s`;
        
        switch(options.type) {
            case 'fadeIn':
                element.style.transform = 'translateY(20px)';
                break;
            case 'fadeInLeft':
                element.style.transform = 'translateX(-30px)';
                break;
            case 'fadeInRight':
                element.style.transform = 'translateX(30px)';
                break;
            case 'zoomIn':
                element.style.transform = 'scale(0.9)';
                break;
            case 'flipInX':
                element.style.transform = 'perspective(1000px) rotateX(90deg)';
                element.style.backfaceVisibility = 'hidden';
                break;
            default:
                element.style.transform = 'translateY(20px)';
        }
    }

    // معالجة حدث التمرير
    handleScroll() {
        this.scrollPosition = window.scrollY || window.pageYOffset;
        this.checkElements();
    }

    // معالجة تغيير حجم النافذة
    handleResize() {
        this.checkElements();
    }

    // التحقق من العناصر وإضافة التأثيرات
    checkElements() {
        const windowHeight = window.innerHeight;
        const triggerOffset = windowHeight * 0.8;
        
        this.animatedElements.forEach(item => {
            if (item.animated && item.once) return;
            
            const elementTop = this.getOffsetTop(item.element);
            const elementHeight = item.element.offsetHeight;
            const elementVisible = triggerOffset;
            
            if (this.scrollPosition + elementVisible > elementTop && 
                this.scrollPosition < elementTop + elementHeight) {
                this.animateElement(item);
            } else if (!item.once) {
                this.resetElement(item);
            }
        });
    }

    // تطبيق التأثير على العنصر
    animateElement(item) {
        if (item.animated) return;
        
        item.animated = true;
        item.element.style.opacity = '1';
        
        switch(item.type) {
            case 'fadeIn':
            case 'fadeInLeft':
            case 'fadeInRight':
                item.element.style.transform = 'translate(0, 0)';
                break;
            case 'zoomIn':
                item.element.style.transform = 'scale(1)';
                break;
            case 'flipInX':
                item.element.style.transform = 'perspective(1000px) rotateX(0)';
                break;
            default:
                item.element.style.transform = 'translate(0, 0)';
        }
        
        // إزالة السمة بعد الانتهاء من التحريك
        if (item.once) {
            setTimeout(() => {
                item.element.style.transition = 'none';
            }, (item.delay + item.duration) * 1000);
        }
    }

    // إعادة تعيين العنصر إلى حالته الأولية
    resetElement(item) {
        if (!item.animated || item.once) return;
        
        item.animated = false;
        item.element.style.opacity = '0';
        
        switch(item.type) {
            case 'fadeIn':
                item.element.style.transform = 'translateY(20px)';
                break;
            case 'fadeInLeft':
                item.element.style.transform = 'translateX(-30px)';
                break;
            case 'fadeInRight':
                item.element.style.transform = 'translateX(30px)';
                break;
            case 'zoomIn':
                item.element.style.transform = 'scale(0.9)';
                break;
            case 'flipInX':
                item.element.style.transform = 'perspective(1000px) rotateX(90deg)';
                break;
            default:
                item.element.style.transform = 'translateY(20px)';
        }
    }


    // الحصول على المسافة من أعلى الصفحة للعنصر
    getOffsetTop(element) {
        let offsetTop = 0;
        while (element) {
            offsetTop += element.offsetTop;
            element = element.offsetParent;
        }
        return offsetTop;
    }


    // تقليل عدد مرات استدعاء الدالة (Throttle)
    throttle(callback, limit) {
        let wait = false;
        return function() {
            if (!wait) {
                callback.call();
                wait = true;
                setTimeout(() => {
                    wait = false;
                }, limit);
            }
        };
    }


    // تأخير تنفيذ الدالة (Debounce)
    debounce(callback, delay) {
        let timeoutId;
        return function() {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(callback, delay);
        };
    }
}

// تأثيرات حركية إضافية
const MotionEffects = {
    // تأثير الطفو (Hover)
    initHoverEffects() {
        const hoverElements = document.querySelectorAll('[data-hover-effect]');
        
        hoverElements.forEach(element => {
            const effect = element.dataset.hoverEffect || 'grow';
            
            element.addEventListener('mouseenter', () => {
                this.applyHoverEffect(element, effect, 'in');
            });
            
            element.addEventListener('mouseleave', () => {
                this.applyHoverEffect(element, effect, 'out');
            });
        });
    },
    
    // تطبيق تأثيرات Hover
    applyHoverEffect(element, effect, direction) {
        switch(effect) {
            case 'grow':
                element.style.transform = direction === 'in' ? 'scale(1.05)' : 'scale(1)';
                break;
            case 'float':
                element.style.transform = direction === 'in' ? 'translateY(-5px)' : 'translateY(0)';
                break;
            case 'tilt':
                element.style.transform = direction === 'in' ? 'rotate(2deg)' : 'rotate(0)';
                break;
            case 'shadow':
                element.style.boxShadow = direction === 'in' ? '0 10px 20px rgba(0,0,0,0.1)' : '0 2px 5px rgba(0,0,0,0.1)';
                break;
        }
        
        // إضافة انتقال سلس
        element.style.transition = 'all 0.3s ease-in-out';
    },
    
    // تأثيرات النقر
    initClickEffects() {
        const clickElements = document.querySelectorAll('[data-click-effect]');
        
        clickElements.forEach(element => {
            const effect = element.dataset.clickEffect || 'ripple';
            
            element.addEventListener('click', (e) => {
                this.applyClickEffect(e, element, effect);
            });
        });
    },
    
    // تطبيق تأثيرات النقر
    applyClickEffect(e, element, effect) {
        switch(effect) {
            case 'ripple':
                this.createRipple(e, element);
                break;
            case 'bounce':
                this.bounce(element);
                break;
            case 'pulse':
                this.pulse(element);
                break;
        }
    },
    
    // تأثير التموج (Ripple)
    createRipple(e, element) {
        // إنشاء عنصر التموج
        const ripple = document.createElement('span');
        const rect = element.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        
        // تعيين أنماط التموج
        ripple.style.width = ripple.style.height = `${size}px`;
        ripple.style.left = `${e.clientX - rect.left - size/2}px`;
        ripple.style.top = `${e.clientY - rect.top - size/2}px`;
        ripple.classList.add('ripple-effect');
        
        // إضافة التموج إلى العنصر
        element.appendChild(ripple);
        
        // إزالة التموج بعد الانتهاء
        setTimeout(() => {
            ripple.remove();
        }, 600);
    },
    
    // تأثير الارتداد (Bounce)
    bounce(element) {
        element.style.animation = 'none';
        void element.offsetWidth; // إعادة تدفق
        element.style.animation = 'bounce 0.5s';
    },
    
    // تأثير النبض (Pulse)
    pulse(element) {
        element.style.animation = 'none';
        void element.offsetWidth; // إعادة تدفق
        element.style.animation = 'pulse 0.5s';
    },
    
    // تهيئة جميع التأثيرات
    initAll() {
        this.initHoverEffects();
        this.initClickEffects();
    }
};

// تهيئة التأثيرات عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', () => {
    // تهيئة تأثيرات التمرير
    const scrollAnimations = new ScrollAnimations();
    
    // تهيئة التأثيرات التفاعلية
    MotionEffects.initAll();
    
    // إضافة أنماط CSS المخصصة
    this.addCustomStyles();
});

// إضافة أنماط CSS المخصصة
function addCustomStyles() {
    const style = document.createElement('style');
    style.textContent = `
        /* أنماط تأثير التموج */
        .ripple-effect {
            position: absolute;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.7);
            transform: scale(0);
            animation: ripple 0.6s linear;
            pointer-events: none;
        }
        
        @keyframes ripple {
            to {
                transform: scale(2.5);
                opacity: 0;
            }
        }
        
        /* أنماط تأثير الارتداد */
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {transform: translateY(0);}
            40% {transform: translateY(-20px);}
            60% {transform: translateY(-10px);}
        }
        
        /* أنماط تأثير النبض */
        @keyframes pulse {
            0% {transform: scale(1);}
            50% {transform: scale(1.05);}
            100% {transform: scale(1);}
        }
        
        /* تأثيرات Hover */
        [data-hover-effect] {
            transition: all 0.3s ease-in-out;
        }
    `;
    
    document.head.appendChild(style);
}

// تصدير الكائنات للاستخدام الخارجي
window.ScrollAnimations = ScrollAnimations;
window.MotionEffects = MotionEffects;
