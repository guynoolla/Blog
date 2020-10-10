<?php
require_once '../../src/initialize.php';

?>
<!DOCTYPE html>
<html lang="en">

<?php
  $page_title = 'Check your email';
  include(SHARED_PATH . '/public_header.php');

?>
<div class="container-fluid">
  <div class="row justify-content-center h-100">
    <div class="col col-md-10 col-lg-8 col-xl-6 my-auto">

      <div class="py-3 pb-5 my-4 rounded bg-white px-0 px-sm-4 px-lg-5">
        <h2><?php echo $page_title ?></h2>
        <p>Confirmation email was sent. Please check your email.</p>
      </div>

    </div>
  </div>
</div>

<?php include SHARED_PATH . '/public_footer.php' ?>