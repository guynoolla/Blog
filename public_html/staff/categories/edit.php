<?php
use App\Classes\Category;

require_once('../../../src/initialize.php');

// Check Admin >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
if (!$session->isAdmin()) redirect_to(url_for('index.php'));
// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Check Admin

$category = false;

if (is_get_request()) {

  // Post ID must be provided
  if(isset($_GET['id'])) {
    $category = App\Classes\Category::findById($_GET['id']);
  }
  if($category === false) {
    redirect_to(url_for('/staff/categories/index.php'));
  }

} elseif (is_post_request()) {

  $id = $_POST['category']['id'] ?? 0;
  $category = Category::findById($id);
  $category->mergeAttributes($_POST['category']);

  if($category->save()) {
    $session->message("The Category '" . $category->name . "' was updated!");
    redirect_to(url_for('/staff/categories/index.php'));
  }

}

$page_title = 'Category edit';
include SHARED_PATH . '/staff_header.php'

?>
<div class="row">
  <aside class="sidebar col col-lg-3 pt-1">
    <?php include SHARED_PATH . '/staff_sidebar.php' ?>
  </aside>
  
  <div class="main col col-lg-9 bg-light-lk bg-light-lk--md">
    <div class="main-content bg-white">
      <?php include('./_form.php') ?>
    </div>
  </div>

</div>

<?php include SHARED_PATH . '/staff_footer.php'; ?>