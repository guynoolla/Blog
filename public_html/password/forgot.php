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

$page_title = 'Password Reset';
include(SHARED_PATH . '/public_header.php');

?>
<div class="container-fluid bg-light-lk">

  <div class="row justify-content-center h-100">
    <div class="col col-md-8 col-lg-6 my-auto">

      <div class="py-5 my-4 rounded bg-white">
      
      <?php if (isset($_GET['r']) && $_GET['r'] == 'reset'): ?>
      
        <p class="mb-5 h1"><?php echo $page_title ?></p>
        <p class="h4">Please check your email.</p>

      <?php else: ?>

        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
          <fieldset class="px-4 px-sm-5 mr-sm-1">
            
            <legend class="mb-5 h1"><?php echo $page_title ?></legend>
            <?php
              if (!empty($user->errors)) echo display_errors($user->errors);
            ?>
            <div class="form-group row mr-sm-0">
              <label for="email" class="col-sm-4 col-form-label pl-0 pl-sm-3">Email</label>
              <input class="col-sm-8 form-control" id="email" type="email" name="email" value="">
           </div>

            <button type="submit" name="submit_button" class="btn btn-outline-default float-right my-4">Reset</button>
          </fieldset>
        </form>

      <?php endif; ?>

      </div>

    </div>
  </div>

</div>

<?php include SHARED_PATH . '/public_footer.php' ?>