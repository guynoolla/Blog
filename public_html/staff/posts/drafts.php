<?php
use App\Classes\Post;
use App\Classes\File;
use App\Classes\User;

require_once('../../../src/initialize.php');

// Check Admin >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
if (!$session->isAdmin()) redirect_to(url_for('index.php'));
// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Check Admin

if (isset($_GET['id'])) {

  $cmd = $_GET['cmd'] ?? false;
  $post = Post::findById($_GET['id']);

  if (!$cmd || !$post) {
    redirect_to(url_for('/staff/posts/unproved.php'));
  }

  if ($cmd == 'delete') {
    $post->fileInstance(new File);
    if ($post->delete() === true) {
      $session->message("Post '" . $post->title . "' was deleted.");
    }
  }

}

$posts = Post::findWhere(
  ['published' => 0],
  ['updated_at' => 'DESC']
);

$page_title = 'Draft Posts';
include SHARED_PATH . '/staff_header.php';
require '_common-posts-html.php';

?>
<div class="row">
  <aside class="sidebar col-lg-3">
    <?php include SHARED_PATH . '/staff_sidebar.php' ?>
  </aside>

  <main class="main col-lg-9">
    <div class="main-content">
      <?php echo page_back_button() ?>
      
      <h2 style="text-align: center;"><?php echo $page_title ?></h2>

      <?php if (empty($posts)): ?>
        <p class="lead">You have not posts yet.</p>
      
      <?php else: ?>
        <?php echo display_session_message('msg success') ?>

        <table class="table table-bordered table-hover table-light table-sm">
          <thead class="bg-muted-lk text-muted">
            <tr>
              <th scope="col">#</th>
              <th scope="col">Title</th>
              <th scope="col">Author</th>
              <th scope="col">Status</th>
              <th scope="col">Email</th>
              <th scope="col">Edited</th>
              <th scope="col">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($posts as $key => $post): ?>
              <tr>
                <th scope="row"><?php echo $key + 1 ?></th>
                <td>
                  <a href="<?php echo url_for('post/' . u($post->title) . '?id=' . $post->id) ?>">
                    <?php echo $post->title ?>
                  </a>
                </td>
                <td>
                  <?php $user = User::findById($post->user_id) ?>
                  <a><?php echo h($user->username) ?></a>
                </td>
                <?php echo td_post_status($post) ?>
                <td>
                  <a href="mailto: <?php echo $user->email ?>">
                    <?php echo h($user->email) ?>
                  </a>
                </td>
                <td>
                  <span><?php echo date('M j, Y', strtotime($post->updated_at)) ?></span>
                </td>
                <td>
                  <a class="btn-lk btn-lk--danger" href="<?php echo url_for('staff/posts/drafts.php?id=' . $post->id . '&cmd=delete') ?>">Delete</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
  
      <?php endif; ?>

    </div>
  </main>
</div><!-- row -->

<?php include SHARED_PATH . '/staff_footer.php'; ?>