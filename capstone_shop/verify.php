<?php
require_once 'functions.php';
require_once 'db.php';
$token = $_GET['token'] ?? '';
$message = "Invalid or expired token.";

if ($token) {
    $stmt = $pdo->prepare("SELECT ev.id, ev.user_id, ev.expires_at, ev.used FROM email_verifications ev WHERE ev.token = :tok LIMIT 1");
    $stmt->execute([':tok'=>$token]);
    $row = $stmt->fetch();
    if ($row) {
        if ($row['used']) {
            $message = "Token already used.";
        } elseif (new DateTime() > new DateTime($row['expires_at'])) {
            $message = "Token expired.";
        } else {
            $pdo->prepare("UPDATE users SET is_verified = 1 WHERE id = :id")->execute([':id'=>$row['user_id']]);
            $pdo->prepare("UPDATE email_verifications SET used = 1 WHERE id = :id")->execute([':id'=>$row['id']]);
            $message = "Email verified! You can now login.";
        }
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Verify</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container"><div style="max-width:600px;margin:40px auto;background:#fff;padding:20px;border-radius:8px">
  <h3>Email Verification</h3>
  <p><?=htmlspecialchars($message)?></p>
  <p><a href="login.php">Login</a></p>
</div></div>
</body></html>
