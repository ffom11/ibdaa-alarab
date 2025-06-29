<?php
// logout.php: تسجيل الخروج ومسح الجلسة
session_start();
session_unset();
session_destroy();
header('Location: login.html');
exit;
