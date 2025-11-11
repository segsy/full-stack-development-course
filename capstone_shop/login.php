<?php
require_once 'functions.php';
require_once 'db.php';
$errors=[];
$email='';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf($_POST['csrf_token'] ?? '')) $errors[] = 'Invalid CSRF token.';
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') 
        $errors[] = "Email and password required.";

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id,name,password,is_verified FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email'=>$email]);
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
                        $qty = max(1, (int)$it['qty']);
                        $up = $pdo->prepare("INSERT INTO cart_items (user_id, product_id, qty) VALUES (:uid,:pid,:qty)
                            ON DUPLICATE KEY UPDATE qty = qty + :qty2");
                        $up->execute([':uid'=>$user['id'],':pid'=>$prodId,':qty'=>$qty,':qty2'=>$qty]);
                    }
                    unset($_SESSION['cart']);
                }

                 // ✅ Role-based redirection
        if ($user['role'] === 'admin') {
            header('Location: admin/dashboard.php');
        } else {

                $next = !empty($_GET['next']) ? $_GET['next'] : 'index.php';

                header('Location: ' . $next);
        }
                exit;
            }
        } else {
            $errors[] = "Invalid credentials.";
        }
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Login</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container"><div class="auth-wrap">
  <h2>Login</h2>
  <?php if ($errors): ?><div class="error-list"><ul><?php foreach($errors as $e) echo "<li>".htmlspecialchars($e)."</li>"; ?></ul></div><?php endif; ?>
  <form method="post" action="?<?= !empty($_GET['next']) ? 'next=' . urlencode($_GET['next']) : '' ?>">
    <?= csrf_field() ?>
    <label>Email</label>
    <input type="email" name="email" type="email" value="<?=htmlspecialchars($email)?>" required>
    <label>Password</label><input type="password" name="password" type="password" required>
    <button class="btn primary" type="submit">Login</button>
  </form>
  <p><a href="password_reset_request.php">Forgot password?</a></p>
  <p>Don't have account? <a href="register.php">Register</a></p>
</div></div>
</body></html>
