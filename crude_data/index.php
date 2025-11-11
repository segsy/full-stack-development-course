<?php
$conn = mysqli_connect("localhost", "root", "", "blog_db");
$result = mysqli_query($conn, "SELECT * FROM posts");

while ($row = mysqli_fetch_assoc($result)) {
  echo "<h2>{$row['title']}</h2>";
  echo "<p>{$row['content']}</p>";
  echo "<small>Posted on: {$row['created_at']}</small><hr>";
}
?>
