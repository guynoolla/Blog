<?php

use App\Classes\Post;

$published = Post::countAll([
  'published' => 1,
  'approved' => 0,
  'user_id' => ['!=' => $session->getUserId()]
]);

?>
<ul class="sidebar-nav">
  <li class="nav-item logo">
    <a href="<?php echo url_for('staff/index.php') ?>" class="nav-link">
      <span class="link-text logo-text">Dashboard</span>
      <svg
        aria-hidden="true"
        focusable="false"
        data-prefix="fad"
        data-icon="angle-double-right"
        role="img"
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 448 512"
        class="doubleArrowJS svg-icon svg-inline--fa fa-angle-double-right fa-w-14 fa-5x"
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
    </a>
  </li>

  <!-- Check Author >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> -->
  <?php if ($session->isAuthor()):
    $active = (url_contain('staff/posts/create') ? ' active' : ''); ?>
    <li class="nav-item">
      <a href="<?php echo url_for('staff/posts/create.php') ?>" class="nav-link<?php echo $active ?>">
        <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="feather" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-icon svg-icon--smaller svg-inline--fa fa-feather fa-w-16 fa-5x"><path fill="currentColor" d="M467.14 44.84c-62.55-62.48-161.67-64.78-252.28 25.73-78.61 78.52-60.98 60.92-85.75 85.66-60.46 60.39-70.39 150.83-63.64 211.17l178.44-178.25c6.26-6.25 16.4-6.25 22.65 0s6.25 16.38 0 22.63L7.04 471.03c-9.38 9.37-9.38 24.57 0 33.94 9.38 9.37 24.6 9.37 33.98 0l66.1-66.03C159.42 454.65 279 457.11 353.95 384h-98.19l147.57-49.14c49.99-49.93 36.38-36.18 46.31-46.86h-97.78l131.54-43.8c45.44-74.46 34.31-148.84-16.26-199.36z" class="svg-icon"></path></svg>
        <span class="link-text">Add Post</span>
      </a>
    </li>
  <?php endif; ?>
  <!-- <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Check Author -->

  <!-- Check Admin >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> -->
  <?php if ($session->isAdmin()):
    $active = (url_contain('staff/posts/index') ? ' active' : ''); ?>
    <li class="nav-item border-bottom">
      <a href="<?php echo url_for('staff/posts/index.php') ?>" class="nav-link<?php echo $active ?>">
      <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="industry" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-icon svg-icon--smaller svg-inline--fa fa-industry fa-w-16 fa-5x"><path fill="currentColor" d="M475.115 163.781L336 252.309v-68.28c0-18.916-20.931-30.399-36.885-20.248L160 252.309V56c0-13.255-10.745-24-24-24H24C10.745 32 0 42.745 0 56v400c0 13.255 10.745 24 24 24h464c13.255 0 24-10.745 24-24V184.029c0-18.917-20.931-30.399-36.885-20.248z" class="svg-icon"></path></svg>
        <span class="link-text">Admin Posts</span>
      </a>
    </li>

    <?php $active = (url_contain('staff/posts/drafts') ? ' active' : ''); ?>
    <li class="nav-item">
      <a href="<?php echo url_for('staff/posts/drafts.php') ?>" class="nav-link<?php echo $active ?>">
        <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="pencil-ruler" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-icon svg-icon--smaller svg-inline--fa fa-pencil-ruler fa-w-16 fa-5x"><path fill="currentColor" d="M109.46 244.04l134.58-134.56-44.12-44.12-61.68 61.68a7.919 7.919 0 0 1-11.21 0l-11.21-11.21c-3.1-3.1-3.1-8.12 0-11.21l61.68-61.68-33.64-33.65C131.47-3.1 111.39-3.1 99 9.29L9.29 99c-12.38 12.39-12.39 32.47 0 44.86l100.17 100.18zm388.47-116.8c18.76-18.76 18.75-49.17 0-67.93l-45.25-45.25c-18.76-18.76-49.18-18.76-67.95 0l-46.02 46.01 113.2 113.2 46.02-46.03zM316.08 82.71l-297 296.96L.32 487.11c-2.53 14.49 10.09 27.11 24.59 24.56l107.45-18.84L429.28 195.9 316.08 82.71zm186.63 285.43l-33.64-33.64-61.68 61.68c-3.1 3.1-8.12 3.1-11.21 0l-11.21-11.21c-3.09-3.1-3.09-8.12 0-11.21l61.68-61.68-44.14-44.14L267.93 402.5l100.21 100.2c12.39 12.39 32.47 12.39 44.86 0l89.71-89.7c12.39-12.39 12.39-32.47 0-44.86z" class="svg-icon"></path></svg>
        <span class="link-text">Users Draft Posts</span>
      </a>
    </li>

    <?php $active = (url_contain('staff/posts/published') ? ' active' : ''); ?>
    <li class="nav-item">
      <a href="<?php echo url_for('staff/posts/published.php') ?>" class="nav-link<?php echo $active ?>">
        <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="toggle-on" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" class="svg-icon svg-icon--smaller svg-inline--fa fa-toggle-on fa-w-18 fa-5x"><path fill="currentColor" d="M384 64H192C86 64 0 150 0 256s86 192 192 192h192c106 0 192-86 192-192S490 64 384 64zm0 320c-70.8 0-128-57.3-128-128 0-70.8 57.3-128 128-128 70.8 0 128 57.3 128 128 0 70.8-57.3 128-128 128z" class="svg-icon"></path></svg>
        <span class="link-text">Users Published Posts &nbsp;<span class="show-number"><?php echo $published ?></span></span>
      </a>
    </li>

    <?php $active = (url_contain('staff/posts/approved') ? ' active' : ''); ?>
    <li class="nav-item border-bottom" id="themeButton">
      <a href="<?php echo url_for('staff/posts/approved.php') ?>" class="nav-link<?php echo $active ?>">
      <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="check-double" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-icon svg-icon--smaller svg-inline--fa fa-check-double fa-w-16 fa-5x"><path fill="currentColor" d="M505 174.8l-39.6-39.6c-9.4-9.4-24.6-9.4-33.9 0L192 374.7 80.6 263.2c-9.4-9.4-24.6-9.4-33.9 0L7 302.9c-9.4 9.4-9.4 24.6 0 34L175 505c9.4 9.4 24.6 9.4 33.9 0l296-296.2c9.4-9.5 9.4-24.7.1-34zm-324.3 106c6.2 6.3 16.4 6.3 22.6 0l208-208.2c6.2-6.3 6.2-16.4 0-22.6L366.1 4.7c-6.2-6.3-16.4-6.3-22.6 0L192 156.2l-55.4-55.5c-6.2-6.3-16.4-6.3-22.6 0L68.7 146c-6.2 6.3-6.2 16.4 0 22.6l112 112.2z" class="svg-icon"></path></svg>
        <span class="link-text">Users Approved Posts</i></span>
      </a>
    </li>

    <?php $active = (url_contain('staff/categories/index') ? ' active' : ''); ?>
    <li class="nav-item border-bottom" id="themeButton">
      <a href="<?php echo url_for('staff/categories/index.php') ?>" class="nav-link<?php echo $active ?>">
        <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="sign" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-icon svg-icon--smaller svg-inline--fa fa-sign fa-w-16 fa-5x"><path fill="currentColor" d="M496 64H128V16c0-8.8-7.2-16-16-16H80c-8.8 0-16 7.2-16 16v48H16C7.2 64 0 71.2 0 80v32c0 8.8 7.2 16 16 16h48v368c0 8.8 7.2 16 16 16h32c8.8 0 16-7.2 16-16V128h368c8.8 0 16-7.2 16-16V80c0-8.8-7.2-16-16-16zM160 384h320V160H160v224z" class="svg-icon"></path></svg>
        <span class="link-text">Categories</span>
      </a>
    </li>

    <?php $active = (url_contain('staff/users/index') ? ' active' : ''); ?>
    <li class="nav-item" id="themeButton">
      <a href="<?php echo url_for('staff/users/index.php') ?>" class="nav-link<?php echo $active ?>">
        <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="user-graduate" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="svg-icon--smaller svg-inline--fa fa-user-graduate fa-w-14 fa-3x"><path fill="currentColor" d="M319.4 320.6L224 416l-95.4-95.4C57.1 323.7 0 382.2 0 454.4v9.6c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48v-9.6c0-72.2-57.1-130.7-128.6-133.8zM13.6 79.8l6.4 1.5v58.4c-7 4.2-12 11.5-12 20.3 0 8.4 4.6 15.4 11.1 19.7L3.5 242c-1.7 6.9 2.1 14 7.6 14h41.8c5.5 0 9.3-7.1 7.6-14l-15.6-62.3C51.4 175.4 56 168.4 56 160c0-8.8-5-16.1-12-20.3V87.1l66 15.9c-8.6 17.2-14 36.4-14 57 0 70.7 57.3 128 128 128s128-57.3 128-128c0-20.6-5.3-39.8-14-57l96.3-23.2c18.2-4.4 18.2-27.1 0-31.5l-190.4-46c-13-3.1-26.7-3.1-39.7 0L13.6 48.2c-18.1 4.4-18.1 27.2 0 31.6z" class="svg-icon"></path></svg>
        <span class="link-text">Users</span>
      </a>
    </li>
    <?php $active = (url_contain('staff/site/edit') ? ' active' : ''); ?>
    <li class="nav-item" id="themeButton">
      <a href="<?php echo url_for('staff/site/edit.php') ?>" class="nav-link<?php echo $active ?>">
        <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="cog" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-icon svg-icon--smaller svg-inline--fa fa-cog fa-w-16 fa-3x"><path fill="currentColor" d="M487.4 315.7l-42.6-24.6c4.3-23.2 4.3-47 0-70.2l42.6-24.6c4.9-2.8 7.1-8.6 5.5-14-11.1-35.6-30-67.8-54.7-94.6-3.8-4.1-10-5.1-14.8-2.3L380.8 110c-17.9-15.4-38.5-27.3-60.8-35.1V25.8c0-5.6-3.9-10.5-9.4-11.7-36.7-8.2-74.3-7.8-109.2 0-5.5 1.2-9.4 6.1-9.4 11.7V75c-22.2 7.9-42.8 19.8-60.8 35.1L88.7 85.5c-4.9-2.8-11-1.9-14.8 2.3-24.7 26.7-43.6 58.9-54.7 94.6-1.7 5.4.6 11.2 5.5 14L67.3 221c-4.3 23.2-4.3 47 0 70.2l-42.6 24.6c-4.9 2.8-7.1 8.6-5.5 14 11.1 35.6 30 67.8 54.7 94.6 3.8 4.1 10 5.1 14.8 2.3l42.6-24.6c17.9 15.4 38.5 27.3 60.8 35.1v49.2c0 5.6 3.9 10.5 9.4 11.7 36.7 8.2 74.3 7.8 109.2 0 5.5-1.2 9.4-6.1 9.4-11.7v-49.2c22.2-7.9 42.8-19.8 60.8-35.1l42.6 24.6c4.9 2.8 11 1.9 14.8-2.3 24.7-26.7 43.6-58.9 54.7-94.6 1.5-5.5-.7-11.3-5.6-14.1zM256 336c-44.1 0-80-35.9-80-80s35.9-80 80-80 80 35.9 80 80-35.9 80-80 80z" class="svg-icon"></path></svg>
        <span class="link-text">Site Settings</span>
      </a>
    </li>
  <!-- Check Admin >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> -->

  <!-- Check Author >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> -->
  <?php elseif ($session->isAuthor()):
    $active = (url_contain('staff/posts/index') ? ' active' : ''); ?>
    <li class="nav-item">
      <a href="<?php echo url_for('staff/posts/index.php') ?>" class="nav-link<?php echo $active ?>">
        <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="industry" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-icon svg-icon--smaller svg-inline--fa fa-industry fa-w-16 fa-5x"><path fill="currentColor" d="M475.115 163.781L336 252.309v-68.28c0-18.916-20.931-30.399-36.885-20.248L160 252.309V56c0-13.255-10.745-24-24-24H24C10.745 32 0 42.745 0 56v400c0 13.255 10.745 24 24 24h464c13.255 0 24-10.745 24-24V184.029c0-18.917-20.931-30.399-36.885-20.248z" class="svg-icon"></path></svg>
        <span class="link-text">User Posts</span>
      </a>
    </li>
  <?php endif; ?>
  <!-- <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Check Author -->
  
  <?php if ($session->isLoggedIn() && !$session->isAdmin()):
    $active = (url_contain('staff/users/edit') ? ' active' : ''); ?>
    <li class="nav-item" id="themeButton">
      <a href="<?php echo url_for('staff/users/edit.php?id=' . $session->getUserId()) ?>" class="nav-link<?php echo $active ?>">
        <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="user" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="svg-icon svg-icon--smaller svg-inline--fa fa-user fa-w-14 fa-5x"><path fill="currentColor" d="M224 256c70.7 0 128-57.3 128-128S294.7 0 224 0 96 57.3 96 128s57.3 128 128 128zm89.6 32h-16.7c-22.2 10.2-46.9 16-72.9 16s-50.6-5.8-72.9-16h-16.7C60.2 288 0 348.2 0 422.4V464c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48v-41.6c0-74.2-60.2-134.4-134.4-134.4z" class="svg-icon"></path></svg>
        <span class="link-text">User Settings</span>
      </a>
    </li>
  <?php endif; ?>
</ul>
