<!DOCTYPE html>
<html>
<head>
   <title> Logical Operators</title>
</head>
<body>
   <?php
   $age = 20;
   $hasLicense = true;

   $hasLicense = true;
   if($age >= 18 && $hasLicense){
    echo "You can drive";

   } else {
    echo " You are not eligible ";
   }
   
   ?>
</body>
</html>