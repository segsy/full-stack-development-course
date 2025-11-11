<?php
// admin/update_order_status.php
require_once '../functions.php';
require_once '../db.php';
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    echo json_encode(['success'=>false,'message'=>'auth_required']); exit;
}

$raw = json_decode(file_get_contents('php://input'), true);
$orderId = (int)($raw['order_id'] ?? 0);
$status  = trim($raw['status'] ?? '');
$csrf    = $raw['csrf_token'] ?? '';

if (!validate_csrf($csrf)) {
    echo json_encode(['success'=>false,'message'=>'invalid_csrf']); exit;
}
$allowed = ['pending','paid','processing','shipped','completed','cancelled','failed'];
if (!in_array($status, $allowed)) {
    echo json_encode(['success'=>false,'message'=>'invalid_status']); exit;
}

try {
    $stmt = $pdo->prepare("UPDATE orders SET payment_status = :s WHERE id = :id");
    $stmt->execute([':s'=>$status, ':id'=>$orderId]);
    echo json_encode(['success'=>true]);
} catch (Exception $e) {
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
