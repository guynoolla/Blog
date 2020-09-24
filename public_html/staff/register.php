<?php
use App\Classes\User;
use App\Classes\Like;

require_once '../../src/initialize.php';

if (is_post_request()) {
  $user = new User($_POST['user']);

  if (strtoupper($_POST['captcha']) == strtoupper($_SESSION['captcha']['code'])) {
    if ($user->save()) {
      $session->message('You have been registered, now you can log in!');
      redirect_to(url_for('staff/login.php'));
    }
  } else {
    $session->store([
      'reg_captcha_err' => 'Captcha validation failed, try again.',
      'reg_user' => serialize($user),
    ]);
    redirect_to(url_for('staff/register.php'));
  }

} else {

  include("../simple-php-captcha.php");
  $_SESSION['captcha'] = simple_php_captcha();
  $captcha_err = "";
  $user = false;
  
  if ($session->store_of('reg_captcha_err')) {
    $captcha_err = $session->store_of('reg_captcha_err', false);
    $user = unserialize($session->store_of('reg_user', false));
  }

  $user = !$user ? new User() : $user;
}

$page_title = 'User Registration';
include SHARED_PATH . '/public_header.php';

?>
<div class="container-fluid bg-other-lk bg-other-lk--md">

  <div class="row justify-content-center h-100">
    <div class="col col-md-10 col-lg-8 col-xl-6 my-auto">

      <div class="py-5 my-5 rounded bg-white px-0 px-sm-4 px-lg-5">
        <form id="registerForm" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">

          <div class="row ml-0"><h1>Register</h1></div>
          <div class="row"><div class="col"><?php
            if (!empty($user->errors)) echo display_errors($user->errors);
          ?></div></div>

          <div class="form-group row mb-0 mx-0">
            <label for="username" class="col-sm-4 col-form-label pl-0">Username</label>
            <input class="col-sm-8 form-control" id="username" type="text" name="user[username]" value="<?php echo h($user->username) ?>">
            <span class="offset-sm-4 col-sm-8 text-danger field-validation-error"></span>
          </div>
          <div class="form-group row mb-0 mx-0">
            <label for="email" class="col-sm-4 col-form-label pl-0">Email</label>
            <input class="col-sm-8 form-control" id="email" type="email" name="user[email]" value="<?php echo h($user->email) ?>">
            <span class="offset-sm-4 col-sm-8 text-danger field-validation-error"></span>
          </div>
          <div class="form-group row mb-0 mx-0">
            <label for="password" class="col-sm-4 col-form-label pl-0">Password</label>
            <input class="col-sm-8 form-control" id="password" type="password" name="user[password]" value="<?php echo $user->empty_password_field ? '' : $user->password ?>">
            <span class="offset-sm-4 col-sm-8 text-danger field-validation-error"></span>
          </div>
          <div class="form-group row mb-0 mx-0">
            <label for="confirm_password" class="col-sm-4 pl-0">Confirm Password</label>
            <input class="col-sm-8 form-control" id="confirm_password" type="password" name="user[confirm_password]" value="<?php echo $user->empty_password_field ? '' : $user->confirm_password ?>">
            <span class="offset-sm-4 col-sm-8 text-danger field-validation-error"></span>
          </div>

          <div class="form-group row my-0 mx-0">
            <label for="confirm_password" class="col-sm-4 pl-0">Captcha Code</label>
            <div class="form-group-captcha col-sm-8 px-0 d-flex align-items-start justify-content-start bg-light clearfix">
              <img src="<?php echo $_SESSION['captcha']['image_src'] ?>" style="z-index:500">
              <input type="text" name="captcha" id="captcha" class="captcha-field align-self-end bg-light border-0" placeholder="code">
              <button type="submit" name="submit_button" class="btn btn-outline-default ml-auto rounded-0">
                <span class="spinner-grow spinner-grow-sm d-none" role="status" aria-hidden="true"></span>
                Register
              </button>
            </div>
            <span class="offset-sm-4 col-sm-8 text-danger field-validation-error"><?php echo $captcha_err ?></span>
          </div>

          <p class="text-center font-weight-bold">Or <a href="<?php echo url_for('staff/login.php') ?>">Log In</a></p>
          <div class="response response--shade"></div>

        </form>
      </div>

    </div>
  </div>

</div>

<?php include SHARED_PATH . '/public_footer.php' ?>