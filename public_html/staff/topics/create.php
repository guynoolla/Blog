<?php
use App\Classes\Topic;

require_once('../../../src/initialize.php');

// Check Admin >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
if (!$session->isAdmin()) redirect_to(url_for('index.php'));
// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Check Admin

if(is_post_request()) {
  $topic = new Topic($_POST['topic']);
  $result = $topic->save();

  if($result === true) {
    $session->message("The New Topic '" . $topic->name . "' was added!");
    redirect_to(url_for('/staff/topics/index.php'));
  }

} else {
  $topic = new Topic;
}

?>
<!DOCTYPE html>
<html lang="en">

  <?php
    $page_title = 'Admin - New Topic';
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
        <h2 style="text-align: center;">Create Topic</h2>

        <?php include './_form.php'; ?>

      </div>
    </div>
    <!-- // Admin Content -->

  </div>


  <?php
    include SHARED_PATH . '/staff_footer.php'
  ?>
</body>

</html>