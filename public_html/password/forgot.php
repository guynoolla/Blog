<?php
use App\Classes\User;
use App\Contracts\Mailer;

require_once '../../src/initialize.php';

$errors = [];

// User must not be logged in.
if ($session->isLoggedIn()) redirect_to(url_for('index.php'));

if (is_post_request()) {
  $email = $_POST['email'] ?? "";

  $reset_token = User::createPasswordResetToken($email);

  if ($reset_token) {
    $url = get_base_url() . "/password/reset.php?token=" . $reset_token;
  
    $text = "Please click on the following URL to reset your password: " . $url;
    $html = "<h1>Password reset</h1>";
    $html .= "<p>Please, <a href=\"" . $url . "\">";
    $html .= "click here to reset your password.";
    $html .= "</a></p>";
  
    try {
      $mailer = new Mailer;
      $mailer->send($email, 'Password reset', $text, $html);

    } catch(Exception $e) {
      exit('Fail to mail...');
    }
   
    redirect_to(url_for('password/forgot.php?r=reset'));
  
  } else {
    $errros[] = 'Email is wrong.';
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

    <?php if (isset($_GET['r']) && $_GET['r'] == 'reset'): ?>
      <h3><?php echo $page_title ?></h3>
      <br>
      <p>Please check your email.</p>

    <?php else: ?>
      <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
        <h3 class="form-title"><?php echo $page_title ?></h3>
        <br>
        <?php
          if(!empty($errors)) echo display_errors($errors, '');
        ?>
        <div>
          <label>Email Address</label>
          <input type="email" name="email" value="" class="text-input">
        </div>
        <div>
          <button type="submit" name="submit_button" class="btn">Reset</button>
        </div>
      </form>

    <?php endif; ?>

  </div>

</body>

</html>