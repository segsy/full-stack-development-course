<?php
require_once 'functions.php';
require_once 'db.php';
$msg=''; $errors=[];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    if (!validate_csrf($_POST['csrf_token'] ?? '')) $errors[] = "Invalid CSRF token.";
    $email = trim($_POST['email'] ?? '');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Provide a valid email.";
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id,name FROM users WHERE email=:e LIMIT 1");
        $stmt->execute([':e'=>$email]);
        $user = $stmt->fetch();
        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            $pdo->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (:uid,:tok,:exp)")->execute([':uid'=>$user['id'],':tok'=>$token,':exp'=>$expires]);
            $resetUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI'])."/password_reset.php?token={$token}";
            $body = "<p>Hi {$user['name']},</p><p>Reset your password: <a href='{$resetUrl}'>Reset Password</a></p>";
            if (!send_email($email,'Password reset',$body)) $msg = "Password reset link: {$resetUrl}";
            else $msg = "If the email exists, a reset link was sent.";
        } else {
            $msg = "If the email exists, a reset link was sent.";
        }
    }
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Password Reset</title></head><body>
<div class="container"><div style="max-width:600px;margin:40px auto;background:#fff;padding:20px;border-radius:8px">
  <h3>Reset Password</h3>
  <?php if(!empty($errors)): echo '<div class="error-list"><ul>'; foreach($errors as $e) echo '<li>'.htmlspecialchars($e).'</li>'; echo '</ul></div>'; endif; ?>
  <?php if(!empty($msg)): ?><p><?=htmlspecialchars($msg)?></p><?php endif; ?>
  <form method="post"><input type="email" name="email" placeholder="Your email" required><?= csrf_field() ?><button type="submit">Send reset link</button></form>
</div></div>
</body></html>
