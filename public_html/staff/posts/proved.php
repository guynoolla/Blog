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

  if (!$cmd || !$post) redirect_to(url_for('index.php'));

  if ($cmd == 'disprove') {
    $post->proved = '0';

    if ($post->save() === true) {
      $session->message("Post '" . $post->title . "' was disproved.");
      redirect_to(url_for('/staff/posts/unproved.php'));
    }
  }

}

$posts = Post::findWhere(
  ['proved' => 1],
  ['updated_at' => 'DESC']
);

?>
<!DOCTYPE html>
<html lang="en">

  <?php
    $page_title = 'Proved Posts';
    include SHARED_PATH . '/staff_header.php';
  ?>

  <div class="admin-wrapper clearfix">

    <?php include SHARED_PATH . '/staff_sidebar.php'; ?>

    <!-- Admin Content -->
    <div class="admin-content clearfix">

      <div class="button-group">
        <?php echo page_back_link('Back', 'btn btn-sm') ?>
      </div>

      <div class="">
        <h2 style="text-align: center;"><?php echo $page_title ?></h2>

        <?php if (empty($posts)): ?>
          <p class="lead">There is no Posts yet.</p>
          <?php exit; ?>
        <?php endif; ?>

        <?php echo display_session_message('msg success') ?>

        <table>
          <thead>
            <th>N</th>
            <th>Title</th>
            <th>Author</th>
            <th>Status</th>
            <th colspan="1">Action</th>
          </thead>
          <tbody>
            <?php foreach($posts as $key => $post): ?>
              <tr class="rec">
                <td><?php echo $key + 1 ?></td>
                <td>
                  <a href="<?php echo url_for('post/' . u($post->title) . '?id=' . $post->id) ?>">
                    <?php echo $post->title ?>
                  </a>
                </td>
                <td><?php echo (User::findById($post->user_id))->username ?></td>
                <td>
                  <?php
                    if ($post->published == 0) {
                      echo 'draft';
                    } elseif ($post->published == 1 && $post->proved == 0) {
                      echo 'on moderation';
                    } elseif ($post->published == 1 && $post->proved == 1) {
                      echo 'published';
                    }
                  ?>
                </td>
             
                <td>
                  <?php if ($post->published && $post->proved): ?>
                    <a href="<?php echo url_for('/staff/posts/proved.php?id=' . $post->id . '&cmd=disprove') ?>" class="disprove">
                      Disprove
                    </a>
                  <?php endif; ?>
                </td>

              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

      </div>
    </div>
    <!-- // Admin Content -->

  </div>

  <?php include SHARED_PATH . '/staff_footer.php'; ?>
</body>

</html>