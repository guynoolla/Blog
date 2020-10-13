<?php

require_once('../../../src/initialize.php');

// Check Admin >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
if (!$session->isAdmin()) redirect_to(url_for('index.php'));
// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Check Admin


$page_title = 'Site';
include SHARED_PATH . '/staff_header.php'

?>
<div class="row">
  <aside class="sidebar col col-lg-3 pt-1">
    <?php include SHARED_PATH . '/staff_sidebar.php' ?>
  </aside>
  
  <div class="main col col-lg-9 bg-light-lk bg-light-lk--md">
    <div class="main-content bg-white">

      <h1 class="dashboard-headline">
        <?php echo $page_title ?>
        <div class="back-btn-pos"><?php echo page_back_button() ?></div>
      </h1>

      <div class="row justify-content-left h-100">
        <div class="col col-md-12">

          <div class="py-2 my-1 rounded bg-white px-0 px-sm-4 px-lg-5">
            <form id="jsonEditForm" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">

              <div class="form-alert"></div>

              <div class="form-group mb-0 mx-0">
                <label for="json" class="col-form-label pl-0">Site Settings</label>
                <textarea class="form-control" name="json" value="" id="json" rows="12"><?php
                ?></textarea>
                <span class="errsum-json text-danger field-validation-error"></span>
              </div>

              <button type="submit" name="submit" class="btn btn-primary float-right">Save</button>
              <button type="submit" name="reload" class="btn btn-success float-right mr-2">Reload</button>
            </form>
          </div>

        </div>
      </div>
      
    </div>
  </div>

</div>

<?php include SHARED_PATH . '/staff_footer.php'; ?>