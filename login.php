<?php
require_once "config/config.php";

$username = $password = "";
$username_err = $password_err = $login_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (empty(trim($_POST["username"]))) {
            $username_err = "Please enter a username.";
        } else {
            $username = trim($_POST["username"]);
            $username = htmlspecialchars($username);
        }

        if (empty(trim($_POST["password"]))) {
            $password_err = "Please enter a password.";
        } else {
            $password = trim($_POST["password"]);
            $password = htmlspecialchars($password);
        }

        if (empty($username_err) && empty($password_err)) {
            $sql = "SELECT user_id, username, password, profile_image_url FROM users WHERE username = ?";

            if ($stmt = $mysqli->prepare($sql)) {
                $stmt->bind_param("s", $param_username);

                $param_username = $username;

                if ($stmt->execute()) {
                    $stmt->store_result();

                    if ($stmt->num_rows == 1) {
                        $stmt->bind_result($user_id, $username, $hashed_password, $profile_image);
                        if ($stmt->fetch()) {
                            if (password_verify($password, $hashed_password)) {
                                if(!isset($_SESSION)){    
                                    session_set_cookie_params(0);
                                    session_start();
                                }
                                $_SESSION["loggedin"] = true;
                                $_SESSION["user_id"] = $user_id;
                                $_SESSION["username"] = $username;
                                $_SESSION["user_profile_image"] = $profile_image;
                                $_SESSION["tab"] = "home";
                                echo json_encode(array("status"=>"success"));
                            } else {
                                echo json_encode(array("status"=>"error","error_message"=>"Invalid username and password combination."));
                            }
                        }
                    } else {
                        echo json_encode(array("status"=>"error","error_message"=>"Provided username not found."));
                    }
                } else {
                    echo json_encode(array("status"=>"error","error_message"=>"Something went wrong, please try again later"));
                }
                $stmt->close();
            }
        }

    $mysqli->close();
}
