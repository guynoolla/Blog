<?php
use App\Classes\Post;
use App\Classes\File;
use App\Classes\User;
use App\Classes\Pagination;

require_once('../../../src/initialize.php');

// Check Admin >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
if (!$session->isAdmin()) redirect_to(url_for('index.php'));
// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Check Admin

if (isset($_GET['id'])) {

  $cmd = $_GET['cmd'] ?? false;
  $post = Post::findById($_GET['id']);

  if (!$cmd || !$post) redirect_to(url_for('index.php'));

  if ($cmd == 'disprove') {
    $post->approved = '0';

    if ($post->save() === true) {
      $session->message("Post '" . $post->title . "' was disapproved.");
      if ($post->user_id == $session->getUserId()) {
        redirect_to(url_for('staff/posts/index.php'));
      } else {
        redirect_to(url_for('staff/posts/published.php'));
      }
    }
  }

}

$current_page = $_GET['page'] ?? 1;
$per_page = DASHBOARD_PER_PAGE;
$total_count = Post::countAll([
  'approved' => '1',
  'user_id' => ['!=' => $session->getUserId()]
]);
$pagination = new Pagination($current_page, $per_page, $total_count);

$sql = "SELECT p.*, t.id AS tid, t.name AS category,";
$sql .= " u.username, u.email AS user_email, u.email_confirmed AS ue_confirmed";
$sql .= " FROM `posts` AS p";
$sql .= " LEFT JOIN `users` AS u ON p.user_id = u.id";
$sql .= " LEFT JOIN `categories` AS t ON p.category_id = t.id";
$sql .= " WHERE p.approved='1'";
$sql .= " AND p.user_id != '{$session->getUserId()}'";
$sql .= " ORDER BY p.updated_at DESC";
$sql .= " LIMIT {$per_page}";
$sql .= " OFFSET {$pagination->offset()}";
$posts = Post::findBySql($sql);

$page_title = 'Users approved posts';
include SHARED_PATH . '/staff_header.php';
include '_common-posts-html.php';
include '../_common-html-render.php';

?>
<div class="row">
  <aside class="sidebar col col-lg-3">
    <?php include SHARED_PATH . '/staff_sidebar.php' ?>
  </aside>

  <main class="main col col-lg-9">
    <div class="main-content">

      <h1 class="dashboard-headline">
        <span class="text-success"><?php echo $page_title ?></span>
        <div class="nav-btn back-btn-pos"><?php echo page_back_button() ?></div>
      </h1>

      <?php
      if (empty($posts)):
        echo tableIsEmpty();

      else: ?>
        <?php echo tableSearchForm('Post title') ?>

        <div class="loadContentJS" data-access="user_post">
          <table class="table table-bordered table-hover table-light <?php echo TABLE_SIZE ?>">
            <thead class="bg-muted-lk text-muted">
              <tr>
                <th scope="col">#</th>
                <th scope="col"><a href="#title" class="click-load" data-access="user_post" data-value="asc" data-type="title_order">Title</a></th>
                <th scope="col">Category</th>
                <th scope="col"><a href="#author" class="click-load" data-access="user_post" data-value="asc" data-type="author_order">Author</a></th>
                <th scope="col">Email</th>
                <th scope="col"><a href="#created" class="click-load" data-access="user_post" data-value="asc" data-type="date_order">Published</a></th>
                <th scope="colgroup" colspan="1">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($posts as $key => $post): ?>
                <tr>
                  <th scope="row"><?php echo $key + 1 ?></th>
                  <?php echo td_post_title($post) ?>
                  <?php echo td_post_category($post, 'user_post') ?>
                  <?php echo td_post_author($post, 'user_post') ?>
                  <?php echo td_post_author_email($post) ?>
                  <?php echo td_post_date($post, 'user_post') ?>
                  <?php echo td_actions_column_snd($post, $session->isAdmin()); ?>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <?php
            $url = url_for('staff/posts/approved.php');
            echo $pagination->pageLinks($url);
          ?>
        </div>

      <?php endif; ?>

    </div>
  </main>
</div><!-- row -->

<?php include SHARED_PATH . '/staff_footer.php'; ?>