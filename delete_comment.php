<?php
require_once("config/config.php");
include_once("check_login.php");


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $commentId = $_POST['comment_id'];
    $requestPostId = $_POST['post_id'];
    $requestUserId = $_SESSION['user_id'];
    if (empty($commentId)) {
        print_r(json_encode(array('error'=> 'Invalid comment id for deletion')));
        exit;
    }
    $sql = "SELECT user_id,post_id FROM comments WHERE comment_id = ?";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("s", $param_commentId);
        $param_commentId = $commentId;
        if($stmt->execute()){
            $result = $stmt->get_result();
            if($result->num_rows>0){
                $row = $result->fetch_assoc();
                $commentUserId = $row['user_id'];
                $commentPostId = $row['post_id'];
                if($commentPostId==$requestPostId){
                    if($commentUserId == $requestUserId){
                        print_r(deleteComment($commentId, $mysqli));
                    } else {
                        print_r(checkAndDeleteCommentForPostOwner($commentPostId, $requestUserId, $commentId, $mysqli));
                    }
                } else {
                    print_r(json_encode(array('error'=> 'Invalid deletion request for the mentioned comment and post combination.'. $commentUserId .' requestpostid: '.$requestPostId. ' requestUserId '.$requestUserId)));
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

function checkAndDeleteCommentForPostOwner($commentPostId, $requestUserId, $commentId, $mysqli){
    $sql = "SELECT user_id FROM posts WHERE post_id = ?";
    if ($stmt2 = $mysqli->prepare($sql)) {
        $param_postId = $commentPostId;
        $stmt2->bind_param("s", $param_postId);
        if($stmt2->execute()){
            $result = $stmt2->get_result();
            if($result->num_rows==1){
                $row = $result->fetch_assoc();
                $postUserId = $row['user_id'];
                if($postUserId == $requestUserId){
                    print_r(deleteComment($commentId, $mysqli));
                }
            }
        }        
    }

}

function deleteComment($commentId, $mysqli){
    $sql = "DELETE FROM comments WHERE comment_id = ".$commentId;
    if($mysqli->query($sql) === TRUE){
        return json_encode(array("success"=> true));
    }
}