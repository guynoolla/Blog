<?php
require_once '../../src/initialize.php';

$username = '';
$password = '';
$errors = [];

if (is_post_request()) {

  $username = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';

  if (is_blank($username)) {
    $errors[] = 'Username cannot be blank.';
  }
  if (is_blank($password)) {
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

if ($session->isLoggedIn()) {
  redirect_to(url_for('index.php'));
}

$page_title = 'User Login';
include(SHARED_PATH . '/public_header.php');

?>
<div class="container-fluid bg-other-lk">

  <div class="row justify-content-center h-100">
    <div class="col col-md-8 col-lg-6 my-auto">

      <div class="py-5 my-4 rounded bg-white">

        <div class="px-2 px-sm-4"><?php
          if (!empty($errors)) echo display_errors($errors);
        ?></div>

        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
          <fieldset class="px-4 px-sm-5 mr-sm-2">

            <legend class="mb-5 h1">Login</legend>

            <div class="form-group row mr-sm-0">
              <label for="username" class="col-sm-4 col-form-label pl-1 pl-sm-3">Username or Email</label>
              <input class="col-sm-8 form-control" id="username" type="text" name="username" value="">
            </div>
            <div class="form-group row mr-sm-0">
              <label for="password" class="col-sm-4 col-form-label pl-1 pl-sm-3">Password</label>
              <input class="col-sm-8 form-control" type="password" name="password" value="">
              <small class="small-nicer-lk ml-auto mt-1"><a href="<?php echo url_for('password/forgot.php') ?>">forgot password?</a></small>
            </div>
            <p class="text-center font-weight-bold">Or <a href="<?php echo url_for('staff/register.php') ?>">Sign Up</a></p>
            <button type="submit" name="submit_button" class="btn btn-outline-default float-right">Login</button>

          </fieldset>
        </form>
      </div>

    </div>
  </div>

</div>

<?php include SHARED_PATH . '/public_footer.php' ?>