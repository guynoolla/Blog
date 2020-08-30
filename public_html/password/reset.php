<?php
use App\Classes\User;

require_once '../../src/initialize.php';

$user = false;
$password = "";
$confirm_password = "";
$errors = [];

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

$page_title = 'Reset';
include(SHARED_PATH . '/public_header.php');

?>
<div class="container-fluid bg-light-lk">

<div class="row justify-content-center h-100">
  <div class="col col-md-8 col-lg-6 my-auto">
    <div class="py-5 my-4 rounded bg-white">

      <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
        <fieldset class="px-4 px-sm-5 mr-sm-1">

          <legend class="mb-5 h1"><?php echo $page_title ?></legend>
          <?php
            if (!empty($errors)) echo display_errors($errors);
          ?>
          <input type="hidden" name="user[token]" value="<?php echo $user_token ?>" />
          
          <div class="form-group row mr-sm-0">
            <label for="password" class="col-sm-4 col-form-label">Password</label>
            <input class="col-sm-8 form-control" type="password" name="user[password]" value="<?php echo $password ?>">
          </div>
          <div class="form-group row mr-sm-0">
            <label for="confirm_password" class="col-sm-4 col-form-label">Confirm Password</label>
            <input class="col-sm-8 form-control" type="password" name="user[confirm_password]" value="<?php echo $confirm_password ?>">
          </div>

          <button type="submit" name="submit_button" class="btn btn-outline-default float-right my-4">Reset</button>
        </fieldset>
      </form>

    </div>
  </div>
</div>

</div>

<?php include SHARED_PATH . '/public_footer.php' ?>