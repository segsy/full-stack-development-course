<?php
$conn = mysqli_connect("localhost", "root", "", "blog_db");
$update = "UPDATE posts SET title='Updated Post Title' WHERE id=1";

if (mysqli_query($conn, $update)) {
  echo "Post updated successfully!";
}
?>
