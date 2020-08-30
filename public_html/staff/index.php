<?php
use App\Classes\Post;
use App\Classes\User;

require_once('../../src/initialize.php');

// Check LoggedIn >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
require_login();
// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Check LoggedIn

$user = User::findById($session->getUserId());
$page_title = ($session->isAdmin() ? 'Admin Posts' : 'User Posts');

include SHARED_PATH . '/staff_header.php';

?>
<div class="row">
  <aside class="sidebar col-lg-3">
    <?php include SHARED_PATH . '/staff_sidebar.php' ?>
  </aside>

  <main class="main col-lg-9">
    <div class="main-content">

      <h2 class="mt-0">Logged In</h2>

      <?php if (!$user->isEmailConfirmed()): ?>
        <p class="text-lk">
          Welcome, <strong><?php echo $user->username ?></strong>! Now you are logged in and can put likes to posts.<br>
          You, also will be able to add your own posts if you confirm your email address.<br>
          Please, <a class="underlined" href="<?php echo url_for('email/confirm_mail.php?email=' . $user->email) ?>" class="link-underlined"><u>confirm</u></a> you email address if you want to have author rights.
        </p>

      <?php else: ?>

        <!-- Email Confirmed User -->

      <?php endif; ?>

    </div>
  </main>
</div>

<?php include SHARED_PATH . '/staff_footer.php'; ?>