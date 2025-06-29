<?php
// معالجة إضافة تقييم جديد لمعلن
session_start();
require_once '../includes/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    die('الدخول مسموح للعملاء فقط.');
}
$adv_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($adv_id <= 0 || !isset($_POST['rating'], $_POST['comment'], $_POST['name'])) die('بيانات ناقصة.');
$client_id = $_SESSION['user_id'];
$name = trim($_POST['name']);
$rating = (int)$_POST['rating'];
$comment = trim($_POST['comment']);
if ($rating < 1 || $rating > 5 || !$comment || !$name) die('بيانات غير صحيحة.');
// تحقق أن العميل لم يقيّم هذا المعلن من قبل
$stmt = $pdo->prepare("SELECT id FROM ratings WHERE advertiser_id=? AND client_id=? LIMIT 1");
$stmt->execute([$adv_id, $client_id]);
if ($stmt->fetch()) die('لقد قمت بتقييم هذا المعلن من قبل.');
$stmt = $pdo->prepare("INSERT INTO ratings (advertiser_id, client_id, name, rating, comment, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
$stmt->execute([$adv_id, $client_id, $name, $rating, $comment]);
header('Location: advertiser_profile.php?id=' . $adv_id);
exit;
