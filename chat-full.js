// ملف chat.js الكامل - يرجى نقله إلى assets/js/chat.js

document.addEventListener('DOMContentLoaded', function() {
    // عناصر واجهة المستخدم
    const messageInput = document.getElementById('messageInput');
    const sendButton = document.getElementById('sendMessage');
    const messagesContainer = document.getElementById('messagesContainer');
    const fileInput = document.getElementById('fileInput');
    const attachButton = document.getElementById('attachFile');
    
    // دالة إرسال الرسالة
    function sendMessage() {
        const message = messageInput ? messageInput.value.trim() : '';
        
        if (message || (fileInput && fileInput.files.length > 0)) {
            // إنشاء FormData لإرسال النص والملفات
            const formData = new FormData();
            
            if (message) {
                formData.append('message', message);
            }
            
            if (fileInput && fileInput.files.length > 0) {
                formData.append('file', fileInput.files[0]);
            }
            
            // هنا سيتم إضافة كود إرسال البيانات إلى الخادم
            console.log('إرسال رسالة:', message);
            
            // مسح حقول الإدخال بعد الإرسال
            if (messageInput) messageInput.value = '';
            if (fileInput) fileInput.value = '';
            
            // التمرير إلى آخر رسالة
            scrollToBottom();
        }
    }
    
    // دالة التمرير إلى آخر رسالة
    function scrollToBottom() {
        if (messagesContainer) {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
    }
    
    // إضافة مستمعات الأحداث
    if (sendButton) {
        sendButton.addEventListener('click', sendMessage);
    }
    
    if (messageInput) {
        messageInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
    }
    
    if (attachButton && fileInput) {
        attachButton.addEventListener('click', function() {
            fileInput.click();
        });
        
        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                // يمكن إضافة معاينة الملف هنا
                console.log('تم اختيار ملف:', this.files[0].name);
            }
        });
    }
    
    // التمرير إلى الأسفل عند تحميل الصفحة
    scrollToBottom();
    
    console.log('تم تحميل نظام الدردشة بنجاح');
});

// دالة لإظهار إشعار للمستخدم
function showNotification(message, type = 'info') {
    // يمكن تخصيص هذه الدالة لعرض إشعارات للمستخدم
    console.log(`[${type}] ${message}`);
}

// دالة لتنسيق التاريخ والوقت
function formatDateTime(timestamp) {
    const date = new Date(timestamp);
    return date.toLocaleString('ar-SA');
}
