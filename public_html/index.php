<?php
use App\Classes\Post;
use App\Classes\Topic;

require_once '../src/initialize.php';

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

$trend_posts = Post::queryProvedPosts();

if (isset($_GET['search_term'])) {
  $term = $_GET['search_term'] ?? '';
  $posts = Post::querySearchPosts(trim($term));
  if ($posts) {
    $page_title = "You searched for '" . $term . "'";
  } else {
    $page_title = "Nothing found for '" . $term . "'";
  }
} elseif (isset($_GET['id'])) {
  $topic_id = $_GET['id'] ?? 0;
  $posts = Post::queryPostsByTopic($topic_id);
  $topic_name = Topic::findById($topic_id)->name;

  if ($posts) {
    $page_title = "You searched for posts under '" . $topic_name . "'";
  } else {
    $page_title = "Sorry, no posts under '" . $topic_name . "' found.";
  }
} else {
  $posts = Post::queryProvedPosts();
  $page_title = 'Recent Posts';
}

include SHARED_PATH . '/public_header.php';

include SHARED_PATH . '/carousel.php';

?>
<div class="container-md">
  <div class="row">
    
    <main class="main col-lg-8" role="main">
      <div class="main-content">
        <?php
        foreach ($posts as $idx => $post):
          $num = $idx + 1;
          if ($num <= 2): // first & second posts go here
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
                            <a href="<?php echo url_for('post/' . u($post->title) . '?id=' . $post->id) ?>">
                              <img src="<?php echo url_for('assets/images' . $post->image) ?>" alt="Image" class="tm-image">
                            </a>
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
                              <a href="<?php echo url_for('post/' . u($post->title) . '?id=' . $post->id) ?>">
                                <img src="<?php echo url_for('assets/images' . $post->image) ?>" alt="Image" class="tm-image">
                              </a>
                              <div class="entry-content">
                                  <p>Puzzle is a Bootstrap (v3.3.6) HTML CSS layout provided by <span class="light-blue-text">templatemo</span>. You can download, modify and use this layout for absolutely free of charge.</p>
                              </div>
                          </div>
                      </div>
                  </div>
              </article>
            <!--Close div after two posts are inside it and reset $posts_inside-->
            <?php if ($posts_inside == 2) : $posts_inside = 0; ?>
              </div> <!--lg-two-articles-row-->
            <?php endif;
          endif;
        endforeach; // Posts loop done!
        
        // Close two articles row if it was not because odd Posts Total
        if (isset($posts_inside) && $posts_inside == 1) echo '</div>';
        ?>
      </div> <!--main content-->
    </main> <!-- main -->

    <aside class="sidebar col-lg-4" role="complementary">
      <?php include SHARED_PATH . '/aside.php' ?>
    </aside>

  </div>
</div> <!--container-->

<?php include SHARED_PATH . '/public_footer.php' ?>