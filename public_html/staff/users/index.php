<?php
use App\Classes\User;

require_once('../../../src/initialize.php');

// Check Admin >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
if (!$session->isAdmin()) redirect_to(url_for('/index.php'));
// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Check Admin

if (isset($_GET['id']) && isset($_GET['cmd'])) {
  $id = $_GET['id'] ?? 0;
  $cmd = $_GET['cmd'] ?? 0;

  if ($id && $cmd) {
    if ($cmd === 'delete') {
      $user = User::findById($id);
  
      if ($user->delete() === true) {
        $session->message("The user '" . $user->username . "' was deleted.");
        redirect_to(url_for('/staff/users/index.php'));
      }
    }
  }
}

$users = User::findAll();

?>
<!DOCTYPE html>
<html lang="en">

  <?php
    $page_title = 'List Users';
    include SHARED_PATH . '/staff_header.php';
  ?>

  <div class="admin-wrapper clearfix">

    <?php include SHARED_PATH . '/staff_sidebar.php' ?>

    <!-- Admin Content -->
    <div class="admin-content clearfix">

      <div class="button-group">
        <?php echo page_back_link('Back', 'btn btn-sm') ?>
      </div>

      <div class="">
        <h2 style="text-align: center;">Manage Users</h2>

        <?php if (empty($users)): ?>
          <p class="lead">There is no Users yet.</p>
          <?php exit; ?>
        <?php endif; ?>

        <?php echo display_session_message('msg success') ?>

        <table>
          <thead>
            <th>N</th>
            <th>Username</th>
            <th>Email</th>
            <th colspan="2">Actions</th>
          </thead>
          <tbody>
            <?php foreach($users as $key => $user): ?>
              <tr class="rec">
                <td><?php echo $key + 1 ?></td>
                <td>
                  <a href="#"><?php echo h($user->username) ?></a>
                  <span><?php echo $user->isAdmin() ? ' - admin' : '' ?></span>
                </td>
                <td>
                  <a href="<?php echo 'mailto:' . $user->email ?>"><?php echo h($user->email) ?></a>
                </td>
                <td>
                  <a href="<?php echo url_for('/staff/users/edit.php?id=' . $user->id) ?>" class="edit">
                    Edit
                  </a>
                </td>
                <td>
                  <?php if ($user->isAdmin()): ?>
                    <span> - </span>
                  <?php else: ?>
                    <a href="<?php echo url_for('/staff/users/index.php?id=' . $user->id . '&cmd=delete') ?>" class="delete">
                      Delete
                    </a>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

      </div>
    </div>
    <!-- // Admin Content -->

  </div>


  <?php include SHARED_PATH . '/staff_footer.php'; ?>
</body>

</html>