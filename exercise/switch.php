<!DOCTYPE html>
<html>
<head>
   <title>Switch statement</title>
</head>
<body>
   <?php
   $day = "Tuesday";

   switch($day){
    case "Monday";
    echo " Start of the week!";
    break;
    case "Tuesday";
    echo " Keep going!";
    break;
    case "Friday";
    echo "Weekend is close!";
    break;
    default:
    echo "Just another day";
   }
   
   ?>
</body>
</html>