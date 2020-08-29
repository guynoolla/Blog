<?php
use App\Classes\Post;
use App\Classes\File;

require_once('../../../src/initialize.php');

// Check Author >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
if (!$session->isAuthor()) redirect_to(url_for('index.php'));
// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Check Author

$post = false;

if (is_post_request()) {

  $id = $_POST['post']['id'] ?? 0;

  $post = Post::findById($id);
  $image = new File($_FILES['image']);
  $post->fileInstance($image);
  $post->mergeAttributes($_POST['post']);

  $result = $post->save();

  if ($result === true) {
    $session->message("Post '" . $post->title . "' was updated!");
    redirect_to(url_for('/staff/posts/edit.php?id=' . $post->id));
  }

} else {
  // Post ID must be provided
  if (isset($_GET['id'])) {
    $post = App\Classes\Post::findById($_GET['id']);
    
    if ($post === false) {
      redirect_to(url_for('/staff/posts/index.php'));
    }
  }
}

$page_title = 'Edit Post';
include SHARED_PATH . '/staff_header.php';

?>
<div class="container-xl">
  <div class="page-admin">

    <div class="row">
      <div class="topbox col-12"></div>
    </div>

    <div class="row">
      <?php include SHARED_PATH . '/staff_sidebar.php' ?>
      
      <div class="main col-lg-9">
        <?php include('./_form.php') ?>
      </div>

    </div>
  </div>
</div>

<?php include SHARED_PATH . '/staff_footer.php'; ?>