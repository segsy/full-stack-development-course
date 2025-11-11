<?php
require_once 'functions.php';
require_once 'db.php';
$errors = []; $msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf($_POST['csrf_token'] ?? '')) $errors[] = 'Invalid CSRF';
    $email = trim($_POST['email'] ?? '');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email required';

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id,name FROM users WHERE email=:e LIMIT 1");
        $stmt->execute([':e'=>$email]);
        $user = $stmt->fetch();
        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            $pdo->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (:uid,:tok,:exp)")
                ->execute([':uid'=>$user['id'],':tok'=>$token,':exp'=>$expires]);

            $resetUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI'])."/password_reset.php?token={$token}";
            $body = "<p>Hi {$user['name']},</p><p>Reset: <a href='{$resetUrl}'>Reset Password</a></p>";

            if (!send_email($email, 'Password reset', $body)) {
                $msg = "Password reset link (dev): {$resetUrl}";
            } else {
                $msg = "If the email exists, a reset link has been sent.";
            }
        } else {
            $msg = "If the email exists, a reset link has been sent.";
        }
    }
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Password reset</title><link rel="stylesheet" href="assets/style.css"></head><body>
<div class="container">
  <h2>Reset Password</h2>
  <?php if($errors): ?><div class="error-list"><ul><?php foreach($errors as $e) echo "<li>".htmlspecialchars($e)."</li>"; ?></ul></div><?php endif; ?>
  <?php if($msg): ?><p><?=htmlspecialchars($msg)?></p><?php endif; ?>

  <form method="post">
    <?= csrf_field() ?>
    <input name="email" type="email" placeholder="Your email" required>
    <button class="btn" type="submit">Send reset link</button>
  </form>
</div></body></html>
