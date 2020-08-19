<?php
require_once('../../src/initialize.php');

?>
<!DOCTYPE html>
<html lang="en">

<?php
  $page_title = 'Password reset successfully';
  include(SHARED_PATH . '/public_header.php');
?>

  <div class="auth-content">

    <h3><?php echo $page_title ?></h3>
    <br>
    <p>You can now <a href="<?php echo url_for('login.php') ?>" class="link-underlined">login</a>.</p>

  </div>

</body>

</html>