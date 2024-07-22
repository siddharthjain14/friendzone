<?php
require_once "config/config.php";

$username = $password = $confirm_password = $email = $user_image = "";
$username_err = $password_err = $confirm_password_err = $email_err = $user_image_err = "";
$registration_success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (empty(trim($_POST["username"]))) {
    $username_err = "Please enter a username.";
  } else if(checkUserNameExists($_POST["username"], $mysqli)){
    $username_err = "Username already exists.";
  } else {
    $username = trim($_POST["username"]);
    $username = htmlspecialchars($username);
  }

  if (empty(trim($_POST["password"]))) {
    $password_err = "Please enter a password.";
  } elseif (strlen(trim($_POST["password"])) < 8) {
    $password_err = "Password must match the guidelines.";
  } else {
    $password = trim($_POST["password"]);
    $password = htmlspecialchars($password);
  }

  if (!empty($_FILES["userimage"]["name"])) {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["userimage"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $check = getimagesize($_FILES["userimage"]["tmp_name"]);
    if ($check !== false) {
      $uploadOk = 1;
    } else {
      $user_image_err = "File is not an image. Please upload a valid image.";
      $uploadOk = 0;
    }

    if ($_FILES["userimage"]["size"] > 10485760) {
      $user_image_err = "Sorry, your file is too large.";
      $uploadOk = 0;
    }

    if (
      $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
      && $imageFileType != "gif"
    ) {
      $user_image_err = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
      $uploadOk = 0;
    }

    if ($uploadOk == 1) {
      if (move_uploaded_file($_FILES["userimage"]["tmp_name"], $target_file)) {
        $user_image = $target_file;
      } else {
        $user_image_err = "Sorry, there was an error uploading your file.";
      }
    }
  }

  if (empty(trim($_POST["email"]))) {
    $email_err = "Please enter your email.";
  } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
    $email_err = "Please provide a valid email.";
  } else {
    $email = trim($_POST["email"]);
    $email = htmlspecialchars($email);
  }

  if (empty($username_err) && empty($password_err) && empty($email_err) && empty($user_image_err)) {
    $sql = "INSERT INTO users (username, password, email, profile_image_url) VALUES (?, ?, ?, ?)";

    if ($stmt = $mysqli->prepare($sql)) {
      $stmt->bind_param("ssss", $param_username, $param_password, $param_email, $param_profile_url);

      $param_username = $username;
      $param_password = password_hash($password, PASSWORD_DEFAULT);
      $param_email = $email;
      $param_profile_url = $user_image;

      if ($stmt->execute()) {
        $registration_success = true;
        echo json_encode(array("status" => "success"));
      } else {
        echo json_encode(array("status" => "error"));
      }
      $stmt->close();
    }
  } else {
    echo json_encode(array("status" => "error", "error_message" => array("email" => $email_err, "password" => $password_err, "userimage" => $user_image_err, "username" => $username_err)));
  }
  $mysqli->close();
}


function checkUserNameExists($username, $mysqli) {
  $sql = "SELECT * FROM users WHERE username = ?";

  if ($stmt = $mysqli->prepare($sql)) {
    $param_username = $username;
    $stmt->bind_param("s", $param_username);
    if ($stmt->execute()) {
      $result = $stmt->get_result();
      if ($result->num_rows > 0) {
        return true;
      }
    } 
    return false;
  }
}