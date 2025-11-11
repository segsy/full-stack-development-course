<?php
if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST["email"]));
    $message = htmlspecialchars(trim($_POST["message"]));

    echo "Thanks, $name!We've received your message.";
    

}
?>