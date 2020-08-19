<?php
use App\Classes\Post;
USE App\Classes\File;

require_once('../../../src/initialize.php');

// Check Author >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
if (!$session->isAuthor()) redirect_to(url_for('index.php'));
// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Check Author

if (is_post_request()) {

  $post = new Post($_POST['post']);
  $image = new File($_FILES['image']);
  $post->fileInstance($image);

  $result = $post->save();

  if ($result === true) {
    if ($post->published) {
      $session->message("Thank you for your post! Now it is awaiting admin proof.");
    } else {
      $session->message("Your Post '" . $post->title . "' saved as draft!");
    }
    redirect_to(url_for('/staff/posts/index.php'));
  }

} else {
  $post = new Post;
}

?>
<!DOCTYPE html>
<html lang="en">

  <?php
    $page_title = 'New Post';
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

        <?php include('./_form.php') ?>

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