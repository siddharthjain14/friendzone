<?php foreach ($posts as $post) : ?>
    <div class="box container-fluid my-3 overflow-hidden rounded-2 p-2 bg-white" id="<?php echo $post['post_id'] ?>">
        <div class="d-flex p-2 justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <img src="<?php echo htmlspecialchars($post['profile_image_url']); ?>" alt="" class="rounded-circle me-2 d-flex" width="32" height="32">
                <div>
                    <h6 class="mb-1" style="color:black"><?php echo htmlspecialchars($post['username']); ?></h6>
                    <p class="mb-0 profile_timestamp"><?php echo date('M d', strtotime($post['created_at'])); ?></p>
                </div>
            </div>
            <?php
            if ($post['user_id'] == $_SESSION['user_id']) {
                echo
                '<div class="dropdown">
                    <a href="#" class="d-flex align-middle link-body-emphasis text-decoration-none" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-ellipsis-vertical dropdown-toggle"></i>
                    </a>
                    <ul class="dropdown-menu shadow">
                    <li><a class="dropdown-item fs-6 delete_post" data-bs-toggle="modal" data-postid="' . $post['post_id'] . '" 
                                data-bs-target="#deletePostModal" href="javascript:void(0)" >Delete Post</a></li>
                    </ul>
                </div>';
            }
            ?>

        </div>

        <div class="d-flex px-2 py-1 align-items-center">
            <p><?php echo htmlspecialchars($post['content']); ?></p>
        </div>

        <?php if (!empty($post['image_url'])) : ?>
            <div class="cover">
                <img src="<?php echo htmlspecialchars($post['image_url']); ?>" alt="" class="w-100">
            </div>
        <?php endif; ?>
        <hr>
        <form class="comment-form" method="post">
            <div class="d-flex align-items-start my-2">
                <img src="<?php echo htmlspecialchars($_SESSION["user_profile_image"]) ?>" alt="" class="rounded-circle mx-2 mt-1" width="32" height="32">
                <div class="background-post-input rounded w-100 mx-1">
                    <input type="text" class="form-control overflow-auto" name="comment_text" placeholder="Write a comment...">
                    <div class="invalid-feedback">
                        Comment content cannot be empty.
                    </div>
                </div>
            </div>
        </form>
        <div class="d-flex flex-row align-items-center mx-2 mt-3">
            <span class="text-dark mr-2 all-comments"> All Comments </span>
            <i class="fa-solid fa-arrow-turn-down fa-xs"></i>
        </div>
        <?php
        $post_id = $post['post_id'];
        $comments_query = "SELECT comments.*,users.username,users.profile_image_url FROM comments JOIN users ON comments.user_id = users.user_id WHERE post_id = $post_id ORDER BY created_at";
        $comments_result = mysqli_query($mysqli, $comments_query);

        if (mysqli_num_rows($comments_result) > 0) {
            if ($post['user_id'] == $_SESSION['user_id']) {
            }
            echo "<div class=\"comment-card text-dark\">";
            while ($comment = mysqli_fetch_assoc($comments_result)) {
                echo
                '<div class="d-flex px-4 py-2" id="' . $comment['comment_id'] . '"><img src="' . htmlspecialchars($comment['profile_image_url']) . '" width="24" height="24" class="rounded-circle mr-2 mt-1">
                            <div class="w-100">
                                <div class="d-flex justify-content-between ">
                                    <div class="d-flex flex-row">
                                        <p class="comment-text fw-normal mb-0 mx-1"><span class="comment-user fw-semibold mb-0">' . $comment['username'] . '</span>: ' . htmlspecialchars($comment['content']) . '</p>
                                    </div>';
                if ($post['user_id'] == $_SESSION['user_id'] || $comment['user_id'] == $_SESSION['user_id']) {
                    echo '
                                <div class="dropdown">
                                    <a href="#" class="d-flex align-middle link-body-emphasis text-decoration-none " data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-caret-down-fill"></i>
                                    </a>
                                    <ul class="dropdown-menu shadow">
                                    <li><a class="dropdown-item fs-6 delete_comment" data-bs-toggle="modal" data-postid="' . $post['post_id'] . '" data-commentid="' . $comment['comment_id'] . '" 
                                                data-bs-target="#deleteCommentModal" href="javascript:void(0)" >Delete Comment</a></li>
                                    </ul>
                                </div>
                            ';
                }
                echo '            
                                </div>
                            </div>
                        </div>
                        ';
            }
            echo "</div>";
        }
        ?>
    </div>
<?php endforeach; ?>

<script>
    Array.from(document.getElementsByClassName('comment-form')).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            if (!form.checkValidity()) {
                event.stopPropagation();
            }
            var content = form.getElementsByTagName('input')[0];
            console.log(content);
            if (content.value.trim() == "") {
                content.classList.add("is-invalid");
            } else {
                content.classList.remove("is-invalid");
            }
            if (this.checkValidity()) {
                const obj = new FormData;
                console.log(form.parentElement.id);
                obj.append('comment_text', content.value);
                obj.append('post_id', form.parentElement.id);
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        console.log(this.response);
                        var data = JSON.parse(this.response);
                        if (data.status && data.status == "success") {
                            var lastTab = localStorage.getItem('lastTab') || "home";
                            loadContentSideMenu(lastTab);
                        } else {
                            var errorMessage = data.error_message;
                            if (errorMessage.trim() !== "") {
                                failureToast(errorMessage);
                            }
                        }
                    }
                };
                xhttp.open("POST", "add_comment.php", false);
                xhttp.send(obj);
            }
        }, false);
    })
</script>