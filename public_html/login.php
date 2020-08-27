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

if ($session->isLoggedIn()) {
  redirect_to(url_for('index.php'));
}

$page_title = 'User Login';
include(SHARED_PATH . '/public_header.php');

?><div class="lg-container bg-light-lk">

  <div class="row justify-content-center h-100">
    <div class="col col-md-8 col-lg-6 my-auto">

      <div class="p-4 px-sm-5 py-5 rounded bg-white">
        <form action="register.php" method="post">
          <fieldset>

            <legend class="mb-5 h1">Login</legend>
            <?php
              if (!empty($user->errors)) echo display_errors($user->errors);
            ?>
            <div class="form-group row">
              <label for="username" class="col-sm-4 col-form-label pl-1 pl-sm-3">Username</label>
              <input class="col-sm-8 form-control" id="username" type="text" name="user[username]" value="<?php echo h($username) ?>">
            </div>
            <div class="form-group row">
              <label for="password" class="col-sm-4 col-form-label pl-1 pl-sm-3">Password</label>
              <input class="col-sm-8 form-control" type="password" name="user[password]" value="" class="text-input">
            </div>
            <p class="text-center mb-0 font-weight-bold">Or <a href="<?php echo url_for('register.php') ?>">Sign Up</a></p>
            <button type="submit" name="submit_button" class="btn btn-outline-default float-right">Login</button>

          </fieldset>
        </form>
      </div>

    </div>
  </div>

</div>

<?php include SHARED_PATH . '/public_footer.php' ?>