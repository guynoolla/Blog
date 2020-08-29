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