<?php
// api/cart.php
require_once dirname(__DIR__).'/functions.php';
require_once dirname(__DIR__).'/db.php';
header('Content-Type: application/json');

$raw = json_decode(file_get_contents('php://input'), true);
$action = $raw['action'] ?? $_POST['action'] ?? null;
$userId = $_SESSION['user_id'] ?? null;

function respond($arr){ echo json_encode($arr); exit; }

// helper to count items (session or db)
function cart_count($pdo, $userId) {
    if ($userId) {
        $st = $pdo->prepare("SELECT SUM(qty) as c FROM cart_items WHERE user_id=:uid");
        $st->execute([':uid'=>$userId]); $r = $st->fetch(); return (int)$r['c'];
    } else {
        if (empty($_SESSION['cart'])) return 0;
        return array_sum(array_column($_SESSION['cart'],'qty'));
    }
}

// add
if ($action === 'add') {
    $id = (int)($raw['id'] ?? 0);
    $title = htmlspecialchars($raw['title'] ?? 'Item');
    $price = floatval($raw['price'] ?? 0);

    if (!$id) respond(['success'=>false,'message'=>'Invalid product id']);

    if ($userId) {
        $stmt = $pdo->prepare("INSERT INTO cart_items (user_id, product_id, qty) VALUES (:uid,:pid,1) ON DUPLICATE KEY UPDATE qty = qty + 1");
        $stmt->execute([':uid'=>$userId,':pid'=>$id]);
        $count = cart_count($pdo, $userId);
        respond(['success'=>true,'count'=>$count]);
    } else {
        if (!isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id] = ['id'=>$id,'title'=>$title,'price'=>$price,'qty'=>1];
        } else {
            $_SESSION['cart'][$id]['qty'] += 1;
        }
        $count = cart_count($pdo, null);
        respond(['success'=>true,'count'=>$count]);
    }
}

// remove
if ($action === 'remove') {
    $id = (int)($raw['id'] ?? 0);
    if ($userId) {
        $pdo->prepare("DELETE FROM cart_items WHERE user_id=:uid AND product_id=:pid")->execute([':uid'=>$userId,':pid'=>$id]);
        $count = cart_count($pdo, $userId);
        respond(['success'=>true,'count'=>$count]);
    } else {
        unset($_SESSION['cart'][$id]);
        $count = cart_count($pdo, null);
        respond(['success'=>true,'count'=>$count]);
    }
}

// list
if ($action === 'list') {
    $items = []; $total = 0;
    if ($userId) {
        $st = $pdo->prepare("SELECT ci.product_id as id, p.title, p.price, ci.qty FROM cart_items ci JOIN products p ON p.id = ci.product_id WHERE ci.user_id = :uid");
        $st->execute([':uid'=>$userId]);
        $items = $st->fetchAll();
        foreach ($items as $it) $total += $it['price'] * $it['qty'];
    } else {
        $items = array_values($_SESSION['cart'] ?? []);
        foreach ($items as $it) $total += $it['price'] * $it['qty'];
    }
    respond(['success'=>true,'items'=>$items,'total'=>$total]);
}

// clear
if ($action === 'clear') {
    if ($userId) {
        $pdo->prepare("DELETE FROM cart_items WHERE user_id = :uid")->execute([':uid'=>$userId]);
        respond(['success'=>true,'count'=>0]);
    } else {
        $_SESSION['cart'] = [];
        respond(['success'=>true,'count'=>0]);
    }
}

// count
if ($action === 'count') {
    respond(['success'=>true,'count'=>cart_count($pdo,$userId)]);
}

respond(['success'=>false,'message'=>'Unknown action']);
