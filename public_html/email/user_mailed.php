<?php
require_once '../../src/initialize.php';

?>
<!DOCTYPE html>
<html lang="en">

<?php
  $page_title = 'Check your email';
  include(SHARED_PATH . '/public_header.php');

?>
<div class="container-fluid bg-other-lk">
  <div class="row justify-content-center h-100">
    <div class="col col-md-8 col-lg-6 my-auto">

      <div class="px-3 px-sm-5 py-3 pb-5 my-4 rounded bg-white">
        <h2><?php echo $page_title ?></h2>
        <p class="h4 mb-5">Confirmation email was sent. Please check you email.</p>
      </div>

    </div>
  </div>
</div>

<?php include SHARED_PATH . '/public_footer.php' ?>