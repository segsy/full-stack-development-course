<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//require __DIR__ . '/vendor/autoload.php'; // composer autoload


// Session hardening
ini_set('session.use_strict_mode', 1);
$cookieParams = session_get_cookie_params();
session_set_cookie_params([
    'lifetime' => 0,
    'path' => $cookieParams['path'],
    'domain' => $cookieParams['domain'],
    'secure' => false, // set to true under HTTPS
    'httponly' => true,
    'samesite' => 'Lax'
]);

// functions.php
if (session_status() === PHP_SESSION_NONE) session_start();

function hard_regenerate_session() {
    session_regenerate_id(true);
}

/* CSRF helpers */
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
function csrf_field() {
    $t = htmlspecialchars(csrf_token());
    return "<input type='hidden' name='csrf_token' value='{$t}'>";
}
function validate_csrf($token) {
    if (empty($token) || empty($_SESSION['csrf_token'])) return false;
    return hash_equals($_SESSION['csrf_token'], $token);
}


function send_smtp_email($to, $subject, $body, $from = 'noreply@yourdomain.com') {
    $mail = new PHPMailer(true);
    try {
        // SMTP config - replace with your provider settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';    // smtp server
        $mail->SMTPAuth = true;
        $mail->Username = 'your-smtp-email@gmail.com';
        $mail->Password = 'your-app-password'; // for Gmail create App Password or use OAuth
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom($from, 'Mini Shop');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = strip_tags($body);

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mail error: " . $mail->ErrorInfo);
        return false;
    }
}


/* Email helper (simple): replace with PHPMailer for SMTP */
function send_email($to, $subject, $body, $from = 'noreply@localhost') {
    $headers  = "From: {$from}\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    return @mail($to, $subject, $body, $headers);
}

function money($n){ return '₦'.number_format($n,2); }
