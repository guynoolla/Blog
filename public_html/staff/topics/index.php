<?php
use App\Classes\Topic;

require_once('../../../src/initialize.php');

// Check Admin >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
if (!$session->isAdmin()) redirect_to(url_for('index.php'));
// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Check Admin

if (isset($_GET['id'])) {
  $cmd = $_GET['cmd'] ?? false;
  $topic = Topic::findById($_GET['id']);

  if (!$cmd || !$topic) {
    redirect_to(url_for('/staff/topics/index.php'));
  }

  if ($cmd == 'delete') {
    $result = $topic->delete();
    $session->message("The topic '" . $topic->name . "' was deleted.");
    redirect_to(url_for('/staff/topics/index.php'));
  }
}

$topics = Topic::findAll();

?>
<!DOCTYPE html>
<html lang="en">

  <?php
    $page_title = 'Admin - List Topics';
    include SHARED_PATH . '/staff_header.php'
  ?>

  <div class="admin-wrapper clearfix">
  
    <?php include SHARED_PATH . '/staff_sidebar.php' ?>

    <!-- Admin Content -->
    <div class="admin-content clearfix">

      <div class="button-group">
        <?php echo page_back_link('Back', 'btn btn-sm') ?>
        <button class="btn btn-sm">
          <a href="<?php echo url_for('staff/topics/create.php') ?>">New Topic</a>
        </button>
      </div>

      <div class="">
        <h2 style="text-align: center;">Manage Topics</h2>

        <?php if (empty($topics)): ?>
          <p class="lead">There is no Topics yet.</p>
          <?php exit; ?>
        <?php endif; ?>

        <?php echo display_session_message('msg success') ?>

        <table>
          <thead>
            <th>N</th>
            <th>Name</th>
            <th>Description</th>
            <th colspan="1">Actions</th>
          </thead>
          <tbody>
            <?php foreach($topics as $key => $topic): ?>
              <tr class="rec">
                <td><?php echo $key + 1 ?></td>
                <td><a href="#"><?php echo $topic->name ?></a></td>
                <td><?php echo $topic->description ?></td>
                <td>
                  <a href="<?php echo url_for('/staff/topics/edit.php?id=' . $topic->id) ?>" class="edit">
                    Edit
                  </a>
                </td>
                <td>
                  <a href="<?php echo url_for('/staff/topics/index.php?id=' . $topic->id . '&cmd=delete') ?>" class="delete">
                    Delete
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


  <?php include SHARED_PATH . '/staff_footer.php' ?>
</body>

</html>