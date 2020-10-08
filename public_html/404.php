<?php
require_once '../src/initialize.php';

$page_title = '404';
$banner_none = true;
include(SHARED_PATH . '/public_header.php');

?><div class="container-xl">
  <div class="row">
    
    <main class="main col" role="main">
      <div class="main-content">

        <div class="row">
          <div class="col" id="#page-top">
            <div class="not-found ard ard--square ard--tall-sm ard--mid-md">
              <div class="not-found-content">
                <div class="error-status"">
                  <h1>404</h1>
                  <p>Page not found</p>
                </div>
              </div>            
            </div>
          </div>
        </div>

      </div> <!--main content-->
    </main> <!-- main -->

  </div>
</div> <!--container-->

<?php include SHARED_PATH . '/public_footer.php' ?>