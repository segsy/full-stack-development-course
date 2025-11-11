use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php'; // composer autoload

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
