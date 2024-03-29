<?php
use App\Classes\Post;
use App\Classes\Category;
USE App\Classes\User;
use App\Classes\Pagination;

require_once '../src/initialize.php';

$headline = '';
$type = 'default';

/*
  GET POSTS BY SEARCH TERM ------------------------------------------*/

 if (isset($_GET['s'])) {
  $type = 'search';

  $term = $_GET['s'] ?? '';

  $current_page = $_GET['page'] ?? 1;
  $per_page = FRONTEND_PER_PAGE;
  $total_count = Post::countAll([
    'approved' => '1',
    ["( title LIKE '%?%' OR body LIKE '%?%' )", $term, $term]
  ]);

  $pagination = new Pagination($current_page, $per_page, $total_count);
  $posts = Post::querySearchPosts(trim($term), $per_page, $pagination->offset());

  if ($posts) {
    $headline = "You searched for '<strong>" . $term . "</strong>'";
  } else {
    $headline = "Nothing found for '<strong>" . $term . "</strong>'";
  }

  $page_title = 'Home - Search';

/*
  GET POSTS BY CATEGORY ------------------------------------------------*/

} elseif (isset($_GET['cid'])) {
  $type = 'category';

  $category_id = (int) $_GET['cid'] ?? 0;
  $category = Category::findById($category_id);

  $url_parts = url_split_by_slash();
  $_name = urldecode(end($url_parts));
  
  if (!$category || $_name !== $category->name) {
    error_404();
  }

  $current_page = $_GET['page'] ?? 1;
  $per_page = FRONTEND_PER_PAGE;
  $total_count = Post::countAll([
    'approved' => 1,
    'category_id' => $category_id,
  ]);

  $pagination = new Pagination($current_page, $per_page, $total_count, 'pagination-lg');
  $posts = Post::queryPostsByCategory($category_id, $per_page, $pagination->offset());

  if ($posts) $headline = $category->name;
  else $headline = $category->name;

  $page_title = 'Home - ' . $category->name;

/*
 GET POSTS BY AUTHOR ------------------------------------------------*/

} elseif (isset($_GET['uid'])) {
  $type = 'author';

  $user_id = $_GET['uid'] ?? 0;
  $user = User::findById($user_id);
  if (!$user) redirect_to(url_for('index.php'));  

  $current_page = $_GET['page'] ?? 1;
  $per_page = FRONTEND_PER_PAGE;
  $total_count = Post::countAll([
    'approved' => 1,
    'user_id' => $user_id
  ]);

  $pagination = new Pagination($current_page, $per_page, $total_count, 'pagination-lg');
  $posts = Post::queryPostsByAuthor($user_id, $per_page, $pagination->offset());

  if ($posts) {
    $headline = "Posts by '<strong>" . $user->username . "</strong>'";
  } else {
    $headline = "Posts by '<strong>" . $user->username . "</strong>' found.";
  }

  $page_title = 'Home - ' . $user->username;

/*
  GET POSTS ON DATE ------------------------------------------------ */
  
} elseif (isset($_GET['ymd'])) {
  $type = 'ondate';

  $created_at = urldecode($_GET['ymd']);
  $date_publ = date('Y-m-d', strtotime($created_at));
  $date_next = date('Y-m-d', strtotime('+ 1 day', strtotime($created_at)));

  $current_page = $_GET['page'] ?? 1;
  $per_page = FRONTEND_PER_PAGE;
  $total_count = Post::countAll([
    'approved' => 1,
    ["( created_at >= '?' AND created_at < '?' )", $date_publ, $date_next],
  ]);

  $pagination = new Pagination($current_page, $per_page, $total_count, 'pagination-lg');
  $posts = Post::queryPostsByDatePub(
    ['date_min' => $date_publ, 'date_max' => $date_next],
    $per_page,
    $pagination->offset()
  );

  $page_title = 'Home - ' . date('M j, Y', strtotime($created_at));

/*
  GET FRONTEND RECENT POSTS ----------------------------------------*/

} else {
  $type= 'default';
  $current_page = $_GET['page'] ?? 1;
  if ($current_page == 1) {
    $carousel_posts = Post::queryImageFormatPosts(6, 0);
  }
  $per_page = FRONTEND_PER_PAGE;
  $total_count = Post::countAll(['approved' => '1']);
  $pagination = new Pagination($current_page, $per_page, $total_count, 'pagination-lg');

  $posts = Post::queryApprovedPosts($per_page, $pagination->offset());
}

$page_title = isset($page_title) ? $page_title : 'Home';
include SHARED_PATH . '/public_header.php';
include SHARED_PATH . '/carousel.php';

