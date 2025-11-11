<!DOCTYPE html>
<html>
<head>
   <title>if else statement</title>
</head>
<body>
   <?php
  $score = 75;
  
   if($score >= 90){
    echo "Grade:A";
   }  elseif ($score >= 70){
    echo "Grade: B";
   } elseif ($score >= 50){
    echo "Grade C";
   } else {
    echo "fail";
   }
 
   ?>
</body>
</html>