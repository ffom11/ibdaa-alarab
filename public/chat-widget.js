// عناصر واجهة الدردشة
const chatWidget = {
    // تهيئة الدردشة
    init: function() {
        this.createChatButton();
        this.createChatWindow();
        this.setupEventListeners();
    },

    // إنشاء زر الدردشة العائم
    createChatButton: function() {
        const chatButton = document.createElement('button');
        chatButton.id = 'chat-float-button';
        chatButton.className = 'float-button';
        chatButton.title = 'الدردشة مع الدعم';
        chatButton.innerHTML = `
            <i class="fas fa-comment-dots"></i>
            <span class="pulse"></span>
        `;
        document.body.appendChild(chatButton);
    },

    // إنشاء نافذة الدردشة
    createChatWindow: function() {
        const chatWindow = document.createElement('div');
        chatWindow.id = 'chat-window';
        chatWindow.className = 'chat-window';
        document.body.appendChild(chatWindow);
    },

    // تحميل محتوى الدردشة
    loadChatContent: function() {
        const chatWindow = document.getElementById('chat-window');
        
        // إذا كان المحتوى غير محمل بعد
        if (chatWindow.children.length === 0) {
            // إنشاء عناصر الدردشة
            chatWindow.innerHTML = `
                <div class="chat-header">
                    <h3><i class="fas fa-comment-dots"></i> محادثة مباشرة</h3>
                    <div class="chat-controls">
                        <button class="chat-control-btn minimize-btn" title="تصغير">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button class="chat-control-btn close-btn" title="إغلاق">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="chat-messages">
                    <div class="message received bounceIn">
                        <div class="message-sender">
                            <i class="fas fa-headset"></i>
                            فريق الدعم
                            <i class="fas fa-check-double"></i>
                        </div>
                        <div class="message-content">مرحباً بك في خدمة الدعم الفني! كيف يمكنني مساعدتك اليوم؟</div>
                        <div class="message-time">
                            <i class="far fa-clock"></i>
                            ${getCurrentTime()}
                        </div>
                    </div>
                </div>
                <div class="chat-input-container">
                    <div class="input-wrapper">
                        <textarea id="message-input" placeholder="اكتب رسالتك هنا..." dir="rtl" rows="1"></textarea>
                        <button class="attach-btn" type="button" title="إرفاق ملف">
                            <i class="fas fa-paperclip"></i>
                        </button>
                        <button class="send-btn" type="button" title="إرسال">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            `;

            this.setupChatEvents();
        }
    },

    // إعداد أحداث الدردشة
    setupChatEvents: function() {
        const messageInput = document.getElementById('message-input');
        const sendButton = document.getElementById('send-message');
        const closeButton = document.getElementById('close-chat');
        const minimizeButton = document.getElementById('minimize-chat');
        const floatButton = document.getElementById('chat-float-button');
        const chatWindow = document.getElementById('chat-window');
        const attachButton = document.getElementById('attach-file');
        let chatVisible = false;

        // وظائف مساعدة
        function getCurrentTime() {
            const now = new Date();
            return now.toLocaleTimeString('ar-SA', { hour: '2-digit', minute: '2-digit' });
        }

        function addMessage(content, isUser = false) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${isUser ? 'sent' : 'received'} bounceIn`;
            
            const messageContent = `
                <div class="message-sender">
                    ${isUser ? 'أنت' : '<i class="fas fa-headset"></i> فريق الدعم'}
                    <i class="fas fa-check-double"></i>
                </div>
                <div class="message-content">${content}</div>
                <div class="message-time">
                    <i class="far fa-clock"></i>
                    ${getCurrentTime()}
                </div>
            `;
            
            messageDiv.innerHTML = messageContent;
            chatMessages.appendChild(messageDiv);
            scrollToBottom();
            
            return messageDiv;
        }

        function scrollToBottom() {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        function showTypingIndicator() {
            const typingIndicator = document.createElement('div');
            typingIndicator.className = 'typing-indicator';
            typingIndicator.innerHTML = `
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
            `;
            chatMessages.appendChild(typingIndicator);
            scrollToBottom();
            return typingIndicator;
        }

        function removeTypingIndicator(indicator) {
            if (indicator && indicator.parentNode) {
                indicator.remove();
            }
        }

        function sendMessage() {
            const message = messageInput.value.trim();
            if (message === '') return;

            // إضافة رسالة المستخدم
            addMessage(message, true);
            messageInput.value = '';
            autoResizeTextarea();

            // إظهار مؤشر الكتابة
            const typingIndicator = showTypingIndicator();

            // محاكاة الرد التلقائي
            setTimeout(() => {
                removeTypingIndicator(typingIndicator);
                
                const responses = [
                    'شكراً لرسالتك! كيف يمكنني مساعدتك اليوم؟',
                    'تم استلام رسالتك، هل هناك أي شيء آخر تحتاج إليه؟',
                    'سأقوم بالرد على استفسارك في أقرب وقت ممكن.',
                    'هل يمكنك توضيح استفسارك أكثر من فضلك؟',
                    'شكراً لتواصلك معنا، فريق الدعم سيسعد بمساعدتك.'
                ];
                
                const randomResponse = responses[Math.floor(Math.random() * responses.length)];
                addMessage(randomResponse, false);
                
                // إضافة تأثير الاهتزاز عند وصول رسالة جديدة إذا كانت النافذة مصغرة
                if (isMinimized) {
                    floatButton.classList.add('new-message-notification');
                    setTimeout(() => {
                        floatButton.classList.remove('new-message-notification');
                    }, 1000);
                }
            }, 1500);
        }

        function autoResizeTextarea() {
            const textarea = messageInput;
            textarea.style.height = 'auto';
            textarea.style.height = (textarea.scrollHeight) + 'px';
        }

        function toggleChat() {
            isChatVisible = !isChatVisible;
            document.body.classList.toggle('chat-visible', isChatVisible);
            
            if (isChatVisible) {
                messageInput.focus();
                floatButton.classList.remove('glow');
                newMessageNotification = false;
            } else {
                if (chatWidget.classList.contains('minimized')) {
                    chatWidget.classList.remove('minimized');
                    isMinimized = false;
                }
            }
        }

        function toggleMinimize() {
            isMinimized = !isMinimized;
            chatWidget.classList.toggle('minimized', isMinimized);
            
            if (!isMinimized) {
                setTimeout(() => {
                    messageInput.focus();
                }, 300);
            }
        }

        // أحداث النقر
        if (sendButton) {
            sendButton.addEventListener('click', sendMessage);
        }
        
        if (messageInput) {
            messageInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    sendMessage();
                }
            });
        }
        
        if (closeButton) {
            closeButton.addEventListener('click', function() {
                chatVisible = false;
                document.body.classList.remove('chat-visible');
            });
        }

        if (minimizeButton) {
            minimizeButton.addEventListener('click', function() {
                chatWindow.classList.toggle('minimized');
                if (chatWindow.classList.contains('minimized')) {
                    minimizeButton.innerHTML = '<i class="fas fa-plus"></i>';
                    minimizeButton.title = 'تكبير';
                } else {
                    minimizeButton.innerHTML = '<i class="fas fa-minus"></i>';
                    minimizeButton.title = 'تصغير';
                }
            });
        }

        if (attachButton) {
            attachButton.addEventListener('click', function() {
                // يمكن إضافة وظيفة رفع الملفات هنا
                alert('سيتم تفعيل خاصية رفع الملفات قريباً');
            });
        }

        // فتح وإغلاق نافذة الدردشة
        if (floatButton) {
            floatButton.addEventListener('click', function(e) {
                e.stopPropagation();
                chatVisible = true;
                document.body.classList.add('chat-visible');
                chatWidget.loadChatContent();
            });
        }
        
        // إغلاق النافذة عند النقر خارجها
        document.addEventListener('click', function(e) {
            if (chatVisible && 
                !chatWindow.contains(e.target) && 
                e.target !== floatButton && 
                !floatButton.contains(e.target)) {
                chatVisible = false;
                document.body.classList.remove('chat-visible');
            }
        });
        
        // منع إغلاق النافذة عند النقر داخلها
        chatWindow.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    },

    // إعداد مستمعي الأحداث
    setupEventListeners: function() {
        // سيتم استدعاء loadChatContent عند النقر على زر الدردشة
    }
};

// تهيئة الدردشة عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    chatWidget.init();
});
