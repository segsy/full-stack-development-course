<?php
require_once '../functions.php';
require_once '../db.php';
if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') { 
    //header('Location: ../login.php'); exit; 
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (isset($_GET['del']) && is_numeric($_GET['del'])) {
    $pdo->prepare("DELETE FROM products WHERE id = :id")->execute([':id'=>$_GET['del']]);
    header('Location: products_manage.php'); exit;
}

$title=''; $price=''; $description=''; $img=''; $stock=0;
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id LIMIT 1");
    $stmt->execute([':id'=>$id]);
    $row = $stmt->fetch();
    if ($row) { $title=$row['title']; $price=$row['price']; $description=$row['description']; $img=$row['img']; $stock=$row['stock']; }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf($_POST['csrf_token'] ?? '')) { $error='Invalid CSRF'; }
    $title = trim($_POST['title'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $img = trim($_POST['img'] ?? '');
    $stock = intval($_POST['stock'] ?? 0);

    if (empty($title)) $error = 'Title required';
    if (!isset($error)) {
        if ($id) {
            $pdo->prepare("UPDATE products SET title=:title, price=:price, description=:desc, img=:img, stock=:stock WHERE id=:id")
                ->execute([':title'=>$title,':price'=>$price,':desc'=>$description,':img'=>$img,':stock'=>$stock,':id'=>$id]);
            $msg='Updated';
        } else {
            $pdo->prepare("INSERT INTO products (title,price,description,img,stock) VALUES (:title,:price,:desc,:img,:stock)")
                ->execute([':title'=>$title,':price'=>$price,':desc'=>$description,':img'=>$img,':stock'=>$stock]);
            $msg='Created';
        }
        header('Location: products_manage.php'); exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head><meta charset="utf-8">
<title>Edit Product</title>
<style>
    form {
      background:#fff;
      padding:20px;
      max-width:400px;
      margin:auto;
      border-radius: 10px;
      box-sahdow: 0 0 10px rgba(0,0,0,0.1);    
    }
    input,textarea {
      width:100%;
      padding: 10px;
      margin-bottom: 10px;
      border:1px solid #cccc;
      border-radius: 5px;
    }
    .error{color:red;}
    .success{color:green;}
</style>
<link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<div class="container">
  <h2><?= $id ? 'Edit' : 'Add' ?> Product</h2>
  <?php if(!empty($error)): ?><p class="error"><?=htmlspecialchars($error)?></p><?php endif; ?>
  <form method="post">
    <?= csrf_field() ?>
    <label>Title</label>
    <input type=text name="title" value="<?=htmlspecialchars($title)?>" required>
    <label>Price</label>
    <input  type="" name="price" value="<?=htmlspecialchars($price)?>" required>
    <label>Image URL</label>
    <input type="file" name="img" value="<?=htmlspecialchars($img)?>">
    <label>Stock</label>
    <input type="text" name="stock" value="<?=htmlspecialchars($stock)?>" type="number">
    <label>Description</label>
    <textarea name="description"><?=htmlspecialchars($description)?></textarea>
    <button class="btn primary" type="submit"><?= $id ? 'Update' : 'Create' ?></button>
  </form>
</div></body></html>
