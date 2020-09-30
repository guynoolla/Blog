<?php
use App\Classes\Post;
use App\Classes\File;
use App\Classes\User;
use App\Classes\Pagination;

require_once('../../../src/initialize.php');

// Check Logged In >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
if (require_login()) redirect_to(url_for('staff/login.php'));
// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Check Logged In

// Check Admin >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
if (isset($_GET['id'])) {

  $cmd = $_GET['cmd'] ?? false;
  $post = Post::findById($_GET['id']);

  if (!$cmd || !$post) redirect_to(url_for('staff/index.php'));

  if ($session->isAdmin()) {
    $message = '';

    if ($cmd == 'publish') {
      $post->published = '1';
      $message = "The post '" . $post->title . "' was published.";

    } elseif ($cmd == 'unpublish') {
      $post->published = '0';
      $post->approved = '0';
      $message = "The post '" . $post->title . "' was unpublished.";
    
    } elseif ($cmd == 'approve') {
      $post->approved = '1';
      $message = "The post '" . $post->title . "' was approved.";

    } elseif ($cmd == 'disprove') {
      $post->published = '0';
      $post->approved = '0';
      $message = "The post '" . $post->title . "' was disapproved.";
    }

    if ($message && $post->save()) {
      $session->message($message);
      redirect_to(url_for('staff/posts/index.php'));
    }
  }

  if ($cmd == 'edit') {
    redirect_to(url_for('staff/posts/edit.php?id=' . $_GET['id']));
  }

}
// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Check Admin

$current_page = $_GET['page'] ?? 1;
$per_page = DASHBOARD_PER_PAGE;
$total_count = Post::countAll(['user_id' => $session->getUserId()]);
$pagination = new Pagination($current_page, $per_page, $total_count);

$sql = "SELECT p.*, u.username, t.id AS tid, t.name AS topic";
$sql .= " FROM `posts` AS p";
$sql .= " LEFT JOIN `users` AS u ON p.user_id = u.id";
$sql .= " LEFT JOIN `topics` AS t ON p.topic_id = t.id";
$sql .= " WHERE p.user_id='{$session->getUserId()}'";
$sql .= " ORDER BY p.updated_at DESC";
$sql .= " LIMIT {$per_page}";
$sql .= " OFFSET {$pagination->offset()}";
$posts = Post::findBySql($sql);

$page_title = ($session->isAdmin() ? 'Admin Posts' : 'User Posts');
include SHARED_PATH . '/staff_header.php';
include '_common-posts-html.php';

?>
<div class="row">
  <aside class="sidebar col-lg-3">
    <?php include SHARED_PATH . '/staff_sidebar.php' ?>
  </aside>

  <main class="main col-lg-9">
    <div class="main-content">
      
      <h2 class="text-center <?php echo $header_mb ?>">
        <?php echo $page_title ?>
        <div class="back-btn-pos"><?php echo page_back_button() ?></div>
      </h2>

      <div class="d-flex">
        <div class="search-widget flex-grow-1 py-1">
          <form id="adminSearchForm" data-type="search" data-access="own_post" method="post" action="<?php echo url_for($_SERVER['PHP_SELF']) ?>" class="form-search w-100" role="search">
            <div class="input-group">
              <label class="screen-reader-text" for="s">Search for:</label>
              <input id="s" name="s" type="text" class="form-control search-query" placeholder="Search..." value="">
              <div class="input-group-append">
                <button type="submit" name="submit" class="btn btn-outline-primary rounded-0">Search</button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <?php if (empty($posts)): ?>
        <p class="lead">No posts here.</p>
      
      <?php else: ?>
        <?php echo display_session_message('msg success') ?>

        <div class="loadPostsJS">
          <table class="table table-bordered table-hover table-light <?php echo $table_size ?>">
            <thead class="bg-muted-lk text-muted">
              <tr>
                <th scope="col">#</th>
                <th scope="col">Title</th>
                <th scope="col">Topic</th>
                <th scope="col">Status</th>
                <th scope="col">Edited</th>
                <th scope="colgroup" colspan="3">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($posts as $key => $post): ?>
                <tr>
                  <th scope="row"><?php echo $key + 1 ?></th>
                  <?php echo td_post_title($post) ?>
                  <?php echo td_post_topic($post) ?>
                  <?php echo td_post_status($post) ?>
                  <?php echo td_post_date($post) ?>
                  <?php echo td_actions_column_fst($post, $session->isAdmin()) ?>
                  <?php echo td_actions_column_snd($post, $session->isAdmin()) ?>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>

          <?php
            $url = url_for('staff/posts/index.php');
            echo $pagination->pageLinks($url);
          ?>
        </div>
  
      <?php endif; ?>

    </div>
  </main>
</div><!-- row -->

<?php include SHARED_PATH . '/staff_footer.php'; ?>