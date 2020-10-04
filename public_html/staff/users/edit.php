<?php
use App\Classes\User;
use App\Classes\File;

require_once('../../../src/initialize.php');

require_login();

if (is_post_request()) {

  $id = $_POST['user']['id'] ?? 0;
  $user = User::findById($id);

  if (!$user) redirect_to(url_for('index.php'));

  $image = new File($_FILES['about_image']);
  $user->fileInstance($image);

  $user->mergeAttributes($_POST['user']);

  if ($user->save()) {
    $session->message("User '" . $user->username ."' settings was updated.");
    if ($user->email != $session->userEmail()) {
      $session->emailFalse();
      redirect_to(url_for('/staff/index.php'));
    } else {
      redirect_to(url_for('/staff/users/edit.php?id=' . $user->id));
    }
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

?>
<div class="row">
  <aside class="sidebar col-lg-3">
    <?php include SHARED_PATH . '/staff_sidebar.php' ?>
  </aside>

  <main class="main col-lg-9 bg-light-lk bg-light-lk--md">
    <div class="main-content bg-white">

      <h1 class="dashboard-headline mb-4">
        <?php echo $page_title ?>
        <div class="back-btn-pos"><?php echo page_back_button() ?></div>
      </h1>

      <div class="row justify-content-center h-100">
        <div class="col col-md-10">

          <div class="py-2 my-4 rounded bg-white px-0 px-sm-4 px-lg-5">
            <form id="userEditForm" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">

              <div class="row"><div class="col"><?php
                if (!empty($user->errors)) echo display_errors($user->errors);
              ?></div></div>

              <?php if ($user->id): ?>
                <input type="hidden" name="user[id]" value="<?php echo $user->id ?>">
              <?php endif; ?>
              <div class="form-group row mb-0 mx-0">
                <label for="username" class="col-sm-4 col-form-label pl-0">Username</label>
                <input class="col-sm-8 form-control" id="username" type="text" name="user[username]" value="<?php echo h($user->username) ?>" <?php if ($session->getUserId() != $user->id) echo 'disabled' ?>>
                <span class="offset-sm-4 col-sm-8 text-danger field-validation-error"></span>
              </div>
              <div class="form-group row mb-0 mx-0">
                <label for="email" class="col-sm-4 col-form-label pl-0">Email</label>
                <input class="col-sm-8 form-control" id="email" type="email" name="user[email]" value="<?php echo h($user->email) ?>" <?php if ($session->getUserId() != $user->id) echo 'disabled' ?>>
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

              <?php if ($session->isAuthor()): ?>

                <hr class="mt-4">
                <h3 class="text-center">About author</h3>
                <div class="form-group row mb-4 mx-0">
                  <label for="about_image" class="col-sm-4 pl-0">Your Image</label>
                  <div class="col-sm-4 pl-0">
                    <input class="form-control-file mb-3 pl-0" type="file" name="about_image" id="about_image">
                    <?php if ($session->isAdmin()): ?>
                      <h5 class="my-0"><?php echo $user->about_image ?></h5>
                    <?php endif; ?>
                  </div>
                  <div class="col-sm-4">
                    <img class="<?php echo ((isset($user->about_image)) ? 'd-block' : 'd-none')
                      ?> rounded-circle float-left" style="width:100%;height:auto;" id="aboutImage"
                      src="<?php echo url_for('/assets/images/' . $user->about_image)
                    ?>">
                  </div>
                </div>
                <div class="form-group row mb-0 mx-0">
                  <label for="aboutText" class="col-sm-4 pl-0">About Text</label>
                  <textarea class="col-sm-8 form-control" id="about_text" name="user[about][about_text]" value="" id="aboutText" rows="5"><?php echo $user->about_text ?></textarea>
                  <span class="offset-sm-4 col-sm-8 text-danger field-validation-error"></span>
                </div>
                <div class="form-group row custom-control custom-switch mt-3 bm-0 mx-0">
                  <label class="col-sm-4 pl-0 pl-sm-3"></label>
                  <input name="user[about][about_appear]" type="checkbox" class="col-sm-8 custom-control-input" id="appearSwitch"<?php echo ($user->about_appear == '1' ? ' checked' : '') ?>>
                  <label class="custom-control-label" for="appearSwitch">Appear in post page sidebar</label>
                </div>

              <?php endif; ?>

              <button type="submit" name="submit_button" class="btn btn-outline-default float-right my-3">Save</button>

            </form>
          </div>

        </div>  
      </div>

    </div>
  </main>
</div><!-- row -->

<?php include SHARED_PATH . '/staff_footer.php'; ?>