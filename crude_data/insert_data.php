<?php
$conn = mysqli_connect("localhost", "root", "", "blog_db");

$title = "My First Blog Post";
$content = "This is the content of my first blog post.";

$sql = "INSERT INTO posts (title, content) VALUES ('$title', '$content')";
if (mysqli_query($conn, $sql)) {
  echo "Post added successfully!";
} else {
  echo "Error: " . mysqli_error($conn);
}
?>
