<?php
use App\Classes\User;

require_once '../src/initialize.php';

if(is_post_request()) {
  $user = new User($_POST['user']);
  if($user->save()) {
    $session->message('You have been registered, now you can log in!');
    redirect_to(url_for('login.php'));
  }
} else {
  $user = new User();
}

?>
<!DOCTYPE html>
<html lang="en">

<?php
  $page_title = 'User Registration';
  include(SHARED_PATH . '/public_header.php');
?>

  <div class="auth-content">
    <form action="register.php" method="post">
      <h3 class="form-title">Register</h3>
      <?php
        if (!empty($user->errors)) echo display_errors($user->errors);
      ?>
      <div>
        <label>Username</label>
        <input type="text" name="user[username]" value="<?php echo h($user->username) ?>" class="text-input">
      </div>
      <div>
        <label>Email</label>
        <input type="email" name="user[email]" value="<?php echo h($user->email) ?>" class="text-input">
      </div>
      <div>
        <label>Password</label>
        <input type="password" name="user[password]" value="<?php echo $user->empty_password_field ? '' : $user->password ?>" class="text-input">
      </div>
      <div>
        <label>Confirm Password</label>
        <input type="password" name="user[confirm_password]" value="<?php echo $user->empty_password_field ? '' : $user->confirm_password ?>" class="text-input">
      </div>
      <div>
        <button type="submit" name="submit_button" class="btn">Register</button>
      </div>
      <p class="auth-nav">Or <a href="login.php">Sign In</a></p>
    </form>
  </div>

</body>

</html>