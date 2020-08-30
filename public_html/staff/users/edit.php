<?php
use App\Classes\User;

require_once('../../../src/initialize.php');

require_login();

if (is_post_request()) {

  $id = $_POST['user']['id'] ?? 0;

  $user = User::findById($id);
  
  if (!$user) {
    redirect_to(url_for('index.php'));    
  }

  $user->mergeAttributes($_POST['user']);
  $result = $user->save();

  if ($result === true) {
    $session->message("User '" . $user->username ."' settings was updated.");
    redirect_to(url_for('/staff/users/edit.php?id=' . $user->id));
  }

} else {

  $id = $_GET['id'] ?? 0;
  $user = User::findById($id);

  if (!$user) {
    redirect_to(url_for('index.php'));
  }
}

// CHECK ACCESS
if ($session->isAdmin() || $session->getUserId() == $user->id) {
  // Do nothing, let the rest of the page proceed
} else {
  redirect_to(url_for('index.php'));
}

$page_title = 'Settings';
include SHARED_PATH . '/staff_header.php';
require '../_common-html.php';

?>
<div class="row">
  <aside class="sidebar col-lg-3">
    <?php include SHARED_PATH . '/staff_sidebar.php' ?>
  </aside>

  <main class="main col-lg-9">
    <div class="main-content bg-white">
      <?php echo page_back_button() ?>

      <h2 class="text-center"><?php echo $page_title ?></h2>

      <div class="row justify-content-center h-100">
        <div class="col my-auto">

          <div class="py-5 my-4 rounded bg-white">
            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">

              <div class="px-2 px-sm-4"><?php
                if (!empty($user->errors)) echo display_errors($user->errors);
              ?></div>

              <fieldset class="px-4 px-sm-5 mr-sm-2">

                <?php if ($user->id): ?>
                  <input type="hidden" name="user[id]" value="<?php echo $user->id ?>">
                <?php endif; ?>
                <div class="form-group row mr-sm-0">
                  <label for="username" class="col-sm-4 col-form-label pl-0 pl-sm-3">Username</label>
                  <input class="col-sm-8 form-control" id="username" type="text" name="user[username]" value="<?php echo h($user->username) ?>">
                </div>
                <div class="form-group row mr-sm-0">
                  <label for="email" class="col-sm-4 col-form-label pl-0 pl-sm-3">Email</label>
                  <input class="col-sm-8 form-control" id="email" type="email" name="user[email]" value="<?php echo h($user->email) ?>">
                </div>
                <div class="form-group row mr-sm-0">
                  <label for="password" class="col-sm-4 col-form-label pl-0 pl-sm-3">Password</label>
                  <input class="col-sm-8 form-control" type="password" name="user[password]" value="<?php echo $user->empty_password_field ? '' : $user->password ?>">
                </div>
                <div class="form-group row mr-sm-0">
                  <label for="confirm_password" class="col-sm-4 pl-0 pl-sm-3">Confirm Password</label>
                  <input class="col-sm-8 form-control" type="password" name="user[confirm_password]" value="<?php echo $user->empty_password_field ? '' : $user->confirm_password ?>">
                </div>

                <button type="submit" name="submit_button" class="btn btn-outline-default float-right my-3">Save</button>
              </fieldset>
            </form>
          </div>

        </div>  
      </div>

    </div>
  </main>
</div><!-- row -->

<?php include SHARED_PATH . '/staff_footer.php'; ?>