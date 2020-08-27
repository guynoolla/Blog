<?php
use App\Classes\User;

require_once '../src/initialize.php';

if (is_post_request()) {
  $user = new User($_POST['user']);
  if($user->save()) {
    $session->message('You have been registered, now you can log in!');
    redirect_to(url_for('login.php'));
  }
} else {
  $user = new User();
}

$page_title = 'User Registration';
include SHARED_PATH . '/public_header.php';
?>

<div class="lg-container bg-light-lk">

  <div class="row justify-content-center h-100">
    <div class="col col-md-8 col-lg-6 my-auto">

      <div class="p-4 px-sm-5 py-5 rounded bg-white">
        <form action="register.php" method="post">
          <fieldset>

            <legend class="mb-5 h1">Register</legend>
            <?php
              if (!empty($user->errors)) echo display_errors($user->errors);
            ?>
            <div class="form-group row">
              <label for="username" class="col-sm-4 col-form-label pl-0 pl-sm-3">Username</label>
              <input class="col-sm-8 form-control" id="username" type="text" name="user[username]" value="<?php echo h($user->username) ?>">
            </div>
            <div class="form-group row">
              <label for="email" class="col-sm-4 col-form-label pl-0 pl-sm-3">Email</label>
              <input class="col-sm-8 form-control" id="email" type="email" name="user[email]" value="<?php echo h($user->email) ?>" class="text-input">
            </div>
            <div class="form-group row">
              <label for="password" class="col-sm-4 col-form-label pl-0 pl-sm-3">Password</label>
              <input class="col-sm-8 form-control" type="password" name="user[password]" value="<?php echo $user->empty_password_field ? '' : $user->password ?>" class="text-input">
            </div>
            <div class="form-group row">
              <label for="confirm_password" class="col-sm-4 pl-0 pl-sm-3">Confirm Password</label>
              <input class="col-sm-8 form-control" type="password" name="user[confirm_password]" value="<?php echo $user->empty_password_field ? '' : $user->confirm_password ?>" class="text-input">
            </div>
            <p class="text-center mb-0 font-weight-bold">Or <a href="<?php echo url_for('login.php') ?>">Sign In</a></p>
            <button type="submit" name="submit_button" class="btn btn-outline-default float-right">Register</button>

          </fieldset>
        </form>
      </div>

    </div>
  </div>

</div>

<?php include SHARED_PATH . '/public_footer.php' ?>