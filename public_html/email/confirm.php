<?php
use App\Classes\User;

require_once '../../src/initialize.php';

if (isset($_GET['token'])) {

  $user_token = $_GET['token'] ?? '';

  if ($user_token) {
    $user = User::getByEmailConfirmToken($user_token);

    if ($user) {
      if ($user->confirmEmail()) {
        $session->message('Thank you, your email confirmed. Now you can add a Post!');
        redirect_to(url_for('staff/index.php'));
      }
    }
  }

}

?>
<!DOCTYPE html>
<html lang="en">

<?php
  $page_title = 'Email confirmation';
  include(SHARED_PATH . '/public_header.php');
?>

  <div class="auth-content">
    <?php if (!$user): ?>

      <h3><?php echo $page_title ?></h3>
      <br>
      <p>
        Email confirmation link is invalid or expired, please click 
        <a href="<?php echo url_for('password/forgot.php') ?>">here</a> 
        to request another one.
      </p>

    <?php endif; ?>
  </div>

</body>

</html>