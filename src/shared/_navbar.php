<!-- header -->
<header class="clearfix">
  <div class="logo">
    <img id="logoImg" src="/assets/styles/img/bumblebee.jpg" alt="Bumblebee" width="50" height="50">
    <a href="<?php echo url_for('/') ?>">
      <h1 class="logo-text" style="padding-top:5px"><span>Multi</span>Blog</h1>
    </a>
  </div>
  <div class="fa fa-reorder menu-toggle"></div>
  <nav>
    <ul>
      <li><a href="<?php echo url_for('/') ?>">Home</a></li>
      <li>
        <a href="#" class="userinfo">
          <i class="fa fa-user"></i>
            <strong>
              <?php echo ($session->username ? $session->username . '&nbsp;' : '') ?>
            </strong>
          <i class="fa fa-chevron-down"></i>
        </a>
        <ul class="dropdown">
          <?php if($session->isLoggedIn()): ?>
            <li><a href="<?= url_for('/staff/index.php') ?>">Dashboard</a></li>
            <li><a href="<?= url_for('/logout.php') ?>" class="logout">logout</a></li>
          <?php else: ?>
            <li><a href="<?= url_for('/login.php') ?>" class="login">login</a></li>
          <?php endif; ?>
        </ul>
      </li>
    </ul>
  </nav>
</header>
<!-- // header -->
