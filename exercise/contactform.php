<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Contact Form</title>
  
</head>
<body>
  <h2>Contact Us</h2>
  <form action="process_form.php" method="POST">
    <label for="name">Full Name:</label><br>
    <input type="text" name="name" id="name" required><br><br>
    <label for="email">Email</label><br>
    <input type="email" name="email" id="email"  required><br><br>
    <label for ="message">Message:</label><br>
     <textarea name="message" id="message" required></textarea><br><br>

     <button type="submit">Send</button>

  </form>
  
</body>
</html>