<?php
require_once 'functions.php';
require_once 'db.php';

$errors = []; 
$name=''; 
$email=''; 
$success='';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf($_POST['csrf_token'] ?? '')) { 
        $errors[] = 'Invalid CSRF token.'; }
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if ($name === '') 
        $errors[] = "Name required";
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) 
        $errors[] = "Valid email required";
    if (strlen($password) < 8) 
        $errors[] = "Password must be at least 8 characters.";
    if ($password !== $password_confirm) 
        $errors[] = "Passwords do not match.";

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        if ($stmt->fetch()) $errors[] = "Email already registered.";
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $ins = $pdo->prepare("INSERT INTO users (name,email,password) VALUES (:name,:email,:pw)");
        $ins->execute([':name'=>$name,':email'=>$email,':pw'=>$hash]);
        $userId = $pdo->lastInsertId();

        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 day'));
        $pdo->prepare("INSERT INTO email_verifications (user_id, token, expires_at) VALUES (:uid,:tok,:exp)")
            ->execute([':uid'=>$userId,':tok'=>$token,':exp'=>$expires]);

        $verifyUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI'])."/verify.php?token={$token}";
        $body = "<p>Hi ".htmlspecialchars($name).",</p><p>Click to verify your email: <a href='{$verifyUrl}'>Verify Email</a></p>";

        if (!send_email($email,"Verify your account",$body)) {
            // dev fallback (mail may not work locally)
            $success = "Account created. (Dev mode) Verify via this link: {$verifyUrl}";
        } else {
            $success = "Account created. Check your email to verify your address.";
        }
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Register</title>
<link rel="stylesheet" href="assets/style.css"></head><body>
<div class="container"><div class="auth-wrap">
  <h2>Create Account</h2>
  <?php if ($errors): ?><div class="error-list"><ul><?php foreach($errors as $e) echo "<li>".htmlspecialchars($e)."</li>"; ?></ul></div><?php endif; ?>
  <?php if ($success): ?><p class="success"><?=htmlspecialchars($success)?></p><?php endif; ?>

  <form method="post" action="">
    <?= csrf_field() ?>
    <label>Name</label><input name="name" value="<?=htmlspecialchars($name)?>" required>
    <label>Email</label><input name="email" type="email" value="<?=htmlspecialchars($email)?>" required>
    <label>Password</label><input name="password" type="password" required>
    <label>Confirm</label><input name="password_confirm" type="password" required>
    <button class="btn primary" type="submit">Register</button>
  </form>
  <p>Have an account? <a href="login.php">Login</a></p>
</div></div>
</body></html>
