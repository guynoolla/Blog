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

  if($topic->save()) {
    $session->message("The Topic '" . $topic->name . "' was updated!");
    redirect_to(url_for('/staff/topics/index.php'));
  }

}

$page_title = 'Admin - Edit Topic';
include SHARED_PATH . '/staff_header.php'

?>
<div class="row">
  <aside class="sidebar col-lg-3">
    <?php include SHARED_PATH . '/staff_sidebar.php' ?>
  </aside>
  
  <div class="main col-lg-9">
    <div class="main-content">
      <?php echo page_back_button() ?>
      <?php include('./_form.php') ?>
    </div>
  </div>

</div>

<?php include SHARED_PATH . '/staff_footer.php'; ?>