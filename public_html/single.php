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

if ($session->isLoggedIn()) {
  $like = new Like([
    'post_id' => $post->id,
    'user_id' => $session->getUserId()
  ]);
}
if (isset($like)) {
  $class = $like->liked ? ' like-red' : ' like-default';
  $action = $like->liked ? 'delete' : 'create';
}

$page_title = $post->title;
include(SHARED_PATH . '/public_header.php');

?><div class="container-md">
  <div class="row">
    
    <main class="main col-lg-8" role="main">
      <div class="main-content">
          <div class="lg-one-article-row">
            <article class="single">
              <div class="w-100 px-1 clearfix pr-3">
                <?php if ($session->getUserId() == $post->user_id): ?>
                  <a class="btn btn-sm btn-outline-secondary" href="<?php echo url_for('staff/posts/edit.php') . '?id=' . $post->id . '&cmd=edit' ?>">Edit</a>
                  <a class="btn btn-sm btn-outline-secondary" href="<?php echo page_back_url() ?>">Back</a>
                <?php endif; ?>

                <div class="like-box like-box--white<?php echo $class ?>"
                  data-pid="<?php echo $post->id ?>"
                  data-uid="<?php echo $session->getUserId() ?>"
                  data-action="<?php echo $action ?>"
                >
                  <!-- <span class="icons-wrap">
                    <i class="fa fa-heart-o" aria-hidden="true"></i>
                    <i class="fa fa-heart" aria-hidden="true"></i>
                  </span> -->
                  <span class="svg-icons-wrap">
                    <svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="heart" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-icon svg-icon--light svg-inline--fa fa-heart fa-w-16 fa-3x"><path fill="currentColor" d="M458.4 64.3C400.6 15.7 311.3 23 256 79.3 200.7 23 111.4 15.6 53.6 64.3-21.6 127.6-10.6 230.8 43 285.5l175.4 178.7c10 10.2 23.4 15.9 37.6 15.9 14.3 0 27.6-5.6 37.6-15.8L469 285.6c53.5-54.7 64.7-157.9-10.6-221.3zm-23.6 187.5L259.4 430.5c-2.4 2.4-4.4 2.4-6.8 0L77.2 251.8c-36.5-37.2-43.9-107.6 7.3-150.7 38.9-32.7 98.9-27.8 136.5 10.5l35 35.7 35-35.7c37.8-38.5 97.8-43.2 136.5-10.6 51.1 43.1 43.5 113.9 7.3 150.8z" class="fa-light"></path></svg>
                    <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="heart" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-icon svg-icon--dark svg-inline--fa fa-heart fa-w-16 fa-3x"><path fill="currentColor" d="M462.3 62.6C407.5 15.9 326 24.3 275.7 76.2L256 96.5l-19.7-20.3C186.1 24.3 104.5 15.9 49.7 62.6c-62.8 53.6-66.1 149.8-9.9 207.9l193.5 199.8c12.5 12.9 32.8 12.9 45.3 0l193.5-199.8c56.3-58.1 53-154.3-9.8-207.9z" class="fa-dark"></path></svg>
                  </span>
                  <span class="like-count"><?php echo Like::countPostLikes($post->id); ?></span>
                </div>

              </div>

              <div class="post">
                <div class="post-item-wrap">
                  <div class="post-item-inner border-bottom-0">

                    <h2 class="entry-title text-center mt-0"><a href=""><?php echo h($post->title) ?></a></h2>

                    <div class="entry-meta">
                      <span class="posted-on">Posted on <a href="#" rel="bookmark">
                        <time class="entry-date published" datetime="<?php echo $post->created_at ?>">
                          <?php echo date('M j, Y', strtotime($post->created_at)) ?>
                        </time>
                      </a></span>by <span class="author">
                        <?php echo User::findById($post->user_id)->username ?>
                      </span>
                    </div>

                    <a href="#">
                      <img src="<?php echo url_for('assets/images' . $post->image) ?>" alt="Image">
                    </a>

                  </div>
                  <div><?php echo $post->getBodyWithVideo() ?></div>
                </div>
              </div>
            </article>
            
            <div class="mt-3">
              <a class="btn btn-sm btn-outline-secondary" href="<?php echo page_back_url() ?>">Back</a>
            </div>
          </div>
      </div> <!--main content-->
    </main> <!-- main -->

    <aside class="sidebar col-lg-4" role="complementary">  
      <?php include SHARED_PATH . '/aside.php' ?>
    </aside>

  </div>
</div> <!--container-->

<?php include SHARED_PATH . '/public_footer.php' ?>