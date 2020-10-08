<?php
require_once '../src/initialize.php';

$page_title = '404';
$top_banner_hide = 'topBannerHideJS';
include(SHARED_PATH . '/public_header.php');

?>
<div class="container-md">
  <div class="row">
    
    <main class="main col" role="main">
      <div class="main-content">

        <div class="row pt-5 firstTopPaddingJS">
          <div class="col">
            <h2 class="my-2 d-block text-center"><?php echo $jsonstore->header->siteName ?></h2>
          </div>
        </div>

        <div class="row">
          <div class="col">
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