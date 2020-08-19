<?php
use App\Classes\Post;
use App\Classes\File;
use App\Classes\User;

require_once('../../../src/initialize.php');

// Check Logged In >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
if (require_login()) redirect_to(url_for('login.php'));
// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Check Logged In

if (isset($_GET['id'])) {

  $cmd = $_GET['cmd'] ?? false;
  $post = Post::findById($_GET['id']);

  if (!$cmd || !$post) {
    redirect_to(url_for('/staff/posts/index.php'));
  }

  if ($cmd == 'unpublish') {
    $post->published = '0';
    if ($post->save() === true) {
      $session->message("The post '" . $post->title . "' was unpublished.");
      redirect_to(url_for('/staff/posts/index.php'));
    }
  }

}

$posts = Post::findWhere(
  ['user_id' => $session->getuserId()],
  ['updated_at' => 'DESC']
);

?>
<!DOCTYPE html>
<html lang="en">

  <?php
    $page_title = ($session->isAdmin() ? 'Admin Posts' : 'User Posts');
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
          <p class="lead">You have not posts yet.</p>
        
        <?php else: ?>
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
                  <td style="color:royalblue">
                    <?php
                      if ($post->published == 0) {
                        echo 'draft';
                      } elseif ($post->published == 1 && $post->proved == 0) {
                        echo 'awaiting moderation';
                      } elseif ($post->published == 1 && $post->proved == 1) {
                        echo 'published';
                      }
                    ?>
                  </td>
              
                  <td>
                    <?php 
                    if ($post->published && $post->proved): ?>
                      <a href="<?php echo url_for('/staff/posts/index.php?id=' . $post->id . '&cmd=unpublish') ?>" class="unpublish">
                        Unpublish
                      </a>
                    <?php elseif (!$post->published): ?>
                      <a href="<?php echo url_for('/staff/posts/edit.php?id=' . $post->id) ?>" class="edit">
                        Edit
                      </a>
                    <?php else: ?>
                      <span>&ndash;</span>
                    <?php endif; ?>
                  </td>

                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        
        <?php endif; ?>

      </div>
    </div>
    <!-- // Admin Content -->

  </div>

  <?php include SHARED_PATH . '/staff_footer.php'; ?>
</body>

</html>