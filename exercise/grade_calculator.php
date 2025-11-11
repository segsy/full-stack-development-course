<!DOCTYPE html>
<html>
<head>
   <title>Grade calculator</title>
</head>
<body>
   <?php
   function calculateGrade($scores){
    foreach ($scores as $subject => $score){
        if($score >= 90){
            $grade = "A";
        } elseif ($score >= 75){
            $grade = "B";

        } elseif ($score  >= 50){
            $grade = "C";
        } else{
            $grade = "F";
        }
        echo "$subject:Score, Grade = $grade<br>";
    }

   }
   $studentScores = [
    "Math" => 85,
    "English" => 92,
    "Science" => 70,
    "History" => 48


   ];

   calculateGrade($studentScores);
    
   ?>
</body>
</html>