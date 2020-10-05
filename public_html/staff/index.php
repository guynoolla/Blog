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

      <h1 class="dashboard-headline mt-0">
        <div class="headline-nav paginateLinksJS">
          Liked Posts
          <a href="" class="chevron chevron--next d-none" style="width:2rem;height:2rem;">
            <!-- <i class="fa fa-chevron-right"></i> -->
            <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="chevron-right" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" class="svg-icon svg-inline--fa fa-chevron-right fa-w-10 fa-3x"><path fill="currentColor" d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z" class=""></path></svg>
          </a>
          <a href="" class="chevron chevron--prev d-none" style="width:2rem;height:2rem;">
            <!-- <i class="fa fa-chevron-left"></i> -->
            <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="chevron-left" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" class="svg-icon svg-inline--fa fa-chevron-left fa-w-10 fa-3x"><path fill="currentColor" d="M34.52 239.03L228.87 44.69c9.37-9.37 24.57-9.37 33.94 0l22.67 22.67c9.36 9.36 9.37 24.52.04 33.9L131.49 256l154.02 154.75c9.34 9.38 9.32 24.54-.04 33.9l-22.67 22.67c-9.37 9.37-24.57 9.37-33.94 0L34.52 272.97c-9.37-9.37-9.37-24.57 0-33.94z" class=""></path></svg>
          </a>
        </div>
      </h1>

      <?php if (!$user->isEmailConfirmed()): ?>
        <p class="text-lk">
          You are logged in <strong><?php echo $user->username ?></strong>.<br>
          <?php echo ($user->user_type == 'admin' ? 'To administrate you should' : 'To add a Post you should') ?>
          <a class="underlined" href="<?php echo url_for('email/confirm_mail.php?email=' . $user->email) ?>" class="link-underlined"><u>confirm</u></a> your email address.
        </p>
      <?php endif; ?>

      <!-- Email Confirmed User -->
      <div class="loadPostsJS">
        <div class="py-4 px-2 mt-1 text-center alert alert-info">
          <p class="h4">You liked no one post yet</p>
        </div>

      </div>
      <div class="paginationJS"></div>

      <div class="loading d-none">
        <div class="spinner-grow" style="width: 2rem; height: 2rem;" role="status">
          <span class="sr-only">Loading...</span>
        </div>
      </div>

    </div>
  </main>
</div>

<?php include SHARED_PATH . '/staff_footer.php'; ?>