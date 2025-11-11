<?php
require_once 'functions.php';
require_once 'db.php';
$token = $_GET['token'] ?? '';
$errors=[]; $msg='';

if ($token) {
    $stmt = $pdo->prepare("SELECT pr.id, pr.user_id, pr.expires_at, pr.used, u.email FROM password_resets pr JOIN users u ON u.id = pr.user_id WHERE pr.token = :tok LIMIT 1");
    $stmt->execute([':tok'=>$token]);
    $row = $stmt->fetch();
    if (!$row) { $errors[] = "Invalid token."; $token=''; }
    elseif ($row['used']) { $errors[] = "Token already used."; $token=''; }
    elseif (new DateTime() > new DateTime($row['expires_at'])) { $errors[] = "Token expired."; $token=''; }
}

if ($_SERVER['REQUEST_METHOD']==='POST' && $token) {
    if (!validate_csrf($_POST['csrf_token'] ?? '')) $errors[]='Invalid CSRF';
    $pw = $_POST['password'] ?? ''; $pw2 = $_POST['password_confirm'] ?? '';
    if (strlen($pw) < 8) $errors[] = "Password at least 8 chars";
    if ($pw !== $pw2) $errors[] = "Passwords do not match";
    if (empty($errors)) {
        $hash = password_hash($pw, PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE users SET password = :pw WHERE id = :uid")->execute([':pw'=>$hash,':uid'=>$row['user_id']]);
        $pdo->prepare("UPDATE password_resets SET used = 1 WHERE id = :id")->execute([':id'=>$row['id']]);
        $msg = "Password reset successful. You can now <a href='login.php'>login</a>.";
        $token = '';
    }
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Reset Password</title></head><body>
<div class="container"><div style="max-width:600px;margin:40px auto;background:#fff;padding:20px;border-radius:8px">
  <h3>Reset Password</h3>
  <?php if(!empty($errors)): echo '<div class="error-list"><ul>'; foreach($errors as $e) echo '<li>'.htmlspecialchars($e).'</li>'; echo '</ul></div>'; endif; ?>
  <?php if($msg): ?><p class="success"><?=$msg?></p><?php endif; ?>
  <?php if($token): ?>
    <form method="post">
      <?= csrf_field() ?>
      <label>New password</label><input type="password" name="password" required>
      <label>Confirm password</label><input type="password" name="password_confirm" required>
      <button type="submit" class="btn primary">Set new password</button>
    </form>
  <?php endif; ?>
</div></div>
</body></html>
