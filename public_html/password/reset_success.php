<?php
require_once('../../src/initialize.php');

$page_title = 'Password reset successfully';
include(SHARED_PATH . '/public_header.php');

?>
<div class="container-fluid bg-other-lk">

  <div class="row justify-content-center h-100">
    <div class="col col-md-8 col-lg-6 my-auto">

      <div class="p-4 px-sm-5 py-5 my-4 rounded bg-white">

        <h2><?php echo $page_title ?></h2>
        <p class="h4">You can now <a class="font-weight-bold" href="<?php echo url_for('login.php') ?>">login</a>.</p>

      </div>
    </div>
  </div>

</div>

<?php include SHARED_PATH . '/public_footer.php' ?>