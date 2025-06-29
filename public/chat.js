// نظام الدردشة - إبداع العرب
document.addEventListener('DOMContentLoaded', function() {
    console.log('تم تحميل نظام الدردشة بنجاح');
    
    // إنشاء واجهة الدردشة
    createChatInterface();
    
    // إضافة مستمعات الأحداث
    setupEventListeners();
});

function createChatInterface() {
    // إنشاء عناصر واجهة المستخدم للدردشة
    const chatContainer = document.createElement('div');
    chatContainer.id = 'chat-container';
    chatContainer.innerHTML = `
        <div class="chat-header">
            <h3>الدردشة المباشرة</h3>
            <button id="minimize-chat" title="تصغير"><i class="fas fa-window-minimize"></i></button>
            <button id="close-chat" title="إغلاق"><i class="fas fa-times"></i></button>
        </div>
        <div class="chat-messages" id="chat-messages">
            <div class="message received">
                <div class="message-sender">فريق الدعم</div>
                <div class="message-content">مرحباً بك في خدمة الدردشة المباشرة! كيف يمكنني مساعدتك اليوم؟</div>
                <div class="message-time">الآن</div>
            </div>
        </div>
        <div class="chat-input-container">
            <div class="chat-input">
                <input type="text" id="message-input" placeholder="اكتب رسالتك هنا..." dir="rtl">
                <button id="send-message" title="إرسال">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(chatContainer);
    
    // إضافة أنماط CSS
    addChatStyles();
}

function setupEventListeners() {
    // إرسال الرسالة عند الضغط على زر الإرسال
    const sendButton = document.getElementById('send-message');
    const messageInput = document.getElementById('message-input');
    
    if (sendButton && messageInput) {
        sendButton.addEventListener('click', sendMessage);
        messageInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
    }
    
    // إغلاق وتصغير نافذة الدردشة
    const closeButton = document.getElementById('close-chat');
    const minimizeButton = document.getElementById('minimize-chat');
    
    if (closeButton) closeButton.addEventListener('click', closeChat);
    if (minimizeButton) minimizeButton.addEventListener('click', minimizeChat);
}

function sendMessage() {
    const messageInput = document.getElementById('message-input');
    const messagesContainer = document.getElementById('chat-messages');
    
    if (messageInput && messagesContainer && messageInput.value.trim() !== '') {
        const message = messageInput.value.trim();
        
        // إضافة الرسالة إلى واجهة المستخدم
        // إضافة رد آلي مع تنسيق أفضل
        const messageElement = document.createElement('div');
        messageElement.className = 'message received';
        
        // الحصول على الوقت الحالي
        const now = new Date();
        const timeString = now.toLocaleTimeString('ar-EG', { hour: '2-digit', minute: '2-digit' });
        
        // إضافة محتوى الرسالة مع التنسيق
        messageElement.innerHTML = `
            <div class="message-sender">فريق الدعم</div>
            <div class="message-content">${message}</div>
            <div class="message-time">${timeString}</div>
        `;
        messagesContainer.appendChild(messageElement);
        
        // مسح حقل الإدخال
        messageInput.value = '';
        
        // التمرير إلى آخر رسالة
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
        
        // هنا يمكنك إضافة كود إرسال الرسالة إلى الخادم
        console.log('تم إرسال الرسالة:', message);
    }
}

function closeChat() {
    const chatContainer = document.getElementById('chat-container');
    if (chatContainer) {
        chatContainer.style.display = 'none';
    }
}

function minimizeChat() {
    const messages = document.getElementById('chat-messages');
    const chatContainer = document.getElementById('chat-container');
    
    if (messages && chatContainer) {
        if (messages.style.display === 'none') {
            messages.style.display = 'block';
            chatContainer.style.height = '400px';
        } else {
            messages.style.display = 'none';
            chatContainer.style.height = '60px';
        }
    }
}

function addChatStyles() {
    const style = document.createElement('style');
    style.textContent = `
        #chat-container {
            position: fixed;
            bottom: 20px;
            left: 20px;
            width: 350px;
            height: 500px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 5px 30px rgba(0, 0, 0, 0.15);
            display: flex;
            flex-direction: column;
            z-index: 9999;
            font-family: 'Tajawal', sans-serif;
            overflow: hidden;
            transition: all 0.3s ease;
            border: 1px solid #e0e0e0;
        }
        
        .chat-header {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .chat-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .chat-header h3:before {
            content: '💬';
            font-size: 1.2em;
        }
        
        .chat-header button {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            font-size: 16px;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }
        
        .chat-header button:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }
        
        .chat-messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            direction: rtl;
            background-color: #f8f9fa;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M54 20v10h6V20a6 6 0 0 0-6-6h-6v6h6zM6 14v6h6v-6a6 6 0 0 1 6-6h6V2h-6C12.27 2 6 8.27 6 16v36c0 4.42 3.58 8 8 8h36c4.42 0 8-3.58 8-8V20h6v30c0 7.73-6.27 14-14 14H14C6.27 64 0 57.73 0 50V16c0-7.73 6.27-14 14-14h6v6h-6c-2.21 0-4 1.79-4 4z' fill='%23e9ecef' fill-opacity='0.2' fill-rule='evenodd'/%3E%3C/svg%3E");
        }
        
        .message {
            margin-bottom: 15px;
            padding: 10px 15px;
            border-radius: 18px;
            max-width: 80%;
            word-wrap: break-word;
            position: relative;
            line-height: 1.5;
            font-size: 14px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
            transition: transform 0.2s ease, opacity 0.2s ease;
        }
        
        .message:hover {
            transform: translateY(-2px);
            box-shadow: 0 3px 6px rgba(0,0,0,0.15);
        }
        
        .message.sent {
            background: #007bff;
            color: white;
            margin-right: auto;
            margin-left: 15%;
            text-align: right;
            border-bottom-right-radius: 5px;
        }
        
        .message.received {
            background: white;
            color: #333;
            margin-left: auto;
            margin-right: 15%;
            text-align: right;
            border: 1px solid #e0e0e0;
            border-bottom-left-radius: 5px;
        }
        
        .chat-input-container {
            padding: 15px;
            background: white;
            border-top: 1px solid #e0e0e0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .chat-input {
            flex: 1;
            position: relative;
        }
        
        .chat-input input {
            width: 100%;
            padding: 12px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 30px;
            font-family: 'Tajawal', sans-serif;
            font-size: 14px;
            transition: all 0.3s ease;
            padding-right: 50px;
        }
        
        .chat-input input:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.15);
        }
        
        .chat-input button {
            position: absolute;
            left: 5px;
            top: 50%;
            transform: translateY(-50%);
            background: #007bff;
            color: white;
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }
        
        .chat-input button:hover {
            background: #0056b3;
            transform: translateY(-50%) scale(1.05);
        }
        
        .chat-input button i {
            font-size: 16px;
        }
        
        /* Scrollbar styling */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
        
        /* Animation for new messages */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .message {
            animation: fadeIn 0.3s ease-out;
        }
        
        /* Responsive design */
        @media (max-width: 480px) {
            #chat-container {
                width: 100%;
                height: 100%;
                bottom: 0;
                left: 0;
                border-radius: 0;
            }
            
            .message {
                max-width: 90%;
            }
        }
    `;
    
    document.head.appendChild(style);
}