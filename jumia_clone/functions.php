<?php


// Secure session setup (call early)
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => false, // set to true on HTTPS
    'httponly' => true,
    'samesite' => 'Lax'
]);


//  Now start the session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// regenerate session on major events
function hard_regenerate_session() {
    session_regenerate_id(true);
}

// CSRF token helpers
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

// Simple email sender — in production use proper SMTP (PHPMailer)
function send_email($to, $subject, $body, $from = 'noreply@localhost') {
    $headers  = "From: {$from}\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    // mail() may be disabled locally; configure SMTP for production
    return mail($to, $subject, $body, $headers);
}

// Helper to format price
function money($n){ return '₦'.number_format($n, 2); }
