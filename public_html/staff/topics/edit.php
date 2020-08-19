<?php
use App\Classes\Topic;

require_once('../../../src/initialize.php');

// Check Admin >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
if (!$session->isAdmin()) redirect_to(url_for('index.php'));
// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Check Admin

$topic = false;

if (is_get_request()) {

  // Post ID must be provided
  if(isset($_GET['id'])) {
    $topic = App\Classes\Topic::findById($_GET['id']);
  }
  if($topic === false) {
    redirect_to(url_for('/staff/topics/index.php'));
  }

} elseif (is_post_request()) {

  $id = $_POST['topic']['id'] ?? 0;
  $topic = Topic::findById($id);
  $topic->mergeAttributes($_POST['topic']);
  $result = $topic->save();

  if($result === true) {
    $session->message("The Topic '" . $topic->name . "' was updated!");
    redirect_to(url_for('/staff/topics/index.php'));
  }

}

?>
<!DOCTYPE html>
<html lang="en">

  <?php
    $page_title = 'Admin - Edit Topic';
    include SHARED_PATH . '/staff_header.php'
  ?>

  <div class="admin-wrapper clearfix">

    <?php include SHARED_PATH . '/staff_sidebar.php' ?>

    <!-- Admin Content -->
    <div class="admin-content clearfix">
      <div class="button-group">
        <a href="<?php echo url_for('/staff/topics/create.php') ?>" class="btn btn-sm">Add Topic</a>
        <a href="<?php echo url_for('/staff/topics/index.php') ?>" class="btn btn-sm">Manage Topics</a>
      </div>
      <div class="">
        <h2 style="text-align: center;">Update Topic</h2>

        <?php include('./_form.php') ?>

      </div>
    </div>
    <!-- // Admin Content -->

  </div>


  <?php
    include SHARED_PATH . 'staff_footer.php'
  ?>
</body>

</html>