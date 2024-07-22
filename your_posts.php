<?php
require_once "config/config.php";
require_once "check_login.php";

function getUserPosts($mysqli, $userId)
{
  $posts = array();
  $sql = "SELECT posts.*, users.username,users.profile_image_url FROM posts JOIN users ON posts.user_id = users.user_id WHERE users.user_id= " . $userId . " ORDER BY posts.created_at DESC";
  $result = $mysqli->query($sql);
  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $posts[] = $row;
    }
  }
  return $posts;
}
// Get the posts
$posts = getUserPosts($mysqli, $_SESSION["user_id"]);
?>
<div class="d-sm-block d-md-none secondary">
    <div class="my-3 rounded-2 p-2 text-center ">
        <img src="resources/brand-main.png" alt="BootstrapBrain Logo" width="300" height="70">
    </div>
    <hr>
</div>
<?php include "posts.php" ;  