<?php
use App\Classes\User;

require_once '../../src/initialize.php';

if (isset($_GET['token'])) {

  $user_token = $_GET['token'] ?? '';

  if ($user_token) {
    $user = User::getByEmailConfirmToken($user_token);

    if ($user) {
      if ($user->confirmEmail()) {
        $session->message('Thank you, your email confirmed! Please log in now again to access author rights!');
        redirect_to(url_for('logout.php'));
      }
    }
  }

}

$page_title = 'Email confirmation';
include(SHARED_PATH . '/public_header.php');

?>
<div class="container-fluid bg-light-lk">

  <div class="row justify-content-center h-100">
    <div class="col col-md-8 col-lg-6 my-auto">

      <div class="p-4 px-sm-5 py-5 my-4 rounded bg-white">

        <?php if (!$user): ?>
          <h2><?php echo $page_title ?></h2>
          <p class="h4">
            Email confirmation link is invalid or expired, please click 
            <a class="font-weight-bold" href="<?php echo url_for('password/forgot.php') ?>">here</a> 
            to request another one.
          </p>
        <?php endif; ?>

      </div>
    </div>
  </div>

</div>

<?php include SHARED_PATH . '/public_footer.php' ?>