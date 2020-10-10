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
<div class="container-fluid">

<div class="row justify-content-center h-100">
  <div class="col col-md-10 col-lg-8 col-xl-6 my-auto">

    <div class="pt-3 pb-5 my-5 rounded bg-white px-0 px-sm-4 px-lg-5">
      <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">

        <div class="row ml-0"><h1><?php echo $page_title ?></h1></div>
        <div class="row"><div class="col">
          <?php if (!empty($errors)) echo display_errors($errors); ?>
        </div></div>

        <input type="hidden" name="user[token]" value="<?php echo $user_token ?>" />
        <div class="form-group row mx-0">
          <label for="password" class="col-sm-4 col-form-label ml-0">Password</label>
          <input class="col-sm-8 form-control" type="password" name="user[password]" value="<?php echo $password ?>">
        </div>
        <div class="form-group row mx-0">
          <label for="confirm_password" class="col-sm-4 col-form-label ml-0">Confirm Password</label>
          <input class="col-sm-8 form-control" type="password" name="user[confirm_password]" value="<?php echo $confirm_password ?>">
        </div>

        <button type="submit" name="submit_button" class="btn btn-outline-default float-right my-3">Reset</button>
        <div class="clearfix"></div>
      </form>
    </div>

  </div>
</div>

</div>

<?php include SHARED_PATH . '/public_footer.php' ?>