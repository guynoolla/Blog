<?php

use App\Classes\Topic;
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
        If user posts exists they also will be permanently deleted!</p>
EOT;
    } else if ($table == 'topics') {
      $page_title = 'Delete Topic';
      $object = Topic::findById($id);
      $warning = <<<EOT
        <p>Are you sure you want to delete the topic <strong class="font-weight-bold">$object->name</strong>?<br>
        In case topic has post(s) you can't delete it unless you delete those post(s)!</p>
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
        } 
      }

    } else if ($table == 'topics') { // topics table
      $page_title = 'Topics';
      $topic = Topic::findById($id);
      
      if ($cancel) {
        $session->message("The topic '" . $topic->name . "' deletion canceled.");
        redirect_to(url_for('staff/topics/index.php'));

      } else if ($delete) {
        if ($topic->delete()) {
          $session->message("The topic '" . $topic->name . "' was deleted.");
          redirect_to(url_for('staff/topics/index.php'));
        } else {
          $posts_count = Post::countAll(['topic_id' => $id]);
          if ($posts_count > 0) {
            $dep_word = $posts_count > 1 ? 'posts' : 'post'; 
            $session->store([
              'table' => $table,
              'warning' => "This topic can't be deleted. It has {$posts_count} {$dep_word}."
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
  <aside class="sidebar col-lg-3">
    <?php include SHARED_PATH . '/staff_sidebar.php' ?>
  </aside>

  <main class="main col-lg-9">
    <div class="main-content adminContentJS">
      <h2 style="text-align: center;"><?php echo $page_title ?></h2>

      <div class="my-2">
        <?php
        if ($table == 'users'): ?>
          <a class="btn btn-outline-primary btn-md" href="<?php echo url_for('staff/users/index.php') ?>">Back</a>
        <?php elseif ($table == 'topics'): ?>
          <a class="btn btn-outline-primary btn-md" href="<?php echo url_for('staff/topics/index.php') ?>">Back</a>
        <?php elseif ($table ==  'posts'): ?>
          <a class="btn btn-outline-primary btn-md" href="<?php echo url_for('staff/posts/index.php') ?>">Back</a>
        <?php endif;
        ?>
      </div>

      <div class="row d-flex justify-content-center">

        <div class="align-self-center my-4">
          <div class="p-5 alert <?php echo ($forbidden ? 'alert-info' : 'alert-warning') ?>">
            <?php echo $warning ?>
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