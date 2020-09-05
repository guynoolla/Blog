<?php
use App\Classes\Post;
use App\Classes\Topic;
use App\Classes\Pagination;

require_once '../src/initialize.php';

$headline = '';

// Handle Contact Form Submit -->
if (is_post_request()) {
  $email = $_POST['email'] ?? '';
  $message = $_POST['message'] ?? '';

  if ($email && $message) {
    $mailer = new App\Contracts\Mailer;
    $text = strip_tags($message);
    
    $mailer->send(ADMIN_EMAIL,'Contact Form', $text, $message);
    $session->message('Thank you for your message!');
    redirect_to(url_for('index.php'));
  }
} // <--Contact Form

if (isset($_GET['s'])) {
  $term = $_GET['s'] ?? '';

  $current_page = $_GET['page'] ?? 1;
  $per_page = 4;
  $total_count = Post::countAll([
    'proved' => 1, 'title' => ['like' => "%{$term}%"],
    "OR p.body LIKE '{%$term%}'"
  ]);
  $pagination = new Pagination($current_page, $per_page, $total_count);
  $posts = Post::querySearchPosts(trim($term), $per_page, $pagination->offset());

  if ($posts) {
    $headline = "You searched for '<strong>" . $term . "</strong>'";
  } else {
    $headline = "Nothing found for '<strong>" . $term . "</strong>'";
  }

} elseif (isset($_GET['id'])) {
  $topic_id = $_GET['id'] ?? 0;
  $posts = Post::queryPostsByTopic($topic_id);
  $topic_name = Topic::findById($topic_id)->name;

  $current_page = $_GET['page'] ?? 1;
  $per_page = 4;
  $total_count = Post::countAll(['topic_id' => $topic_id, 'proved' => '1']);
  $pagination = new Pagination($current_page, $per_page, $total_count, 'pagination-lg');

  if ($posts) {
    $headline = "You searched for posts under '<strong>" . $topic_name . "</strong>'";
  } else {
    $headline = "Sorry, no posts under '<strong>" . $topic_name . "</strong>' found.";
  }

} else {
  $current_page = $_GET['page'] ?? 1;
  $per_page = 4;
  $total_count = Post::countAll(['proved' => '1']);
  $pagination = new Pagination($current_page, $per_page, $total_count, 'pagination-lg');
  
  $posts = Post::queryProvedPosts($per_page, $pagination->offset());
  $page_title = 'Recent Posts';
}

include SHARED_PATH . '/public_header.php';

if (!isset($_GET['id']) && !isset($_GET['s'])) {
  include SHARED_PATH . '/carousel.php';
}

