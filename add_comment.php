<?php
require_once "config/config.php";

try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $user_id = $_SESSION["user_id"];
        $post_id = htmlspecialchars(trim($_POST["post_id"]));
        $comment_text = $_POST["comment_text"];

        $sql = "INSERT INTO comments (user_id, post_id, content, created_at) VALUES (?, ?, ?, NOW())";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("iis", $user_id, $post_id, $comment_text);
        $stmt->execute();
        $stmt->close();

        echo json_encode(array("status" => "success"));
        exit;
    }
} catch (exception $e) {
    echo json_encode(array("status" => "error", "error_message" => $e->getMessage()));
}
