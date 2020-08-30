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

$page_title = 'Admin - New Topic';
include SHARED_PATH . '/staff_header.php'

?>
<div class="row">
  <aside class="sidebar col-lg-3">
    <?php include SHARED_PATH . '/staff_sidebar.php' ?>
  </aside>
  
  <div class="main col-lg-9">
    <?php include('./_form.php') ?>
  </div>

</div>

<?php include SHARED_PATH . '/staff_footer.php'; ?>