<?php
use App\Classes\Post;
USE App\Classes\File;

require_once('../../../src/initialize.php');

// Check Author >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
if (!$session->isAuthor()) redirect_to(url_for('index.php'));
// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Check Author

function posts_creation_limit() {
  global $session;
  $limit = 10;
  $count = Post::countAll([
    'user_id' => $session->getUserId(),
    ['?', 'DATE(`created_at`) = CURDATE()']
  ]);

  if ($count >= $limit) {
    $session->message('Sorry, you reached the maximum  posts per day (' . $count . ')');
    redirect_to(url_for('staff/posts/index.php'));
  }
}

if (is_post_request()) {

  posts_creation_limit();

  $post = new Post($_POST['post']);
  $post->formEditScenario('create', $session->getUserId());

  if (isset($_FILES['image'])) {
    $image = new File($_FILES['image']);
    $post->fileInstance($image);
  }

  $result = $post->save();

  if ($result === true) {
    if ($post->published) {
      $session->message("Thank you for your post! It is awaiting admin moderation.");
    } else {
      $session->message("Your Post '" . $post->title . "' saved as draft!");
    }
    redirect_to(url_for('/staff/posts/index.php'));
  }

} else {

  posts_creation_limit(3);

  $post = new Post;
}

$page_title = 'New Post';
include SHARED_PATH . '/staff_header.php';

?>
<div class="row">
  <aside class="sidebar col col-lg-3">
    <?php include SHARED_PATH . '/staff_sidebar.php' ?>
  </aside>

  <div class="main col col-lg-9">
    <div class="main-content adminContentJS">
      <?php include('./_form.php') ?>
    </div>
  </div>
</div>

<?php include SHARED_PATH . '/staff_footer.php'; ?>
