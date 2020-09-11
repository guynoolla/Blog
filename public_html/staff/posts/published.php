<?php
use App\Classes\Post;
use App\Classes\File;
use App\Classes\User;
use App\Classes\Pagination;

require_once('../../../src/initialize.php');

// Check Admin >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
if (!$session->isAdmin()) redirect_to(url_for('index.php'));
// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Check Admin

if (isset($_GET['id'])) {

  $cmd = $_GET['cmd'] ?? false;
  $post = Post::findById($_GET['id']);

  if (!$cmd || !$post) {
    redirect_to(url_for('staff/posts/published.php'));
  }

  if ($cmd == 'edit') {
    redirect_to(url_for('staff/posts/edit.php?id=' . $_GET['id']));

  } elseif ($cmd == 'unpublish') {
    $post->published = '0';
    
    if ($post->save() === true) {
      $session->message("Post '" . $post->title . "' was unpublished.");
      redirect_to(url_for('staff/posts/drafts.php'));
    }

  } elseif ($cmd == 'approve') {
    $post->approved = '1';
    
    if ($post->save() === true) {
      $session->message("Post '" . $post->title . "' was approved.");
      redirect_to(url_for('staff/posts/approved.php'));
    }
  } 

}

$current_page = $_GET['page'] ?? 1;
$per_page = DASHBOARD_PER_PAGE;
$total_count = Post::countAll([
  'published'=>'1',
  'approved'=>'0',
  'user_id'=>['!=' => $session->getUserId()]
]);
$pagination = new Pagination($current_page, $per_page, $total_count);

$sql = "SELECT p.*, u.username, t.id AS tid, t.name AS topic";
$sql .= " FROM posts AS p";
$sql .= " LEFT JOIN users AS u ON p.user_id = u.id";
$sql .= " LEFT JOIN topics AS T ON p.topic_id = t.id";
$sql .= " WHERE p.published='1' AND p.approved='0'";
$sql .= " AND p.user_id != '{$session->getUserId()}'";
$sql .= " ORDER BY p.updated_at DESC";
$sql .= " LIMIT {$per_page}";
$sql .= " OFFSET {$pagination->offset()}";
$posts = Post::findBySql($sql);

$page_title = 'Author\'s Published Posts';
include SHARED_PATH . '/staff_header.php';
include '_common-posts-html.php';

?>
<div class="row">
  <aside class="sidebar col-lg-3">
    <?php include SHARED_PATH . '/staff_sidebar.php' ?>
  </aside>

  <main class="main col-lg-9">
    <div class="main-content">
      <?php echo page_back_button() ?>

      <h2 style="text-align: center;"><em class="text-primary">Published</em></h2>

      <?php if (empty($posts)): ?>
        <p class="lead">No posts here.</p>
      
      <?php else: ?>
        <?php echo display_session_message('msg success') ?>

        <table class="table table-bordered table-hover table-light table-sm">
          <thead class="bg-muted-lk text-muted">
            <tr>
              <th scope="col">#</th>
              <th scope="col" colspan="2">Title</th>
              <th scope="col">Topic</th>
              <th scope="col">Author</th>
              <th scope="col">Status</th>
              <th scope="col">Edited</th>
              <th scope="colgroup" colspan="2">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($posts as $key => $post): ?>
              <tr>
                <th scope="row"><?php echo $key + 1 ?></th>
                <?php echo td_post_title($post) ?>
                <td><?php echo td_post_topic($post) ?></td>
                <td><?php echo (User::findById($post->user_id))->username ?></td>
                  <?php echo td_post_status($post) ?>
                </td>
                <td>
                  <span><?php echo date('M j, Y', strtotime($post->updated_at)) ?></span>
                </td>
                <?php
                  echo td_actions_column_fst($post, $session->isAdmin());
                  echo td_actions_column_snd($post, $session->isAdmin());
                ?>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
  
        <?php
          $url = url_for('staff/posts/published.php');
          echo $pagination->pageLinks($url);
        ?>

      <?php endif; ?>

    </div>
  </main>
</div><!-- row -->

<?php include SHARED_PATH . '/staff_footer.php'; ?>