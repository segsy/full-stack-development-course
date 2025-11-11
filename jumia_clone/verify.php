<?php
require_once 'functions.php';
require_once 'db.php';

$token = $_GET['token'] ?? '';
$message = "Invalid or expired token.";

if ($token) {
    $stmt = $pdo->prepare("SELECT ev.id, ev.user_id, ev.expires_at, ev.used, u.is_verified FROM email_verifications ev JOIN users u ON u.id = ev.user_id WHERE ev.token = :tok LIMIT 1");
    $stmt->execute([':tok'=>$token]);
    $row = $stmt->fetch();

    if ($row) {
        if ($row['used']) { 
            $message = "Token already used."; }
        elseif (new DateTime() > new DateTime($row['expires_at'])) { 
            $message = "Token expired."; }
        else {
            // mark user verified and token used
            $pdo->prepare("UPDATE users SET is_verified = 1 WHERE id = :id")->execute([':id'=>$row['user_id']]);
            $pdo->prepare("UPDATE email_verifications SET used = 1 WHERE id = :id")->execute([':id'=>$row['id']]);
            $message = "Email verified successfully! You can now login.";
        }
    }
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Verify</title></head><body>
<div class="container"><div style="max-width:600px;margin:40px auto;padding:20px;background:#fff;border-radius:8px">
  <h3>Email Verification</h3>
  <p><?=htmlspecialchars($message)?></p>
  <p><a href="login.php">Go to Login</a></p>
</div></div>
</body></html>
