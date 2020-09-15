<?php $loggedIn = $session->isLoggedIn() ?>

<li class="position-relative nav-item<?php $loggedIn ? ' dropdown' : '' ?>">
  <?php if (!$loggedIn): ?>
    <a class="nav-link" href="<?php echo url_for('login.php') ?>">Login</a>
  <?php else: ?>
    <a class="username-lk nav-link dropdown-toggle" href="" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      <?php echo $session->username ?>
    </a>
    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
      <?php if (!url_contain('staff')): ?>
        <a class="dropdown-item" href="<?php echo url_for('staff/index.php') ?>">Dashboard</a>
      <?php else: ?>
        <a class="dropdown-item" href="<?php echo url_for('/') ?>">Home</a>
      <?php endif; ?>
      <a class="dropdown-item text-muted" href="<?php echo url_for('staff/users/edit.php?id=' . $session->getUserId()) ?>">User Settings</a>
      <div class="dropdown-divider"></div>
      <a class="dropdown-item text-muted" href="<?php echo url_for('logout.php') ?>">Logout</a>
    </div>
  <?php endif; ?>
</li>
