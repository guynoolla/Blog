<?php
require_once '../src/initialize.php';

$errors = [];
$username = '';
$password = '';

if(is_post_request()) {

  $username = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';

  if(is_blank($username)) {
    $errors[] = 'Username cannot be blank.';
  }
  if(is_blank($password)) {
    $errors[] = 'Password cannot be blank.';
  }

  $user = App\Classes\User::findByUsername($username);
  $user = !$user ? App\Classes\User::findByEmail($username) : $user;

  if (empty($errors)) {
    if ($user && $user->verifyPassword($password)) {
      $session->login($user);
      $session->message('You are successfully logged in!');
      redirect_to(url_for('staff/index.php'));
    } else {
      $errors[] = 'Log in was unsuccessful.';
    }
  }
}

if ($session->isLoggedIn()) redirect_to(url_for('index.php'));

?>
<!DOCTYPE html>
<html lang="en">

<?php
  $page_title = 'User Login';
  include(SHARED_PATH . '/public_header.php');
?>

  <div class="auth-content">
    <form action="login.php" method="post">
      <h3 class="form-title">Login</h3>
      <?php
        if(!empty($errors)) echo display_errors($errors, '');
      ?>
      <div>
        <label>Username or Email</label>
        <input type="text" name="username" value="" class="text-input">
      </div>
      <div>
        <label>Password</label>
        <input type="password" name="password" value="" class="text-input">
        <a href="<?php echo url_for('password/forgot.php') ?>" class="forgot-link">Forgot password?</a>
      </div>
      <div>
        <button type="submit" name="submit_button" class="btn">Login</button>
      </div>
      <p class="auth-nav">Or <a href="register.php">Sign Up</a></p>
    </form>
  </div>

</body>

</html>