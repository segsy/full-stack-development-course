<?php
// admin/products_delete.php
require_once '../functions.php';
require_once '../db.php';
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) 
    session_start();
if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') { 
    echo json_encode(['success'=>false,'message'=>'auth']); 
    exit;
 }
$raw = json_decode(file_get_contents('php://input'), true);
$id = (int)($raw['id'] ?? 0);
$csrf = $raw['csrf_token'] ?? '';
if (!validate_csrf($csrf)) { echo json_encode(['success'=>false,'message'=>'csrf']); 
    exit; 
}
try{
  $pdo->prepare("DELETE FROM products WHERE id = :id")->execute([':id'=>$id]);
  echo json_encode(['success'=>true]);
}catch(Exception $e){ echo json_encode(['success'=>false,'message'=>$e->getMessage()]); }
