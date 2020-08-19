<?php
use App\Classes\User;

require_once '../../src/initialize.php';

$user = false;
$password = "";
$confirm_password = "";

if (is_get_request()) {

  if (isset($_GET['token'])) {
    $user_token = $_GET['token'] ?? '';
  
    if ($user_token) {
      $user = User::getByPasswordResetToken($user_token);
    }
  }

} elseif (is_post_request()) {

  $user_token = $_POST['user']['token'] ?? '';

  if ($user_token) {
    $user = User::getByPasswordResetToken($user_token);

    if ($user) {
      if (!$user->resetPassword($_POST['user'])) {
        $errors = $user->errors;
      } else {
        redirect_to(url_for('password/reset_success.php'));
      } 
    }
  }

}

?>
<!DOCTYPE html>
<html lang="en">

<?php
  $page_title = 'Password Reset';
  include(SHARED_PATH . '/public_header.php');
?>

  <div class="auth-content">
    <?php if (!$user): ?>

      <h3><?php echo $page_title ?></h3>
      <br>
      <p>
        Password reset link is invalid or expired, please click 
        <a href="<?php echo url_for('password/forgot.php') ?>">here</a> 
        to request another one.
      </p>

    <?php else: ?>

      <form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
        <h3><?php echo $page_title ?></h3>
        <br>
        <?php
          if (!empty($errors)) echo display_errors($errors);
        ?>
        <input type="hidden" name="user[token]" value="<?= $user_token ?>" />
        <div>
          <label for="inputPassword">Password</label>
          <input type="password" name="user[password]" value="<?php echo $password ?>"  placeholder="Password" class="text-input" required>
        </div>
        <div>
          <label for="inputPassword">Confirm Password</label>
          <input type="password" name="user[confirm_password]" value="<?php echo $confirm_password ?>"  placeholder="Confirm Password" class="text-input" required>
        </div>
        <button type="submit" name="submit_button" class="btn">Reset</button>
      </form>

    <?php endif; ?>
  </div>

</body>

</html>