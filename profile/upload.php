<?php
$conn = new mysqli("localhost", "root", "", "travelworldweb");
$user_id = 1;

if (isset($_FILES['avatar'])) {
  $target = "uploads/" . basename($_FILES['avatar']['name']);
  if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target)) {
    $sql = "UPDATE users SET avatar='$target' WHERE user_id=$user_id";
    $conn->query($sql);
  }
}
header("Location: profile.php");
?>
