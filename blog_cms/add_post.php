<?php include 'db.php'; ?>

<form method="POST">
  <input type="text" name="title" placeholder="Post Title" required><br/><br/>
  <textarea name="content" placeholder="Post Content" required></textarea><br><br/><br/>
  <button type="submit" name="submit">Add Post</button>
</form>

<?php
if (isset($_POST['submit'])) {
  $title = $_POST['title'];
  $content = $_POST['content'];
  $sql = "INSERT INTO posts (title, content) VALUES ('$title', '$content')";
  mysqli_query($conn, $sql);
  echo "Post added successfully!";
}
?>
