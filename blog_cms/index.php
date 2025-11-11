<?php include 'db.php'; ?>
<h2>My Blog Posts</h2>
<a href="add_post.php">+ Add New Post</a><hr>

<?php
$result = mysqli_query($conn, "SELECT * FROM posts ORDER BY id DESC");
while ($row = mysqli_fetch_assoc($result)) {
  echo "<h3>{$row['title']}</h3>";
  echo "<p>{$row['content']}</p>";
  echo "<a href='edit_post.php?id={$row['id']}'>Edit</a> | ";
  echo "<a href='delete_post.php?id={$row['id']}'>Delete</a>";
  echo "<hr>";
}
?>
