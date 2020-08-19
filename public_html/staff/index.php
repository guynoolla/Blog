<?php
use App\Classes\Post;
use App\Classes\User;

require_once('../../src/initialize.php');

// Check LoggedIn >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
require_login();
// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Check LoggedIn

$user = User::findById($session->getUserId());

?>
<!DOCTYPE html>
<html lang="en">

  <?php
    $page_title = ($session->isAdmin() ? 'Admin Posts' : 'User Posts');
    include SHARED_PATH . '/staff_header.php';
  ?>

  <div class="admin-wrapper clearfix">

    <?php include SHARED_PATH . '/staff_sidebar.php'; ?>

    <div class="admin-content clearfix">
      <div class="">

        <h2 style="margin-left:0">Logged In</h2>

        <?php echo display_session_message(); ?>

        <?php if (!$user->isEmailConfirmed()): ?>
          <p class="lead">
            Welcome, <strong><?php echo $user->username ?></strong>! Now you are logged in and can like posts.<br>
            You, also will be able to add posts if you confirm your email address.<br>
            Please, <a href="<?php echo url_for('email/confirm_mail.php?email=' . $user->email) ?>" class="link-underlined">confirm</a> you email address if you want to have author capability.
          </p>

        <?php else: ?>

          <!-- Email Confirmed User -->

        <?php endif; ?>

      </div>
    </div>

  </div>

  <?php include SHARED_PATH . '/staff_footer.php'; ?>
</body>

</html>