?>
<div class="container-md">
  <div class="row">
    
    <main class="main col-lg-8" role="main" id="homeMain">
      <div class="main-content">
        <?php if ($headline): ?>
          <h1 class="text-center"><?php echo $headline ?></h1>
        <?php endif;

        $total = count($posts);
        
        foreach ($posts as $idx => $post):
          $num = $idx + 1;
          $grow = false;
          $shrink = false;

          if (($idx + 1) == $total) {
            $grow = (!(isset($posts_inside)) || ($posts_inside == 0));
            $shrink = ($current_page > 1 && (count($posts) % 2 == 0));
          } 
          
          if (($num <= 2 || $grow) || ($num == 1 && $shrink)): // first & second posts go here
            ?>
            <div class="lg-one-article-row">
              <article>
                <div class="post">
                  <div class="post-item-wrap">
                    <div class="post-item-inner">
                      <h2 class="entry-title text-center">
                        <a href="<?php echo url_for('post/' . u($post->title) . '?id=' . $post->id) ?>">
                          <?php echo h($post->title) ?>
                        </a>
                      </h2>
                      <div class="entry-meta">
                        <span class="posted-on">Posted on <a href="#" rel="bookmark">
                          <time class="entry-date published" datetime="<?php echo $post->created_at ?>">
                            <?php echo date('M j, Y', strtotime($post->created_at)) ?>
                          </time>
                        </a></span>by <span class="author vcard">
                          <a class="url fn n" href="https://colorlib.com/activello/author/aigars-silkalns/">
                            <?php echo h($post->username) ?>
                          </a></span>
                      </div>

                      <div class="post-format<?php echo ($post->format == 'video' ? ' post-format--video' : '') ?>">
                        <?php if ($post->format == 'image'): ?>
                          <a href="<?php echo url_for('post/' . u($post->title) . '?id=' . $post->id) ?>">
                            <div class="ard ard--image ard--hor-lg" style="background-image: url(<?php echo url_for('assets/images' . $post->image) ?>)"></div>
                          </a>
                        <?php elseif ($post->format == 'video'): ?>
                          <div class="embed-responsive embed-responsive-16by9">
                            <?php echo $post->getEntryVideo() ?>
                            <a class="overlay" href="<?php echo url_for('post/' . u($post->title) . '?id=' . $post->id) ?>"></a>
                          </div>
                        <?php endif; ?>
                      </div>

                      <div class="entry-content"><?php echo Post::excerpt($post->body) ?></div>
                    </div>
                  </div>
                </div>
              </article>
            </div>
            <?php
          else: // all posts after second go here
            static $posts_inside = 0;
            // open div if post number is odd
            if (!($num % 2 == 0)) : $posts_inside++; ?>
              <div class="lg-two-articles-row">
              <?php
            else:
              $posts_inside++;
            endif; ?>
            <article>
              <div class="post">
                <div class="post-item-wrap">
                    <div class="post-item-inner">
                      <h2 class="entry-title text-center"><a href="<?php echo url_for('post/' . u($post->title) . '?id=' . $post->id) ?>"><?php echo h($post->title) ?></a></h2>
                      <div class="entry-meta">
                        <span class="posted-on">Posted on <a href="#" rel="bookmark">
                          <time class="entry-date published" datetime="<?php echo $post->created_at ?>">
                            <?php echo date('M j, Y', strtotime($post->created_at)) ?>
                          </time>
                        </a></span>by <span class="author vcard">
                          <a class="url fn n" href="https://colorlib.com/activello/author/aigars-silkalns/">
                            <?php echo h($post->username) ?>
                          </a></span>
                      </div>

                      <div class="post-format<?php echo ($post->format == 'video' ? ' post-format--video' : '') ?>">
                        <?php if ($post->format == 'image'): ?>
                          <a href="<?php echo url_for('post/' . u($post->title) . '?id=' . $post->id) ?>">  
                            <div class="ard ard--image ard--ver-md ard--opt-lg" style="background-image: url(<?php echo url_for('assets/images' . $post->image) ?>)"></div>
                          </a>
                        <?php elseif ($post->format == 'video'): ?>
                          <div class="embed-responsive embed-responsive-16by9">
                            <?php echo $post->getEntryVideo() ?>
                            <a class="overlay" href="<?php echo url_for('post/' . u($post->title) . '?id=' . $post->id) ?>"></a>
                          </div>
                        <?php endif; ?>
                      </div>

                      <div class="entry-content"><?php echo Post::excerpt($post->body) ?></div>
                    </div>
                </div>
              </div>
            </article>
            <!--Close div after two posts are inside it and reset $posts_inside-->
            <?php if ($posts_inside == 2): $posts_inside = 0; ?>
              </div> <!--lg-two-articles-row-->
            <?php endif;
          endif;
        endforeach; // Posts loop done!
        
        // Close two articles row if it was not because odd Posts Total
        if (isset($posts_inside) && $posts_inside == 1) echo '</div>';
        ?>

        <div class="row justify-content-center mt-4"><?php
          $url = url_for('index.php');
          echo $pagination->page_links($url);
        ?></div>

      </div> <!--main content-->
    </main> <!-- main -->

    <aside class="sidebar col-lg-4" role="complementary">
      <?php include SHARED_PATH . '/aside.php' ?>
    </aside>

  </div>
</div> <!--container-->

<?php include SHARED_PATH . '/public_footer.php' ?>