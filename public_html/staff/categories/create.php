<?php
use App\Classes\Category;

require_once('../../../src/initialize.php');

// Check Admin >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
if (!$session->isAdmin()) redirect_to(url_for('index.php'));
// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Check Admin

if(is_post_request()) {
  $category = new Category($_POST['category']);
  $result = $category->save();

  if($result === true) {
    $session->message("The New Category '" . $category->name . "' was added!");
    redirect_to(url_for('/staff/categories/index.php'));
  }

} else {
  $category = new Category;
}

$page_title = 'Admin - New Category';
include SHARED_PATH . '/staff_header.php'

?>
<div class="row">
  <aside class="sidebar col col-lg-3 pt-1">
    <?php include SHARED_PATH . '/staff_sidebar.php' ?>
  </aside>
  
  <div class="main col col-lg-9">
    <div class="main-content"><?php include('./_form.php') ?></div>
  </div>

</div>

<?php include SHARED_PATH . '/staff_footer.php'; ?>