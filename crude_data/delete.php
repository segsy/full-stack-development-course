<?php
$conn = mysqli_connect("localhost", "root", "", "blog_db");
$delete = "DELETE FROM posts WHERE id=1";

if (mysqli_query($conn, $delete)) {
  echo "Post deleted successfully!";
}
?>
