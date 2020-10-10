<?php
require_once('../../src/initialize.php');

$page_title = 'Password reset successfully';
include(SHARED_PATH . '/public_header.php');

?>
<div class="container-fluid">

  <div class="row justify-content-center h-100">
    <div class="col col-md-10 col-lg-8 col-xl-6 my-auto">

      <div class="pt-3 pb-5 my-5 rounded bg-white px-0 px-sm-4 px-lg-5">

        <h1><?php echo $page_title ?></h1>
        <p>You can now <a class="font-weight-bold" href="<?php echo url_for('staff/login.php') ?>">login</a>.</p>

      </div>
    </div>
  </div>

</div>

<?php include SHARED_PATH . '/public_footer.php' ?>