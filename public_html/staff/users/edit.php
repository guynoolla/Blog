<?php
use App\Classes\User;

require_once('../../../src/initialize.php');

require_login();

if (is_post_request()) {

  $id = $_POST['user']['id'] ?? 0;

  $user = User::findById($id);
  
  if (!$user) {
    redirect_to(url_for('index.php'));    
  }

  $user->mergeAttributes($_POST['user']);
  $result = $user->save();

  if ($result === true) {
    $session->message("User '" . $user->username ."' settings was updated.");
    redirect_to(url_for('/staff/users/edit.php?id=' . $user->id));
  }

} else {

  $id = $_GET['id'] ?? 0;
  $user = User::findById($id);

  if (!$user) {
    redirect_to(url_for('index.php'));
  }
}

// CHECK ACCESS
if ($session->isAdmin() || $session->getUserId() == $user->id) {
  // Do nothing, let the rest of the page proceed
} else {
  redirect_to(url_for('index.php'));
}

?>
<!DOCTYPE html>
<html lang="en">

  <?php
    $page_title = 'Edit User';
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
        <h2 style="text-align: center;">
          <?php echo $user->isAdmin() ? 'Admin'  : 'User' ?> Settings
        </h2>

        <?php echo display_session_message() ?>

        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">

          <?php echo display_errors($user->errors) ?>
          
          <?php if ($user->id): ?>
            <input type="hidden" name="user[id]" value="<?php echo $user->id ?>">
          <?php endif; ?>

          <?php if ($user->isAdmin()): ?>
            <div class="input-group">
              <label>Username</label>
              <input type="username" name="user[username]" value="<?php echo $user->username ?>" class="text-input">
            </div>
          <?php else: ?>
            <dl>
              <dt>Username </dt>
              <dd style="font-weight:bold;font-size:1.2em;"><?php echo $user->username ?></dd>
            </dl>
          <?php endif; ?>

          <div class="input-group">
            <label>Email</label>
            <input type="email" name="user[email]" value="<?php echo $user->email ?>" class="text-input">
          </div>
          <div class="input-group">
            <label>Password</label>
            <input type="password" name="user[password]" value="<?php echo $user->password ?>" class="text-input">
          </div>
          <div class="input-group">
            <label>Confirm Password</label>
            <input type="password" name="user[confirm_password]" value="<?php echo $user->password ?>" class="text-input">
          </div>
          <div class="input-group">
            <button type="submit" name="submit_button" class="btn">Save User</button>
          </div>
        </form>

      </div>
    </div>
    <!-- // Admin Content -->

  </div>


  <?php
    include SHARED_PATH . '/staff_footer.php';
  ?>
</body>

</html>