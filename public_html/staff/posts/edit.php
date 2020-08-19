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

?>
<!DOCTYPE html>
<html lang="en">

  <?php
    $page_title = 'Edit Post';
    include SHARED_PATH . '/staff_header.php';
  ?>

  <div class="admin-wrapper clearfix">

    <?php include SHARED_PATH . '/staff_sidebar.php' ?>

    <!-- Admin Content -->
    <div class="admin-content clearfix">

      <div class="button-group">
        <?php echo page_back_link('Back', 'btn btn-sm') ?>
      </div>

      <div class="">
        <h2 style="text-align: center;"><?php echo $page_title ?></h2>

        <?php echo display_session_message('msg success') ?>

        <?php include './_form.php' ?>

      </div>
    </div>
    <!-- // Admin Content -->

  </div>


  <?php
    $ckeditor = true;
    include SHARED_PATH . '/staff_footer.php';
  ?>
</body>

</html>