?>
<div class="container-md">
  <div class="row">
  
    <?php
    if ($headline): ?>
      <div class="col-12 py-4 text-center category-headline-wrap">
        <h3 class="category-headline my-0"><?php echo $headline ?></h3><?php
        if ($type == 'category') {
          if ($category->description && $jsonstore->showCategoryDescription): ?>
            <p class="w-100 text-center mt-0"><?php
              echo $category->description ?>
            </p><?php 
          endif;
        } ?>
      </div><?php
    endif;

    ?>
    <main class="main col-lg-8" role="main" id="homeMain">
      <div class="main-content">
      <?php 

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
                      <a href="<?php echo url_for('category/' . u($post->category) . '?cid=' . $post->category_id) ?>" class="category category--dark text-center"><?php echo $post->category ?></a>
                      <h2 class="entry-title text-center">
                        <a href="<?php echo url_for('post/' . u($post->title) . '?id=' . $post->id) ?>">
                          <?php echo h($post->title) ?>
                        </a>
                      </h2>

                      <div class="entry-meta">
                        <span class="posted-on">Posted on <a href="<?php echo url_for('ondate/pub/?ymd=' . u(date('Y-m-d', strtotime($post->created_at)))) ?>" rel="bookmark">
                          <time class="entry-date published" datetime="<?php echo $post->created_at ?>">
                            <?php echo date('M j, Y', strtotime($post->created_at)) ?>
                          </time>
                        </a></span>by <span class="posted-by">
                          <a class="url fn n" href="<?php echo url_for('author/' . u($post->username) . '?uid=' . $post->user_id) ?>">
                            <?php echo h($post->username) ?>
                          </a></span>
                      </div>

                      <div class="post-format<?php echo ($post->format == 'video' ? ' post-format--video' : '') ?>">
                        <?php if ($post->format == 'image'): ?>
                          <a href="<?php echo url_for('post/' . u($post->title) . '?id=' . $post->id) ?>">
                            <div class="ard ard--wide-md">
                              <img class="ard-image ard-image--wide lazyload" data-sizes="auto" data-srcset="<?php echo Post::responsive($post->image) ?>" alt="<?php $post->title ?>">
                            </div>
                          </a>
                        <?php elseif ($post->format == 'video'): ?>
                          <div class="embed-responsive embed-responsive-16by9">
                            <?php echo $post->getEntryVideo() ?>
                            <a class="overlay" href="<?php echo url_for('post/' . u($post->title) . '?id=' . $post->id) ?>"></a>
                          </div>
                        <?php endif; ?>
                      </div>

                      <div class="entry-content"><?php echo $post->excerpt() ?></div>
                      <div class="read-more read-more--dark text-center mt-3">
                        <a href="<?php echo url_for('post/' . u($post->title)) . '?id=' . $post->id ?>">Read More</a>
                      </div>
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
                      
                    <a href="<?php echo url_for('category/' . u($post->category) . '?cid=' . $post->category_id) ?>" class="category category--dark text-center"><?php echo $post->category ?></a>
                      <h2 class="entry-title text-center"><a href="<?php echo url_for('post/' . u($post->title) . '?id=' . $post->id) ?>"><?php echo h($post->title) ?></a></h2>
                      
                      <div class="entry-meta">
                        <span class="posted-on">Posted on <a href="<?php echo url_for('ondate/pub/?ymd=' . u(date('Y-m-d', strtotime($post->created_at)))) ?>" rel="bookmark">
                          <time class="entry-date published" datetime="<?php echo $post->created_at ?>">
                            <?php echo date('M j, Y', strtotime($post->created_at)) ?>
                          </time>
                        </a></span>by<span>
                        <a href="<?php echo url_for('author/' . u($post->username) . '?uid=' . $post->user_id) ?>">
                          <?php echo h($post->username) ?>
                        </a></span>
                      </div>

                      <div class="post-format<?php echo ($post->format == 'video' ? ' post-format--video' : '') ?>">
                        <?php if ($post->format == 'image'): ?>
                          <a href="<?php echo url_for('post/' . u($post->title) . '?id=' . $post->id) ?>">  
                            <div class="ard ard--mid-md ard--tall-lg ard--mid-xl">
                              <img class="ard-image ard-image--center ard-image--wide lazyload" data-sizes="auto" data-srcset="<?php echo Post::responsive($post->image) ?>" alt="<?php $post->title ?>">
                            </div>
                          </a>
                        <?php elseif ($post->format == 'video'): ?>
                          <div class="embed-responsive embed-responsive-16by9">
                            <?php echo $post->getEntryVideo() ?>
                            <a class="overlay" href="<?php echo url_for('post/' . u($post->title) . '?id=' . $post->id) ?>"></a>
                          </div>
                        <?php endif; ?>
                      </div>

                      <div class="entry-content"><?php echo $post->excerpt() ?></div>
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
          $url = $_SERVER['REQUEST_URI'];
          echo $pagination->pageLinks($url);
        ?></div>

      </div> <!--main content-->
    </main> <!-- main -->

    <aside class="sidebar col-lg-4" role="complementary">
      <?php include SHARED_PATH . '/aside_sidebar.php' ?>
    </aside>

  </div>
</div> <!--container-->

<?php include SHARED_PATH . '/public_footer.php' ?>