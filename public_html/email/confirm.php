<?php
use App\Classes\User;

require_once '../../src/initialize.php';

if (isset($_GET['token'])) {

  $user_token = $_GET['token'] ?? '';

  if ($user_token) {
    $user = User::getByEmailConfirmToken($user_token);

    if ($user) {
      if ($user->confirmEmail()) {
        $session->message('Thank you, your email was confirmed! Log in, please!');
        redirect_to(url_for('staff/logout.php'));
      }
    }
  }

}

$page_title = 'Email confirmation';
include(SHARED_PATH . '/public_header.php');

?>
<div class="container-fluid">

  <div class="row justify-content-center h-100">
    <div class="col col-md-10 col-lg-8 col-xl-6 my-auto">

      <div class="py-3 pb-5 my-4 rounded bg-white px-0 px-sm-4 px-lg-5">

        <?php if (!isset($user) || !$user): ?>
          <h2><?php echo $page_title ?></h2>
          <p>
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