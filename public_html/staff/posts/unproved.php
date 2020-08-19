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

?>
<!DOCTYPE html>
<html lang="en">

  <?php
    $page_title = 'Unproved Posts';
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
            <th>Email</th>
            <th colspan="3">Actions</th>
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
                <?php $user = User::findById($post->user_id); ?>
                <td><?php echo $user->username ?></td>
                <td>
                  <a href="mailto: <?php echo $user->email ?>">
                    <?php echo $user->email ?>
                  </a>
                </td>
                <td>
                  <a href="<?php echo url_for('/staff/posts/edit.php?id=' . $post->id) ?>">
                    Edit
                  </a>
                </td>
                <td>
                  <?php if ($post->proved == 0): ?>
                    <a href="<?php echo url_for('/staff/posts/unproved.php?id=' . $post->id . '&cmd=prove') ?>" class="prove">
                      Prove
                    </a>
                  <?php endif; ?>
                </td>
                <td>
                  <a href="<?php echo url_for('/staff/posts/unproved.php?id=' . $post->id . '&cmd=unpublish') ?>" class="unpublish">
                    Unpublish
                  </a>
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