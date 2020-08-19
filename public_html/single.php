<?php
use App\Classes\Post;
use App\Classes\User;
use App\Classes\Like;

require_once '../src/initialize.php';

$id = $_GET['id'] ?? 0;

$post = Post::findById($id);
$url_parts = url_split_by_slash();
$_title = urldecode(end($url_parts));

if (!$post || $_title !== $post->title) {
  error_404();
}

if (!$post->proved) {
  $access = false;

  if ($session->isLoggedIn()) {
    if ($session->isAdmin()) {
      $access = true;
    } else {
      if ($session->getUserId() == $post->user_id) {
        $access = true;
      }
    }
  }
  
  if (!$access) {
    redirect_to(url_for('index.php'));
  }
}

$popular_posts = Post::findWhere(['proved' => 1]);

// User must be logged in to be able to like the post.
$like = $session->isLoggedIn()
      ? Like::getUserPostLike($session->getUserId(), $post->id)
      : false;

$likes_count = Like::countPostLikes($post->id);

?>
<!DOCTYPE html>
<html lang="en">

<?php
  $page_title = $post->title;
  include(SHARED_PATH . '/public_header.php');
?>

  <!-- <div id="fb-root"></div>
  <script>
  (function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s);
    js.id = id;
    js.src =
      'https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.2&appId=285071545181837&autoLogAppEvents=1';
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));
  </script> -->

  <!-- Page wrapper -->
  <div class="page-wrapper">

    <!-- content -->
    <div class="content clearfix">
      <div class="page-content single">
        <img src="<?php echo url_for('/assets/images/' . $post->image) ?>" class="post-image image-wide" alt="">
        <h2 style="text-align: center;"><?php echo $post->title ?></h2>
        <?php
          $class = $like ? ' like-red' : ' like-default';
          $action = $like ? 'delete' : 'create';
        ?>
        <div class="like-box<?php echo $class ?>"
          data-pid="<?php echo $post->id ?>"
          data-uid="<?php echo $session->getUserId() ?>"
          data-action="<?php echo $action ?>"
        >
          <i class="fa fa-heart-o" aria-hidden="true"></i>
          <i class="fa fa-heart" aria-hidden="true"></i>
          <span class="like-count"><?php echo $likes_count ?></span>
        </div>
        <div class="post-info">
          <i class="fa fa-user-o"></i> <?php echo h(User::findById($post->user_id)->username) ?>&nbsp;
          <i class="fa fa-calendar"></i> <?php echo date('F j, Y', strtotime($post->created_at)) ?>
        </div>
<?php

  // $a = 'https://www.youtube.com/watch?v=GDeJtgjvXTk';
  // $b = 'https://youtu.be/GDeJtgjvXTk';
  // $test = parse_url($b);
  // echo $test['host'];

?>
        <?php echo $post->getBodyWithVideo() ?>
      </div>

      <div class="sidebar single">

        <!-- Popular Posts -->
        <div class="section popular">
          <h2>Popular</h2>
          <?php foreach($popular_posts as $post): ?>
            <div class="post clearfix">
              <img src="<?php echo url_for('/assets/images' . $post->image) ?>">
              <a href="<?php echo url_for('post/' . u($post->title) . '?id=' . $post->id) ?>" class="title">
                <?php echo $post->title ?>
              </a>
            </div>
          <?php endforeach; ?>
        </div>
        <!-- // Popular Posts -->

        <?php include SHARED_PATH . '/_topics_widget.php' ?>

      </div>
    </div>
    <!-- // content -->

  </div>
  <!-- // page wrapper -->

  <?php include(SHARED_PATH . '/public_footer.php'); ?>
</body>

</html>