<?php
require_once 'functions.php';
require_once 'db.php';

$errors=[]; $email='';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    if (!validate_csrf($_POST['csrf_token'] ?? '')) $errors[] = "Invalid CSRF token.";
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email==='' || $password==='') $errors[] = "Email & password required";

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id,name,password,is_verified FROM users WHERE email=:e LIMIT 1");
        $stmt->execute([':e'=>$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            if (!$user['is_verified']) {
                $errors[] = "Please verify your email before login.";
            } else {
                hard_regenerate_session();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];

                // merge session cart into DB cart
                if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
                    foreach ($_SESSION['cart'] as $prodId => $it) {
                        $qty = (int)$it['qty'];
                        if ($qty < 1) $qty = 1;
                        // insert or update cart_items
                        $up = $pdo->prepare("INSERT INTO cart_items (user_id, product_id, qty) VALUES (:uid,:pid,:qty)
                            ON DUPLICATE KEY UPDATE qty = qty + :qty2");
                        $up->execute([':uid'=>$user['id'],':pid'=>$prodId,':qty'=>$qty,':qty2'=>$qty]);
                    }
                    // clear session cart after merge
                    unset($_SESSION['cart']);
                }

                // redirect if next param set
                $next = !empty($_GET['next']) ? $_GET['next'] : 'index.php';
                header('Location: ' . $next);
                exit;
            }
        } else {
            $errors[] = "Invalid credentials or user does not exist.";
        }
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Login</title>
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
<link rel="stylesheet" href="assets/style.css"></head><body>
<div class="container"><div class="auth-wrap">
  <h2>Login</h2>
  <?php if(!empty($errors)): ?><div class="error-list"><ul><?php foreach($errors as $e) echo "<li>".htmlspecialchars($e)."</li>"; ?></ul></div><?php endif; ?>
  <form method="post" action="?<?= !empty($_GET['next']) ? 'next=' . urlencode($_GET['next']) : '' ?>">
    <?= csrf_field() ?>
    <label>Email</label><input name="email" type="email" value="<?=htmlspecialchars($email)?>" required><br/><br/>
    <label>Password</label><input name="password" type="password" required><br/><br/>
    <button class="btn primary" type="submit">Login</button>
  </form>
  <p><a href="password_reset_request.php">Forgot password?</a></p>
  <p>Don't have an account? <a href="register.php">Register</a></p>
</div></div>
</body></html>
