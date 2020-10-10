<?php
require_once '../src/initialize.php';

$page_title = $jsonstore->contactForm->title;
include(SHARED_PATH . '/public_header.php');

include("./simple-php-captcha.php");
$_SESSION['captcha'] = simple_php_captcha();

$email = "";
$message = "";
$captcha_err = "";

if ($session->store_of('fp_captcha_err')) {
  $email = $session->store_of('fp_field_email', false);
  $message = $session->store_of('fp_field_message', false);
  $captcha_err = $session->store_of('fp_captcha_err', false);
}

if ($jsonstore->contactForm->placeholder) {
  $email_ph = 'Your ' . strtolower($jsonstore->contactForm->emailLabel);
  $message_ph = 'Your ' . strtolower($jsonstore->contactForm->messageLabel);
}

?>
<div class="container-fluid bg-other-lk--md">

  <div class="row justify-content-center h-100">
    <div class="col col-md-10 col-lg-8 col-xl-6 my-auto">

      <div class="pt-3 pb-5 my-5 rounded bg-white px-0 px-sm-4 px-lg-5">
        <form id="contactForm" action="<?php echo url_for('form_post.php') ?>" method="post" name="contactForm">

          <div class="row ml-0"><h1 class="mb-5"><?php echo $page_title ?></h1></div>
          <div class="row"><div class="col"><?php
            if (!empty($user->errors)) echo display_errors($user->errors);
          ?></div></div>

          <div class="form-group row mb-0 mx-0">
            <label for="email" class="col-sm-4 col-form-label pl-0">
              <?php echo $jsonstore->contactForm->emailLabel ?>
            </label>
            <input class="col-sm-8 form-control" id="email" type="email" name="email" <?php
              echo ($email_ph ? 'placeholder="' . $email_ph . '"' : '')
            ?>>
            <span class="offset-sm-4 col-sm-8 text-danger field-validation-error"></span>
          </div>
          <div class="form-group row mb-0 mx-0">
            <label for="message" class="col-sm-4 col-form-label pl-0">
              <?php echo $jsonstore->contactForm->messageLabel ?>
            </label>
            <textarea class="col-sm-8 form-control" id="message" name="message" rows="6" <?php
              echo ($message_ph ? 'placeholder="'.$message_ph.'"' : '')
            ?>><?php echo $message ?></textarea>
            <span class="offset-sm-4 col-sm-8 text-danger field-validation-error"></span>
          </div>

          <div class="form-group row my-0 mx-0">
            <label for="confirm_password" class="col-sm-4 pl-0"><?php echo $jsonstore->captcha->label ?></label>
            <div class="form-group-captcha col-sm-8 px-0 d-flex align-items-start justify-content-start bg-light clearfix">
              <img src="<?php echo $_SESSION['captcha']['image_src'] ?>" style="z-index:500">
              <input type="text" name="captcha" id="captcha" class="captcha-field align-self-end bg-light border-0" placeholder="<?php echo $jsonstore->captcha->placeholder ?>">
              <button type="submit" name="submit_button" class="btn btn-outline-default ml-auto rounded-0">
                <span class="spinner-grow spinner-grow-sm d-none" role="status" aria-hidden="true"></span>
                <?php echo $jsonstore->contactForm->buttonText ?>
              </button>
            </div>
            <span class="offset-sm-4 col-sm-8 text-danger field-validation-error"><?php echo $captcha_err ?></span>
          </div>

          <div class="response response--shade mt-4"></div>
        </form>
      </div>

    </div>
  </div>

</div>

<?php include SHARED_PATH . '/public_footer.php' ?>