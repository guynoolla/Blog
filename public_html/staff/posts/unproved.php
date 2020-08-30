<?php
use App\Classes\Post;
use App\Classes\File;
use App\Classes\User;

require_once('../../../src/initialize.php');

// Check Admin >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
if (!$session->isAdmin()) redirect_to(url_for('index.php'));
// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Check Admin

if (isset($_GET['id'])) {

  $cmd = $_GET['cmd'] ?? false;
  $post = Post::findById($_GET['id']);

  if (!$cmd || !$post) {
    redirect_to(url_for('/staff/posts/unproved.php'));
  }

  if ($cmd == 'unpublish') {
    $post->published = '0';
    
    if ($post->save() === true) {
      $session->message("Post '" . $post->title . "' was unpublished.");
      redirect_to(url_for('/staff/posts/drafts.php'));
    }

  } elseif ($cmd == 'prove') {
    $post->proved = '1';
    
    if ($post->save() === true) {
      $session->message("Post '" . $post->title . "' was proved.");
      redirect_to(url_for('/staff/posts/proved.php'));
    } else {
      dd('Cannot...');
    }
  }

}

$posts = Post::findWhere(
  ['published' => 1,'proved' => 0],
  ['updated_at' => 'DESC']
);

$page_title = 'Unproved Posts';
include SHARED_PATH . '/staff_header.php';
require '_common-posts-html.php'

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
              <th scope="col">Edited</th>
              <th scope="colgroup" colspan="2">Action</th>
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
                <td><?php echo (User::findById($post->user_id))->username ?></td>
                  <?php echo td_post_status($post) ?>
                </td>
                <td>
                  <span><?php echo date('M j, Y', strtotime($post->updated_at)) ?></span>
                </td>
                <?php echo td_colgroup_actions($post); ?>
                <?php echo td_colgroup_actions_admin($post); ?>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
  
      <?php endif; ?>

    </div>
  </main>
</div><!-- row -->

<?php include SHARED_PATH . '/staff_footer.php'; ?>