document.addEventListener("DOMContentLoaded", function() {
  var lastTab = localStorage.getItem('lastTab') || 'home';
  loadContent(lastTab, document.getElementById(lastTab + "-nav"));
});

function loadContent(page, tab) {
  $("#home-container").load(page + ".php");
  updateTabSelection(tab);
}

function loadContentSideMenu(page){
  loadContent(page, document.getElementById(page + "-nav"));
}

function updateTabSelection(selectedTab) {
  var tabs = document.querySelectorAll('.nav-link');
  tabs.forEach(function(tab) {
      tab.classList.remove('active');
      tab.classList.add('link-body-emphasis');
  });
  selectedTab.classList.add('active');
  selectedTab.classList.remove('link-body-emphasis');

  localStorage.setItem('lastTab', selectedTab.id.slice(0,-4));
}

$(document).ready(function () {
  $(".dropdown-item").hover(
    function () {
      $(this).addClass("active");
    },
    function () {
      $(this).removeClass("active");
    }
  );

  $("#deletePostModal").on("show.bs.modal", function (e) {
    var data = $(e.relatedTarget).data("postid");
    $(e.currentTarget).find("#delete-modal-post-id").attr("value", data);
  });

  $("#deleteConfirmation").click(function () {
    var data = $("#delete-modal-post-id").attr("value");
    deletePost(data);
  });

  $("#deleteCommentModal").on("show.bs.modal", function (e) {
    var postId = $(e.relatedTarget).data("postid");
    console.log('postid', postId);
    $(e.currentTarget)
      .find("#delete-comment-modal-post-id")
      .attr("value", postId);
    var commentId = $(e.relatedTarget).data("commentid");
    console.log(commentId);
    $(e.currentTarget)
      .find("#delete-comment-modal-comment-id")
      .attr("value", commentId);
  });

  $("#deleteCommentConfirmation").click(function () {
    var postId = $("#delete-comment-modal-post-id").attr("value");
    var commentId = $("#delete-comment-modal-comment-id").attr("value");
    deleteComment(commentId, postId);
  });

});

function successToast(message) {
  $(document).ready(function () {
    $("#toastContainer").append(
      '<div id="successToast" class="toast text-bg-success fade show" data-bs-delay="1500" role="alert"><div class="toast-body"><div class="d-flex gap-3"><span><i class="fa-solid fa-circle-check fa-lg"></i></span><div class="d-flex flex-grow-1 align-items-center"><span class="fw-semibold">' +
        message +
        "</span></div></div></div></div>"
    );
  });
  setTimeout(function () {
    $("#successToast").remove();
  }, 1500);
}

function failureToast(message) {
  $(document).ready(function () {
    $("#toastContainer").append(
      '<div id="failureToast" class="toast text-bg-danger fade show" data-bs-delay="1500" role="alert"><div class="toast-body"><div class="d-flex gap-4"><span><i class="fa-solid fa-circle-exclamation fa-lg"></i></span><div class="d-flex flex-grow-1 align-items-center"><span class="fw-semibold">' +
        message +
        "</span></div></div></div></div>"
    );
  });
  setTimeout(function () {
    $("#failureToast").remove();
  }, 1500);
}

function deletePost(postId) {
  $.ajax({
    url: "delete_post.php",
    type: "post",
    data: { post_id: postId },
    success: function (result) {
      if (JSON.parse(result).success !== "") {
        document.getElementById(postId).remove();
      }
      successToast("Post deleted successfully!");
    },
    error: function (result) {
      failureToast(JSON.parse(result).error);
    },
  });
}

function deleteComment(commentId, postId) {
  $.ajax({
    url: "delete_comment.php",
    type: "post",
    data: { comment_id: commentId, post_id: postId },
    success: function (result) {
      if (JSON.parse(result).success !== "") {
        window.location.reload();
      }
      successToast("Comment deleted successfully!");
    },
    error: function (result) {
      failureToast(JSON.parse(result).error);
    },
  });
}
