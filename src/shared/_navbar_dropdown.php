<?php $loggedIn = $session->isLoggedIn() ?>

<li class="position-relative nav-item<?php $loggedIn ? ' dropdown' : '' ?>">
  <?php if (!$loggedIn): ?>
    <!-- <a class="nav-link" href="<php echo url_for('admin/login.php') ?>">Login</a> -->
  <?php else: ?>
    <a class="username-lk nav-link<?php echo ($header_type == 'dashboard' ? ' nav-link--bg' : '' ) ?> dropdown-toggle" href="" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      <?php echo $session->username() ?>
    </a>
    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
      <?php if (!url_contain('/staff/')): ?>
        <a class="dropdown-item" href="<?php echo url_for('staff/index.php') ?>">Dashboard</a>
      <?php else: ?>
        <a class="dropdown-item" href="<?php echo url_for('/') ?>">Home</a>
      <?php endif; ?>
      <a class="dropdown-item text-muted" href="<?php echo url_for('staff/users/edit.php?id=' . $session->getUserId()) ?>">User Settings</a>
      <?php if ($session->isAdmin()): ?>
        <a class="dropdown-item text-muted" href="<?php echo url_for('staff/site/edit.php') ?>">Site Settings</a>
      <?php endif; ?>
      <div class="dropdown-divider"></div>
        <a class="dropdown-item text-muted" href="<?php echo url_for('staff/logout.php') ?>">Logout</a>
    </div>
  <?php endif; ?>
</li>
