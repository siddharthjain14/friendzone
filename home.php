<?php

require_once "config/config.php";


function getAllPosts($mysqli)
{
    $posts = array();
    $sql = "SELECT posts.*, users.username,users.profile_image_url FROM posts JOIN users ON posts.user_id = users.user_id ORDER BY posts.created_at DESC";
    $result = $mysqli->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }
    }
    return $posts;
}

$posts = getAllPosts($mysqli);
?>
<div class="d-sm-block d-md-none secondary">
    <div class="my-3 rounded-2 p-2 text-center ">
        <img src="resources/brand-main.png" alt="BootstrapBrain Logo" width="300" height="70">
    </div>
    <hr>
</div>
<div class="box container my-3 rounded-2 p-2 bg-white">
    <form id="post-form" method="post" enctype="multipart/form-data">
        <div class="d-flex align-items-start my-2">
            <img src="<?php echo htmlspecialchars($_SESSION["user_profile_image"]) ?>" alt="" class="rounded-circle mx-2 mt-1" width="32" height="32">
            <div class="background-post-input rounded w-100 mx-1">
                <textarea id="post-content" type="text" class="form-control overflow-auto" name="post_content" placeholder="What's on your mind?" rows="1"></textarea>
                <div class="invalid-feedback bg-white" id="invalid-content-feedback">
                    Please type in the post content or choose an image.
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-between mx-2 my-2">
            <div class="d-flex align-items-center">
                <label for="post-image" class="d-flex" role="button">
                    <svg color="gray" xmlns="http://www.w3.org/2000/svg" width="20" height="20">
                        <use xlink:href="#post-camera"></use>
                    </svg>
                </label>
                <input type="file" id="post-image" class="post-image-input" name="post_image" accept=".gif, .png, .jpg, .jpeg" />
                <p class="d-flex mb-0 mx-2 fw-lighter image-title" id="post-image-title"></p>
            </div>
            <button id="submit-post" type="submit" class="bg-white post-button">Post</button>
        </div>
    </form>
</div>
<script>
    var form = document.getElementById('post-form');
    form.addEventListener('submit', function(event) {
        event.preventDefault();
        if (!form.checkValidity()) {
            event.stopPropagation();
        }
        const content = document.getElementById('post-content');
        const image = document.getElementById('post-image');
        if (content.value.trim() == "" && image.value.trim() == "") {
            content.classList.add("is-invalid");
            return;
        } else {
            content.classList.remove("is-invalid");
        }

        if (this.checkValidity()) {
            const obj = new FormData;
            obj.append('post_content', content.value);
            obj.append('post_image', image.files[0]);
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    console.log(this.response);
                    var data = JSON.parse(this.response);
                    if (data.status && data.status == "success") {
                        var lastTab =  localStorage.getItem('lastTab') || "home";
                        loadContentSideMenu(lastTab);
                    } else {
                        var errorMessage = data.error_message;
                        if (errorMessage.trim() !== "") {
                            document.getElementById("invalid-content-feedback").innerHTML = errorMessage;
                            content.classList.add('is-invalid');
                        } else {
                            content.classList.remove('is-invalid');
                        }
                    }
                }
            };
            xhttp.open("POST", "add_post.php", false);
            xhttp.send(obj);
        }
    }, false);

    var image = document.getElementById('post-image');
    image.addEventListener('change', function(event) {
        var file = event.target.files[0];
        if (file) {
            document.getElementById('post-image-title').innerText = file.name;
        }
    });
    image.addEventListener('cancel', function(event) {
        document.getElementById('post-image-title').innerText = " ";
    });
</script>
<?php include "posts.php" ?>