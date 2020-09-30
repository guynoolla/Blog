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

  if (isset($_POST['delete'])) {
    if ($post->delete()) {
      $session->message("The post ' . $post->title . ' was deleted.");
      redirect_to(url_for('staff/posts/index.php'));
    }
  }

  if (!isset($_POST['post']['published'])) {
    $_POST['post']['published'] = '0';
  } 
  $post->mergeAttributes($_POST['post']);

  if ($post->save()) {
    $session->message("Post was updated, you can view it by clicking on its title!");
    
    if ($session->isAdmin()) {
      if ($post->user_id == $session->getUserId()) {
        redirect_to(url_for('staff/posts/index.php'));
      } else {
        redirect_to(url_for('staff/posts/drafts.php'));
      }
    } else {
      redirect_to(url_for('staff/posts/index.php'));
    }
  }

} else {
  // Post ID must be provided
  if (isset($_GET['id'])) {
    $post = App\Classes\Post::findById($_GET['id']);

    // Check Access >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    if (!$session->isAdmin() && $session->getUserId() != $post->user_id)
      redirect_to(url_for('index.php'));
    // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Check Access
    
    if ($post === false) {
      redirect_to(url_for('staff/posts/index.php'));
    }
  }
}

$page_title = 'Edit Post';
include SHARED_PATH . '/staff_header.php';

?>
<div class="row">
  <aside class="sidebar col-lg-3">
    <?php include SHARED_PATH . '/staff_sidebar.php' ?>
  </aside>
  
  <div class="main col-lg-9">
    <div class="main-content adminContentJS">
      <?php include('./_form.php') ?>
    </div>
  </div>
</div>

<?php include SHARED_PATH . '/staff_footer.php'; ?>