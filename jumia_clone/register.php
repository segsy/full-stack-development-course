<?php
require_once 'functions.php';
require_once 'db.php';

$errors=[];
$name=''; 
$email='';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    if (!validate_csrf($_POST['csrf_token'] ?? '')) { $errors[] = "Invalid CSRF token."; }
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if ($name==='') $errors[]="Name required";
    if ($email==='' || !filter_var($email,FILTER_VALIDATE_EMAIL)) $errors[]="Valid email required";
    if (strlen($password) < 8) $errors[]="Password at least 8 chars";
    if ($password !== $password_confirm) $errors[]="Passwords do not match";

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email=:email LIMIT 1");
        $stmt->execute([':email'=>$email]);
        if ($stmt->fetch()) $errors[]="Email already registered";
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $ins = $pdo->prepare("INSERT INTO users (name,email,password) VALUES (:name,:email,:pw)");
        $ins->execute([':name'=>$name,':email'=>$email,':pw'=>$hash]);
        $userId = $pdo->lastInsertId();

        // create verification token
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 day'));
        $pdo->prepare("INSERT INTO email_verifications (user_id, token, expires_at) VALUES (:uid,:tok,:exp)")
            ->execute([':uid'=>$userId,':tok'=>$token,':exp'=>$expires]);

        // send verification email (adjust domain)
        $verifyUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']
            . dirname($_SERVER['REQUEST_URI']) . "/verify.php?token={$token}";
        $body = "<p>Hi " . htmlspecialchars($name) . ",</p>
                 <p>Click to verify your email: <a href='{$verifyUrl}'>Verify Email</a></p>
                 <p>If you didn't create an account, ignore this message.</p>";

        if (!send_email($email, "Verify your account", $body)) {
            // In many local devs, mail() may fail; show token link for testing
            $errors[] = "Verification email could not be sent. Use the link shown below to verify (dev mode).";
            $devVerifyLink = $verifyUrl;
        }

        // show message and do not auto-login (recommended until verified)
        $success = "Account created! Please check your email to verify your address.";
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Register</title>
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
  <h2 style="text-align:center;">Register</h2>
  <?php if(!empty($errors)): ?><div class="error-list"><ul><?php foreach($errors as $e) echo "<li>".htmlspecialchars($e)."</li>"; ?></ul></div><?php endif; ?>
  <?php if(!empty($success)): ?><p class="success"><?=htmlspecialchars($success)?></p><?php endif; ?>
  <?php if(!empty($devVerifyLink)): ?><p>Dev verify link: <a href="<?=htmlspecialchars($devVerifyLink)?>"><?=htmlspecialchars($devVerifyLink)?></a></p><?php endif; ?>

  <form method="post" action="">
    <?= csrf_field() ?>
    <label>Name</label>
    <input type="text" name="name" value="<?=htmlspecialchars($name)?>" required><br/><br/>
    <label>Email</label>
    <input type="email" name="email" type="email" value="<?=htmlspecialchars($email)?>" required><br/><br/>
    <label>Password</label>
    <input type="password" name="password" type="password" required><br/><br/>
    <label>Confirm Password</label>
    <input type="password" name="password_confirm" type="password" required><br/><br/>
    <button class="btn primary" type="submit">Create account</button>
  </form>
  <p>Already have an account? <a href="login.php">Login</a></p>
</div></div>
</body></html>
