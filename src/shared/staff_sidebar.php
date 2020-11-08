<?php

use App\Classes\Post;

$published = Post::countAll([
  'published' => 1,
  'approved' => 0,
  'user_id' => ['!=' => $session->getUserId()]
]);

?>
<ul class="sidebar-nav">
  <li class="nav-item logo logo--flex">
    <a href="<?php echo url_for('staff/index.php') ?>" class="nav-link">
      <span class="link-text logo-text">Dashboard</span>
    </a>
    <span class="double-arrow-btn">
      <?php if ($jsonstore->fontAwesome != 'svg'): ?>
        <i class="fa fa-angle-double-right"></i>
      <?php else: ?>
        <svg
          aria-hidden="true"
          focusable="false"
          data-prefix="fad"
          data-icon="angle-double-right"
          role="img"
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 448 512"
          class="svg-icon svg-inline--fa fa-angle-double-right fa-w-14 fa-5x"
        >
          <g class="fa-group">
            <path
              fill="currentColor"
              d="M224 273L88.37 409a23.78 23.78 0 0 1-33.8 0L32 386.36a23.94 23.94 0 0 1 0-33.89l96.13-96.37L32 159.73a23.94 23.94 0 0 1 0-33.89l22.44-22.79a23.78 23.78 0 0 1 33.8 0L223.88 239a23.94 23.94 0 0 1 .1 34z"
              class="fa-secondary"
            ></path>
            <path
              fill="currentColor"
              d="M415.89 273L280.34 409a23.77 23.77 0 0 1-33.79 0L224 386.26a23.94 23.94 0 0 1 0-33.89L320.11 256l-96-96.47a23.94 23.94 0 0 1 0-33.89l22.52-22.59a23.77 23.77 0 0 1 33.79 0L416 239a24 24 0 0 1-.11 34z"
              class="fa-primary"
            ></path>
          </g>
        </svg>
      <?php endif; ?>
    </span>
  </li>

  <!-- Check Author >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> -->
  <?php if ($session->isAuthor()):
    $active = (url_contain('staff/posts/create') ? ' active' : ''); ?>
    <li class="nav-item">
      <a href="<?php echo url_for('staff/posts/create.php') ?>" class="nav-link<?php echo $active ?>">
        <?php if ($jsonstore->fontAwesome != 'svg'): ?>
          <i class="fa fa-pencil"></i>
        <?php else: ?>
          <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="feather" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-icon svg-icon--smaller svg-inline--fa fa-feather fa-w-16 fa-5x"><path fill="currentColor" d="M467.14 44.84c-62.55-62.48-161.67-64.78-252.28 25.73-78.61 78.52-60.98 60.92-85.75 85.66-60.46 60.39-70.39 150.83-63.64 211.17l178.44-178.25c6.26-6.25 16.4-6.25 22.65 0s6.25 16.38 0 22.63L7.04 471.03c-9.38 9.37-9.38 24.57 0 33.94 9.38 9.37 24.6 9.37 33.98 0l66.1-66.03C159.42 454.65 279 457.11 353.95 384h-98.19l147.57-49.14c49.99-49.93 36.38-36.18 46.31-46.86h-97.78l131.54-43.8c45.44-74.46 34.31-148.84-16.26-199.36z" class="svg-icon"></path></svg>
        <?php endif; ?>
        <span class="link-text">New post</span>
      </a>
    </li>
  <?php endif; ?>
  <!-- <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Check Author -->

  <!-- Check Admin >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> -->
  <?php if ($session->isAdmin()):
    $active = (url_contain('staff/posts/index') ? ' active' : ''); ?>
    <li class="nav-item border-bottom">
      <a href="<?php echo url_for('staff/posts/index.php') ?>" class="nav-link<?php echo $active ?>">
        <?php if ($jsonstore->fontAwesome != 'svg'): ?>
          <i class="fa fa--more fa-adn" style="transform:scale(1.1)"></i>
        <?php else: ?>
          <svg aria-hidden="true" focusable="false" data-prefix="fab" data-icon="adn" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 496 512" class="svg-icon svg-icon--middle svg-icon svg-inline--fa fa-adn fa-w-16 fa-3x"><path fill="currentColor" d="M248 167.5l64.9 98.8H183.1l64.9-98.8zM496 256c0 136.9-111.1 248-248 248S0 392.9 0 256 111.1 8 248 8s248 111.1 248 248zm-99.8 82.7L248 115.5 99.8 338.7h30.4l33.6-51.7h168.6l33.6 51.7h30.2z" class="svg-icon"></path></svg>
          <!--<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="save" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="svg-icon--large svg-inline--fa fa-save fa-w-14 fa-5x"><path fill="currentColor" d="M433.941 129.941l-83.882-83.882A48 48 0 0 0 316.118 32H48C21.49 32 0 53.49 0 80v352c0 26.51 21.49 48 48 48h352c26.51 0 48-21.49 48-48V163.882a48 48 0 0 0-14.059-33.941zM224 416c-35.346 0-64-28.654-64-64 0-35.346 28.654-64 64-64s64 28.654 64 64c0 35.346-28.654 64-64 64zm96-304.52V212c0 6.627-5.373 12-12 12H76c-6.627 0-12-5.373-12-12V108c0-6.627 5.373-12 12-12h228.52c3.183 0 6.235 1.264 8.485 3.515l3.48 3.48A11.996 11.996 0 0 1 320 111.48z" class="svg-icon"></path></svg>-->
        <?php endif; ?>
        <span class="link-text">Admin posts</span>
      </a>
    </li>

    <?php $active = (url_contain('staff/posts/drafts') ? ' active' : ''); ?>
    <li class="nav-item">
      <a href="<?php echo url_for('staff/posts/drafts.php') ?>" class="nav-link<?php echo $active ?>">
        <?php if ($jsonstore->fontAwesome != 'svg'): ?>
          <i class="fa fa-folder"></i>
        <?php else: ?>
          <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="folder" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-icon svg-icon--middle svg-inline--fa fa-folder fa-w-16 fa-3x"><path fill="currentColor" d="M464 128H272l-64-64H48C21.49 64 0 85.49 0 112v288c0 26.51 21.49 48 48 48h416c26.51 0 48-21.49 48-48V176c0-26.51-21.49-48-48-48z" class="svg-icon"></path></svg>
        <?php endif; ?>
        <span class="link-text">Users draft posts</span>
      </a>
    </li>

    <?php $active = (url_contain('staff/posts/published') ? ' active' : ''); ?>
    <li class="nav-item">
      <a href="<?php echo url_for('staff/posts/published.php') ?>" class="nav-link<?php echo $active ?>">
        <?php if ($jsonstore->fontAwesome != 'svg'): ?>
          <i class="fa fa-toggle-on"></i>
        <?php else: ?>
          <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="toggle-on" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" class="svg-icon svg-icon--smaller svg-inline--fa fa-toggle-on fa-w-18 fa-5x"><path fill="currentColor" d="M384 64H192C86 64 0 150 0 256s86 192 192 192h192c106 0 192-86 192-192S490 64 384 64zm0 320c-70.8 0-128-57.3-128-128 0-70.8 57.3-128 128-128 70.8 0 128 57.3 128 128 0 70.8-57.3 128-128 128z" class="svg-icon"></path></svg>
        <?php endif; ?>
        <span class="link-text">Users published posts &nbsp;<span class="show-number"><?php echo $published ?></span></span>
      </a>
    </li>

    <?php $active = (url_contain('staff/posts/approved') ? ' active' : ''); ?>
    <li class="nav-item border-bottom" id="themeButton">
      <a href="<?php echo url_for('staff/posts/approved.php') ?>" class="nav-link<?php echo $active ?>">
      <?php if ($jsonstore->fontAwesome != 'svg'): ?>
        <i class="fa fa-check" style="transform:scale(1.1)"></i>
      <?php else: ?>
        <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="check" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-icon svg-icon--middle svg-inline--fa fa-check fa-w-16 fa-5x"><path fill="currentColor" d="M173.898 439.404l-166.4-166.4c-9.997-9.997-9.997-26.206 0-36.204l36.203-36.204c9.997-9.998 26.207-9.998 36.204 0L192 312.69 432.095 72.596c9.997-9.997 26.207-9.997 36.204 0l36.203 36.204c9.997 9.997 9.997 26.206 0 36.204l-294.4 294.401c-9.998 9.997-26.207 9.997-36.204-.001z" class="svg-icon"></path></svg>
      <?php endif; ?>
      <span class="link-text">Users approved posts</i></span>
      </a>
    </li>

    <?php $active = (url_contain('staff/categories/index') ? ' active' : ''); ?>
    <li class="nav-item border-bottom" id="themeButton">
      <a href="<?php echo url_for('staff/categories/index.php') ?>" class="nav-link<?php echo $active ?>">
        <?php if ($jsonstore->fontAwesome != 'svg'): ?>
          <i class="fa fa-map-signs"></i>
        <?php else: ?>
          <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="map-signs" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-icon svg-icon--middle svg-inline--fa fa-map-signs fa-w-16 fa-5x"><path fill="currentColor" d="M507.31 84.69L464 41.37c-6-6-14.14-9.37-22.63-9.37H288V16c0-8.84-7.16-16-16-16h-32c-8.84 0-16 7.16-16 16v16H56c-13.25 0-24 10.75-24 24v80c0 13.25 10.75 24 24 24h385.37c8.49 0 16.62-3.37 22.63-9.37l43.31-43.31c6.25-6.26 6.25-16.38 0-22.63zM224 496c0 8.84 7.16 16 16 16h32c8.84 0 16-7.16 16-16V384h-64v112zm232-272H288v-32h-64v32H70.63c-8.49 0-16.62 3.37-22.63 9.37L4.69 276.69c-6.25 6.25-6.25 16.38 0 22.63L48 342.63c6 6 14.14 9.37 22.63 9.37H456c13.25 0 24-10.75 24-24v-80c0-13.25-10.75-24-24-24z" class="svg-icon"></path></svg>
        <?php endif; ?>
        <span class="link-text">Categories</span>
      </a>
    </li>

    <?php $active = (url_contain('staff/users/index') ? ' active' : ''); ?>
    <li class="nav-item" id="themeButton">
      <a href="<?php echo url_for('staff/users/index.php') ?>" class="nav-link<?php echo $active ?>">
        <?php if ($jsonstore->fontAwesome != 'svg'): ?>
          <i class="fa fa-users"></i>
        <?php else: ?>
          <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="users" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" class="svg-icon svg-icon--smaller svg-inline--fa fa-users fa-w-20 fa-5x"><path fill="currentColor" d="M96 224c35.3 0 64-28.7 64-64s-28.7-64-64-64-64 28.7-64 64 28.7 64 64 64zm448 0c35.3 0 64-28.7 64-64s-28.7-64-64-64-64 28.7-64 64 28.7 64 64 64zm32 32h-64c-17.6 0-33.5 7.1-45.1 18.6 40.3 22.1 68.9 62 75.1 109.4h66c17.7 0 32-14.3 32-32v-32c0-35.3-28.7-64-64-64zm-256 0c61.9 0 112-50.1 112-112S381.9 32 320 32 208 82.1 208 144s50.1 112 112 112zm76.8 32h-8.3c-20.8 10-43.9 16-68.5 16s-47.6-6-68.5-16h-8.3C179.6 288 128 339.6 128 403.2V432c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48v-28.8c0-63.6-51.6-115.2-115.2-115.2zm-223.7-13.4C161.5 263.1 145.6 256 128 256H64c-35.3 0-64 28.7-64 64v32c0 17.7 14.3 32 32 32h65.9c6.3-47.4 34.9-87.3 75.2-109.4z" class="svg-icon"></path></svg>
          <!--<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="user-graduate" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="svg-icon--smaller svg-inline--fa fa-user-graduate fa-w-14 fa-3x"><path fill="currentColor" d="M319.4 320.6L224 416l-95.4-95.4C57.1 323.7 0 382.2 0 454.4v9.6c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48v-9.6c0-72.2-57.1-130.7-128.6-133.8zM13.6 79.8l6.4 1.5v58.4c-7 4.2-12 11.5-12 20.3 0 8.4 4.6 15.4 11.1 19.7L3.5 242c-1.7 6.9 2.1 14 7.6 14h41.8c5.5 0 9.3-7.1 7.6-14l-15.6-62.3C51.4 175.4 56 168.4 56 160c0-8.8-5-16.1-12-20.3V87.1l66 15.9c-8.6 17.2-14 36.4-14 57 0 70.7 57.3 128 128 128s128-57.3 128-128c0-20.6-5.3-39.8-14-57l96.3-23.2c18.2-4.4 18.2-27.1 0-31.5l-190.4-46c-13-3.1-26.7-3.1-39.7 0L13.6 48.2c-18.1 4.4-18.1 27.2 0 31.6z" class="svg-icon"></path></svg>-->
        <?php endif; ?>
        <span class="link-text">Users</span>
      </a>
    </li>
    <?php $active = (url_contain('staff/site/edit') ? ' active' : ''); ?>
    <li class="nav-item" id="themeButton">
      <a href="<?php echo url_for('staff/site/edit.php') ?>" class="nav-link<?php echo $active ?>">
        <?php if ($jsonstore->fontAwesome != 'svg'): ?>
          <i class="fa fa-cog" style="transform:scale(1.1)"></i>
        <?php else: ?>
          <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="cog" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-icon svg-icon--middle svg-inline--fa fa-cog fa-w-16 fa-5x"><path fill="currentColor" d="M487.4 315.7l-42.6-24.6c4.3-23.2 4.3-47 0-70.2l42.6-24.6c4.9-2.8 7.1-8.6 5.5-14-11.1-35.6-30-67.8-54.7-94.6-3.8-4.1-10-5.1-14.8-2.3L380.8 110c-17.9-15.4-38.5-27.3-60.8-35.1V25.8c0-5.6-3.9-10.5-9.4-11.7-36.7-8.2-74.3-7.8-109.2 0-5.5 1.2-9.4 6.1-9.4 11.7V75c-22.2 7.9-42.8 19.8-60.8 35.1L88.7 85.5c-4.9-2.8-11-1.9-14.8 2.3-24.7 26.7-43.6 58.9-54.7 94.6-1.7 5.4.6 11.2 5.5 14L67.3 221c-4.3 23.2-4.3 47 0 70.2l-42.6 24.6c-4.9 2.8-7.1 8.6-5.5 14 11.1 35.6 30 67.8 54.7 94.6 3.8 4.1 10 5.1 14.8 2.3l42.6-24.6c17.9 15.4 38.5 27.3 60.8 35.1v49.2c0 5.6 3.9 10.5 9.4 11.7 36.7 8.2 74.3 7.8 109.2 0 5.5-1.2 9.4-6.1 9.4-11.7v-49.2c22.2-7.9 42.8-19.8 60.8-35.1l42.6 24.6c4.9 2.8 11 1.9 14.8-2.3 24.7-26.7 43.6-58.9 54.7-94.6 1.5-5.5-.7-11.3-5.6-14.1zM256 336c-44.1 0-80-35.9-80-80s35.9-80 80-80 80 35.9 80 80-35.9 80-80 80z" class="svg-icon"></path></svg>
          <!--<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="door-open" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" class="svg-icon svg-icon--smaller svg-inline--fa fa-door-open fa-w-20 fa-5x"><path fill="currentColor" d="M624 448h-80V113.45C544 86.19 522.47 64 496 64H384v64h96v384h144c8.84 0 16-7.16 16-16v-32c0-8.84-7.16-16-16-16zM312.24 1.01l-192 49.74C105.99 54.44 96 67.7 96 82.92V448H16c-8.84 0-16 7.16-16 16v32c0 8.84 7.16 16 16 16h336V33.18c0-21.58-19.56-37.41-39.76-32.17zM264 288c-13.25 0-24-14.33-24-32s10.75-32 24-32 24 14.33 24 32-10.75 32-24 32z" class="svg-icon"></path></svg>-->
        <?php endif; ?>
        <span class="link-text">Site settings</span>
      </a>
    </li>
  <!-- Check Admin >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> -->

  <!-- Check Author >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> -->
  <?php elseif ($session->isAuthor()):
    $active = (url_contain('staff/posts/index') ? ' active' : ''); ?>
    <li class="nav-item">
      <a href="<?php echo url_for('staff/posts/index.php') ?>" class="nav-link<?php echo $active ?>">
        <?php if ($jsonstore->fontAwesome != 'svg'): ?>
          <i class="fa fa-folder-open"></i>
        <?php else: ?>
          <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="folder-open" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" class="svg-icon svg-icon--middle svg-inline--fa fa-folder-open fa-w-18 fa-3x"><path fill="currentColor" d="M572.694 292.093L500.27 416.248A63.997 63.997 0 0 1 444.989 448H45.025c-18.523 0-30.064-20.093-20.731-36.093l72.424-124.155A64 64 0 0 1 152 256h399.964c18.523 0 30.064 20.093 20.73 36.093zM152 224h328v-48c0-26.51-21.49-48-48-48H272l-64-64H48C21.49 64 0 85.49 0 112v278.046l69.077-118.418C86.214 242.25 117.989 224 152 224z" class="svg-icon"></path></svg>
        <?php endif; ?>
        <span class="link-text">User posts</span>
      </a>
    </li>
  <?php endif; ?>
  <!-- <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Check Author -->
  
  <?php if ($session->isLoggedIn() && !$session->isAdmin()):
    $active = (url_contain('staff/users/edit') ? ' active' : ''); ?>
    <li class="nav-item" id="themeButton">
      <a href="<?php echo url_for('staff/users/edit.php?id=' . $session->getUserId()) ?>" class="nav-link<?php echo $active ?>">
        <?php if ($jsonstore->fontAwesome != "svg"): ?>
          <i class="fa fa-user"></i>
        <?php else: ?>
          <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="user" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="svg-icon svg-icon--smaller svg-inline--fa fa-user fa-w-14 fa-5x"><path fill="currentColor" d="M224 256c70.7 0 128-57.3 128-128S294.7 0 224 0 96 57.3 96 128s57.3 128 128 128zm89.6 32h-16.7c-22.2 10.2-46.9 16-72.9 16s-50.6-5.8-72.9-16h-16.7C60.2 288 0 348.2 0 422.4V464c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48v-41.6c0-74.2-60.2-134.4-134.4-134.4z" class="svg-icon"></path></svg>
        <?php endif; ?>
        <span class="link-text">User settings</span>
      </a>
    </li>
  <?php endif; ?>
</ul>
