<?php
require_once("config/config.php");
include_once("check_login.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $postId = $_POST['post_id'];
    $requestUserId = $_SESSION['user_id'];
    if (empty($postId)) {
        print_r(json_encode(array('error'=> 'Invalid post id for deletion')));
        exit;
    }
    $sql = "SELECT user_id FROM posts WHERE post_id = ?";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("s", $param_postId);
        $param_postId = $postId;
        if($stmt->execute()){
            $stmt->store_result();
            if($stmt->num_rows==1){
                $stmt->bind_result($postUserId);
                if($stmt->fetch() && $postUserId == $requestUserId){
                    print_r(deletePost($postId, $mysqli));
                } else {
                    print_r(json_encode(array('error'=> 'Unauthorized deletion request. Access denied')));
                    exit;
                }
            }
        } 
        $stmt->close();
        $mysqli->close();
    } else {
        print_r(json_encode(array('error'=> 'Database connection failure')));
        exit;
    }

}

function deletePost($postId, $mysqli){
    $sql = "DELETE FROM posts WHERE post_id = ".$postId;
    if($mysqli->query($sql) === TRUE){
        return json_encode(array("success"=> true));
    }
}