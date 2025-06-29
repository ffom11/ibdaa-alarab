// تهيئة محسن الصور
class ImageOptimizer {
    constructor() {
        this.lazyImages = [];
        this.lazyLoadThrottleTimeout;
        this.init();
    }

    init() {
        // تحميل الصور الكسولة
        this.lazyImages = [].slice.call(document.querySelectorAll('img.lazy'));
        
        // إضافة مستمعات الأحداث
        document.addEventListener('DOMContentLoaded', this.lazyLoad.bind(this));
        window.addEventListener('scroll', this.throttle(this.lazyLoad.bind(this), 200));
        window.addEventListener('resize', this.throttle(this.lazyLoad.bind(this), 200));
        
        // تحسين الصور الموجودة
        this.optimizeExistingImages();
    }

    // تحميل الصور الكسولة عند ظهورها في نافذة العرض
    lazyLoad() {
        if (this.lazyLoadThrottleTimeout) {
            window.cancelAnimationFrame(this.lazyLoadThrottleTimeout);
        }

        this.lazyLoadThrottleTimeout = window.requestAnimationFrame(() => {
            let lazyImages = this.lazyImages.filter(img => img.classList.contains('lazy'));
            
            if (lazyImages.length === 0) {
                document.removeEventListener('scroll', this.lazyLoad);
                window.removeEventListener('resize', this.lazyLoad);
                window.removeEventListener('orientationchange', this.lazyLoad);
                return;
            }

            lazyImages.forEach(img => {
                if (this.isInViewport(img)) {
                    this.loadImage(img);
                }
            });

            this.lazyImages = this.lazyImages.filter(img => img.classList.contains('lazy'));
        });
    }


    // تحميل الصورة الفردية
    loadImage(img) {
        if (!img.dataset.src) return;
        
        const src = img.dataset.src;
        
        // إنشاء صورة جديدة للتحقق من التحميل
        const tempImg = new Image();
        tempImg.src = src;
        
        tempImg.onload = () => {
            // استبدال السورس الأصلي بالصورة المحملة
            img.src = src;
            img.classList.remove('lazy');
            img.classList.add('lazy-loaded');
            
            // إضافة تأثير ظهور تدريجي
            img.style.opacity = '0';
            img.style.transition = 'opacity 0.3s';
            
            // إجبار المتصفح على إعادة تدفق الصفحة
            void img.offsetWidth;
            
            // تعتيم الصورة تدريجياً
            img.style.opacity = '1';
            
            // تحديث سمة srcset إذا كانت موجودة
            if (img.dataset.srcset) {
                img.srcset = img.dataset.srcset;
            }
            
            // إزالة السمة data-src
            img.removeAttribute('data-src');
            img.removeAttribute('data-srcset');
        };
        
        tempImg.onerror = () => {
            console.error('فشل تحميل الصورة:', src);
            img.classList.add('lazy-error');
        };
    }
    
    // التحقق مما إذا كان العنصر مرئيًا في نافذة العرض
    isInViewport(el) {
        const rect = el.getBoundingClientRect();
        return (
            rect.bottom >= 0 &&
            rect.right >= 0 &&
            rect.top <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.left <= (window.innerWidth || document.documentElement.clientWidth)
        );
    }
    
    // تقليل عدد مرات استدعاء الدالة
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
    
    // تحسين الصور الموجودة في الصفحة
    optimizeExistingImages() {
        const images = document.querySelectorAll('img:not(.lazy)');
        
        images.forEach(img => {
            // إضافة سمة loading="lazy" للمتصفحات المدعومة
            if ('loading' in HTMLImageElement.prototype) {
                img.loading = 'lazy';
            }
            
            // تحسين سمة alt إذا كانت مفقودة
            if (!img.alt) {
                img.alt = 'صورة توضيحية';
            }
            
            // تحسين أبعاد الصورة
            if (!img.width || !img.height) {
                const tempImg = new Image();
                tempImg.onload = () => {
                    if (!img.width || !img.height) {
                        img.width = tempImg.width;
                        img.height = tempImg.height;
                    }
                };
                tempImg.src = img.src;
            }
        });
    }
    
    // تحويل الصور إلى تنسيق WebP إذا كان مدعومًا
    async convertToWebPIfSupported() {
        if (!this.supportsWebP()) return;
        
        const images = document.querySelectorAll('img[data-webp]');
        
        images.forEach(img => {
            const webpSrc = img.dataset.webp;
            if (webpSrc) {
                img.src = webpSrc;
            }
        });
    }
    
    // التحقق من دعم تنسيق WebP
    supportsWebP() {
        return new Promise(resolve => {
            const img = new Image();
            img.onload = () => resolve(img.width === 1);
            img.onerror = () => resolve(false);
            img.src = 'data:image/webp;base64,UklGRiIAAABXRUJQVlA4IBYAAAAwAQCdASoBAAEADsD+JaQAA3AAAAAA';
        });
    }
}

// تهيئة المحسن عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', () => {
    const imageOptimizer = new ImageOptimizer();
    
    // تحويل الصور إلى WebP إذا كان مدعومًا
    imageOptimizer.convertToWebPIfSupported();
    
    // تعطيل سحب وإفلات الصور
    document.addEventListener('dragstart', (e) => {
        if (e.target.tagName === 'IMG') {
            e.preventDefault();
            return false;
        }
    });
});

// دالة مساعدة لإضافة تأثير التحميل التدريجي للصور
function addProgressiveLoading(selector = '.progressive-image') {
    const images = document.querySelectorAll(selector);
    
    images.forEach(img => {
        // إضافة صورة مصغرة
        const thumbnail = img.src;
        const fullImage = img.dataset.full || thumbnail;
        
        // تعيين صورة مصغرة أولاً
        img.src = thumbnail;
        img.style.filter = 'blur(5px)';
        img.style.transition = 'filter 0.3s ease-out';
        
        // تحميل الصورة كاملة
        const tempImg = new Image();
        tempImg.src = fullImage;
        
        tempImg.onload = () => {
            img.src = fullImage;
            img.style.filter = 'blur(0)';
        };
    });
}

// تصدير الدوال للاستخدام الخارجي
window.ImageOptimizer = ImageOptimizer;
window.addProgressiveLoading = addProgressiveLoading;
