<?php
require_once 'functions.php';
require_once 'db.php';
$token = $_GET['token'] ?? '';
$errors = []; 
$msg = '';

if ($token) {
    $stmt = $pdo->prepare("SELECT pr.id, pr.user_id, pr.expires_at, pr.used, u.email FROM password_resets pr JOIN users u ON u.id = pr.user_id WHERE pr.token = :tok LIMIT 1");
    $stmt->execute([':tok'=>$token]);
    $row = $stmt->fetch();
    if (!$row) { 
    $errors[] = 'Invalid token.'; 
    $token=''; }
    elseif ($row['used']) { 
      $errors[] = 'Token already used.'; 
      $token=''; }
    elseif (new DateTime() > new DateTime($row['expires_at'])) { 
      $errors[] = 'Token expired.'; $token=''; }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $token) {
    if (!validate_csrf($_POST['csrf_token'] ?? '')) 
      $errors[] = 'Invalid CSRF';
    $pw = $_POST['password'] ?? ''; 
    $pw2 = $_POST['password_confirm'] ?? '';
    if (strlen($pw) < 8) $errors[] = 'Password must be at least 8 chars';
    if ($pw !== $pw2) $errors[] = 'Passwords do not match';
    if (empty($errors)) {
        $hash = password_hash($pw, PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE users SET password = :pw WHERE id = :uid")->execute([':pw'=>$hash,':uid'=>$row['user_id']]);
        $pdo->prepare("UPDATE password_resets SET used = 1 WHERE id = :id")->execute([':id'=>$row['id']]);
        $msg = 'Password reset successful. You can now login.';
        $token = '';
    }
}
?>
<!doctype html>
<html>
  <head><meta charset="utf-8">
  <title>Set new password</title><link rel="stylesheet" href="assets/style.css"></head><body>
<div class="container">
  <h2>Set New Password</h2>
  <?php if($errors): ?><div class="error-list"><ul><?php foreach($errors as $e) echo "<li>".htmlspecialchars($e)."</li>"; ?></ul></div><?php endif; ?>
  <?php if($msg): ?><p class="success"><?=htmlspecialchars($msg)?></p><?php endif; ?>
  <?php if($token): ?>
    <form method="post">
      <?= csrf_field() ?>
      <label>New password</label><input name="password" type="password" required>
      <label>Confirm password</label><input name="password_confirm" type="password" required>
      <button class="btn primary" type="submit">Set password</button>
    </form>
  <?php endif; ?>
</div></body></html>
