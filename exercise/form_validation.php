<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Contact Form with Validation</title>
  <style>
    body{
      font-family:Arial,sans-serif;
      background-color:#f4f4f4;
      padding: 40px;

    }
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
  
</head>
<body>
  <?php
// Initialize variables
$name = $email = $message = "";
$nameErr = $emailErr = $messageErr = "";
$successMsg = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  // Validate Name
  if (empty($_POST["name"])) {
    $nameErr = "Name is required";
  } else {
    $name = htmlspecialchars(trim($_POST["name"]));
    if (!preg_match("/^[a-zA-Z-' ]*$/", $name)) {
      $nameErr = "Only letters and spaces allowed";
    }
  }

  // Validate Email
  if (empty($_POST["email"])) {
    $emailErr = "Email is required";
  } else {
    $email = htmlspecialchars(trim($_POST["email"]));
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $emailErr = "Invalid email format";
    }
  }

  // Validate Message
  if (empty($_POST["message"])) {
    $messageErr = "Message cannot be empty";
  } else {
    $message = htmlspecialchars(trim($_POST["message"]));
  }

  // If no errors, show success message
  if (empty($nameErr) && empty($emailErr) && empty($messageErr)) {
    $successMsg = "✅ Message sent successfully! Thank you, $name.";
    // You can also store the message in a database or send an email here
  }
}
?>

  <h2 style="text-align:center;">Contact Us</h2>

  <form action="" method="POST">
    <label for="name">Full Name:</label>
    <input type="text" name="name" id="name" placeholder="Enter your name">

    <label for="email">Email:</label>
    <input type="email" name="email" id="email" placeholder="Enter your email">

    <label for="message">Message:</label>
    <textarea name="message" id="message" rows="4" placeholder="Type your message here..."></textarea>

    <button type="submit" name="submit">Send Message</button>
  </form>

</body>
</html>