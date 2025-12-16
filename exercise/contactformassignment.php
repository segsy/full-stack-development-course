<?php
// Define variables and set empty values
$name = $email = $subject = $message = "";
$nameErr = $emailErr = $subjectErr = $messageErr = "";
$successMsg = "";

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // NAME VALIDATION
    if (empty($_POST["name"])) {
        $nameErr = "Name is required";
    } else {
        $name = htmlspecialchars($_POST["name"]);
    }

    // EMAIL VALIDATION
    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = htmlspecialchars($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
        }
    }

    // SUBJECT VALIDATION
    if (empty($_POST["subject"])) {
        $subjectErr = "Subject is required";
    } else {
        $subject = htmlspecialchars($_POST["subject"]);
    }

    // MESSAGE VALIDATION
    if (empty($_POST["message"])) {
        $messageErr = "Message is required";
    } else {
        $message = htmlspecialchars($_POST["message"]);
    }

    // IF NO ERRORS — SUCCESS MESSAGE
    if (empty($nameErr) && empty($emailErr) && empty($subjectErr) && empty($messageErr)) {
        $successMsg = "Message sent successfully!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Contact Form</title>
    <style>
        .error { color: red; font-size: 14px; }
        .success { color: green; font-size: 16px; font-weight: bold; }
        form { width: 350px; padding: 20px; background: #f4f4f4; border-radius: 8px; }
        input, textarea { width: 100%; padding: 8px; margin-top: 6px; margin-bottom: 12px; }
        button { padding: 10px; background: blue; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>

<h2>Contact Form</h2>

<?php if ($successMsg): ?>
    <p class="success"><?= $successMsg ?></p>
<?php endif; ?>

<form method="post" action="">
    <label>Name:</label>
    <input type="text" name="name" value="<?= $name ?>">
    <span class="error"><?= $nameErr ?></span>

    <label>Email:</label>
    <input type="text" name="email" value="<?= $email ?>">
    <span class="error"><?= $emailErr ?></span>

    <label>Subject:</label>
    <input type="text" name="subject" value="<?= $subject ?>">
    <span class="error"><?= $subjectErr ?></span>

    <label>Message:</label>
    <textarea name="message"><?= $message ?></textarea>
    <span class="error"><?= $messageErr ?></span>

    <button type="submit">Send Message</button>
</form>

</body>
</html>
