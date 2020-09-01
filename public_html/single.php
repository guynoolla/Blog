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

$page_title = $post->title;
include(SHARED_PATH . '/public_header.php');

?><div class="container-md">
  <div class="row">
    
    <main class="main col-lg-8" role="main">
      <div class="main-content">
          <div class="lg-one-article-row">
            <article>
              <div class="w-100 text-right border-bottom py-1 px-1 rounded bg-gray-lk">
                <a class="btn btn-outline-secondary" href="<?php echo url_for('staff/posts/edit.php') . '?id=' . $post->id . '&cmd=edit' ?>">Edit</a>
              </div>
              <div class="post">
                <div class="post-item-wrap">
                    <div class="post-item-inner">
                      <h2 class="entry-title text-center"><a href=""><?php echo h($post->title) ?></a></h2>
                      <div class="entry-meta">
                        <span class="posted-on">Posted on <a href="#" rel="bookmark">
                          <time class="entry-date published" datetime="<?php echo $post->created_at ?>">
                            <?php echo date('M j, Y', strtotime($post->created_at)) ?>
                          </time>
                        </a></span>by <span class="author vcard">
                        <!-- <a class="url fn n" href="https://colorlib.com/activello/author/aigars-silkalns/">
                          <php echo h($post->username) ?>
                        </a></span> -->
                      </div>
                      <a href="#">
                        <img src="<?php echo url_for('assets/images' . $post->image) ?>" alt="Image" class="tm-image">
                      </a>
                    </div>
                    <div><?php echo $post->getBodyWithVideo() ?></div>
                </div>
              </div>
            </article>
          </div>
      </div> <!--main content-->
    </main> <!-- main -->

    <aside class="sidebar col-lg-4" role="complementary">  
      <?php include SHARED_PATH . '/aside.php' ?>
    </aside>

  </div>
</div> <!--container-->

<?php include SHARED_PATH . '/public_footer.php' ?>