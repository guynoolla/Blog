<?php

use App\Classes\Category;
use App\Classes\User;
use App\Classes\Post;
use App\Classes\File;

require_once('../../src/initialize.php');

// Check Admin >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
if (!$session->isAdmin()) redirect_to(url_for('/index.php'));
// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Check Admin

$forbidden = false;

if (is_get_request()) { // GET request

  $table = $_GET['table'] ?? "";
  $id = $_GET['id'] ?? 0;

  if ($table && $id) {
    if ($table == 'users') {
      $page_title = 'Delete User';
      $object = User::findById($id);
      $warning = <<<EOT
        <p>Are you sure you want to delete the user <strong class="font-weight-bold">$object->username</strong>?<br>
        You can't delete the user, which has posts unless you delete those posts!</p></p>
EOT;
    } else if ($table == 'categories') {
      $page_title = 'Delete Category';
      $object = Category::findById($id);
      $warning = <<<EOT
        <p>Are you sure you want to delete the category <strong class="font-weight-bold">$object->name</strong>?<br>
        If the category has post(s) you can't delete it unless you delete those post(s)!</p>
EOT;
    } else if ($table == 'posts') {
      $page_title = 'Delete Post';
      $object = Post::findById($id);
      $warning = <<<EOT
        <p>Are you sure you want to delete the post <strong class="font-weight-bold">$object->title</strong>?<br>
        This post will be permanently deleted!</p>
EOT;
    }

    if (!$object) redirect_to(url_for('staff/index.php'));

  } else if ($session->store_of('warning')) {
    $table = $session->store_of('table', false);
    $warning = $session->store_of('warning', false);
    $forbidden = true;
  
  } else {
    redirect_to(url_for('staff/index.php'));
  }

} else if (is_post_request()) { // POST request

  $table = $_POST['table'] ?? "";
  $cancel = $_POST['cancel'] ?? "";
  $delete = $_POST['delete'] ?? "";
  $id = $cancel ? (int) $cancel : (int) $delete;

  if ($table && $id) {

    if ($table == 'users') { // users table
      $page_title = "Users";
      $user = User::findById($id);
      
      if ($cancel) {
        $session->message("The user '" . $user->username . "' deletion canceled.");
        redirect_to(url_for('staff/users/index.php'));
      
      } else if ($delete) {
        if ($user->delete()) {
          $session->message("The user '" . $user->username . "' was deleted.");
          redirect_to(url_for('staff/users/index.php'));
        } else {
          $posts_count = Post::countAll(['user_id' => $id]);
          if ($posts_count > 0) {
            $dep_word = $posts_count > 1 ? 'posts' : 'post'; 
            $session->store([
              'table' => $table,
              'warning' => "<strong>{$user->username}</strong> could not be deleted because this user has {$posts_count} {$dep_word}."
            ]);
            redirect_to(url_for('staff/delete.php'));
          }          
        }
      }

    } else if ($table == 'categories') { // categories table
      $page_title = 'Categories';
      $category = Category::findById($id);
      
      if ($cancel) {
        $session->message("The category '" . $category->name . "' deletion canceled.");
        redirect_to(url_for('staff/categories/index.php'));

      } else if ($delete) {
        if ($category->delete()) {
          $session->message("The category '" . $category->name . "' was deleted.");
          redirect_to(url_for('staff/categories/index.php'));
        } else {
          $posts_count = Post::countAll(['category_id' => $id]);
          if ($posts_count > 0) {
            $deps_arr = $posts_count > 1 ? ['are', 'posts'] : ['is', 'post']; 
            $session->store([
              'table' => $table,
              'warning' => "This category can not be deleted, because there {$deps_arr[0]} {$posts_count} {$deps_arr[1]} under it."
            ]);
            redirect_to(url_for('staff/delete.php'));
          }
        }
      }

    } else if ($table == 'posts') { // posts table
      $page_title = 'Posts';
      $post = Post::findById($id);
      $post->fileInstance(new File);

      if ($cancel) {
        $session->message("The post '" . $post->title . "' deletion canceled.");
        redirect_to(url_for('staff/posts/edit.php?id=' . $id));
      
      } else if ($delete) {
        if ($post->delete()) {
          $session->message("The post '" . $post->title . "' was deleted.");
          redirect_to(url_for('staff/posts/index.php'));
        }
      }
    }

  }

} // <-- POST request

include SHARED_PATH . '/staff_header.php';

?>
<div class="row">
  <aside class="sidebar col col-lg-3">
    <?php include SHARED_PATH . '/staff_sidebar.php' ?>
  </aside>

  <main class="main col col-lg-9">
    <div class="main-content adminContentJS">
      
      <h1 class="dashboard-headline">
        <?php echo $page_title ?>

        <div class="back-btn-pos"><?php
          if ($table == 'users') $back_url = 'staff/users/index.php';
          elseif ($table == 'categories') $back_url = 'staff/categories/index.php';
          elseif ($table == 'posts') $back_url = 'staff/posts/index.php';
          ?><a class="btn btn-outline-secondary btn-md rounded-0"
              href="<?php echo url_for($back_url) ?>">Back</a>
        </div>
      </h1>

      <div class="row">

        <div class="col my-4 w-100">
          <div class="py-5 px-2 text-center alert <?php echo ($forbidden ? 'alert-warning' : 'alert-danger') ?>">
            <p class="h4 text-muted"><?php echo $warning ?></p>
          </div>
        </div>

        <?php if (!$forbidden): ?>
          <div class="col-12 text-center">
            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
              <input type="hidden" name="table" value="<?php echo $table ?>">
              <button class="btn btn-primary btn-md" name="cancel" value="<?php echo $object->id ?>">Cancel</button>
              <button class="btn btn-danger btn-md" name="delete" value="<?php echo $object->id ?>">Delete</button>
            </form>
          </div>
        <?php endif; ?>
  
      </div>

    </div>
  </main>
</div><!-- row -->

<?php include SHARED_PATH . '/staff_footer.php'; ?>