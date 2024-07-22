<?php
require_once "config/config.php";

$post_content = $post_image = "";
$post_content_err = $post_image_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $post_content = htmlspecialchars(trim($_POST["post_content"]));

    if (!empty($_FILES["post_image"]["name"])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["post_image"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["post_image"]["tmp_name"]);

        if ($check !== false) {
            $uploadOk = 1;
        } else {
            $post_image_err = "File is not an image.";
            $uploadOk = 0;
        }

        if ($_FILES["post_image"]["size"] > 10485760) {
            $post_image_err = "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            $post_image_err = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["post_image"]["tmp_name"], $target_file)) {
                $post_image = $target_file;
            } else {
                $post_image_err = "Sorry, there was an error uploading your file.";
            }
        }
    }

    if (empty($post_image_err)) {
        $sql = "INSERT INTO posts (user_id, content, image_url) VALUES (?, ?, ?)";

        if ($stmt = $mysqli->prepare($sql)) {
            $param_user_id = $_SESSION["user_id"];
            $param_content = $post_content;
            $param_image_url = $post_image;
            $stmt->bind_param("iss", $param_user_id, $param_content, $param_image_url);
            
            if ($stmt->execute()) {
                echo json_encode(array("status"=> "success"));
            } else {
                echo json_encode(array("status"=>"error","error_message"=> "Sorry something went wrong, please try again later"));
            }
            $stmt->close();
        }
    } else {
        echo json_encode(array("status"=>"error","error_message"=> $post_image_err));
    }
    $mysqli->close();
}
