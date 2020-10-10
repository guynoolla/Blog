<?php

use App\Classes\User;
use App\Classes\Post;
use App\Classes\Topic;

include("./simple-php-captcha.php");
$_SESSION['captcha'] = simple_php_captcha();

$email = "";
$message = "";
$captcha_err = "";

if ($session->store_of('fp_captcha_err')) {
  $email = $session->store_of('fp_field_email', false);
  $message = $session->store_of('fp_field_message', false);
  $captcha_err = $session->store_of('fp_captcha_err', false);
}

if (url_contain(['/post/', '/preview/'])) {
  $user = User::findById($post->user_id);
}

$posts = App\Classes\Post::findWhere(
  ['approved' => '1', 'format' => 'image'],
  'ORDER BY created_at DESC'
);

$topics = Topic::findAll();

?>
<div class="sidebar-content mt-2 mb-4 py-1">

  <?php if ($jsonstore->sidebarWidget->follow->show): ?>

  <section class="widget">
    <h3 class="title"><?php echo $jsonstore->sidebarWidget->follow->title ?></h3>

    <div class="social-links-widget more-space-between">
      <?php include '_social_links_list.php' ?>
    </div>
  </section>

  <?php endif; ?>

  <?php if ($jsonstore->sidebarWidget->about->show) { ?>

    <?php if (url_contain(['/post/','/preview/']) && $user->about_appear): ?>
    <section class="widget">
      <h3 class="title mt-5 mb-3"><?php echo $jsonstore->sidebarWidget->about->title ?></h3>

      <div class="about-widget">
        <div class="about-image d-flex align-items-center justify-content-center">
          <img class="rounded-circle lazyload" data-sizes="auto" data-src="<?php echo url_for('assets/images' . $user->about_image) ?>" alt="About Us">
        </div>
        <div class="about-text">
          <p><?php echo $user->about_text ?></p>
        </div>
      </div>
    </section>
    <?php endif; ?>

  <?php } ?>

  <?php if ($jsonstore->sidebarWidget->posts->show): ?>

  <section class="widget">
    <h3 class="title"><?php echo $jsonstore->sidebarWidget->posts->title ?></h3>

    <div class="recent-posts-widget">
      <?php $i = 1; foreach ($posts as $post): ?>
        <div class="post">
          <div class="post-image ">
            <a href="<?php echo url_for('post/' . u($post->title) . '?id=' . $post->id) ?>">
              <img src="<?php echo url_for('render_img.php?img='. u($post->image) .'&w=420') ?>" style="object-fit:cover" width="150" height="150">
            </a>
          </div> 
          <div class="post-content">
            <a href="<?php echo url_for('post/' . u($post->title) . '?id=' . $post->id) ?>"><?php echo $post->title ?></a>
            <span class="date">- 05 Oct , 2016</span>
          </div>
        </div>
        <?php if ($i == 3) break; ?>
      <?php $i++; endforeach; ?>
    </div>
  </section>

  <?php endif; ?>

  <?php if ($jsonstore->sidebarWidget->search->show): ?>

  <section class="widget">
    <div class="search-widget pt-4 pb-3">
      <form id="asideSearchForm" role="search" method="get" class="form-search" action="<?php echo url_for('index.php') ?>">
        <div class="input-group">
          <label class="screen-reader-text" for="s">Search for:</label>
          <input name="s" type="text" class="form-control search-query" placeholder="Search…" value="" title="Search for:">
          <div class="input-group-append">
            <button type="submit" class="btn btn-default" id="searchSubmit">Search</button>
          </div>
        </div>
      </form>
    </div>
  </section>

  <?php endif; ?>

  <?php if ($jsonstore->sidebarWidget->topics->show): ?>

  <section class="widget">
    <h3 class="title mb-3"><?php echo $jsonstore->sidebarWidget->topics->title ?></h3>
    
    <div class="cats-widget">
      <ul>
        <?php foreach ($topics as $topic): ?>
          <li class="cat-item">
            <a href="<?php echo url_for('topic/') . u($topic->name) . '?tid=' . $topic->id ?>" title="<?php $topic->name ?>"><?php echo $topic->name ?></a>
            <span><?php echo Post::countAll(
              ['topic_id' => $topic->id, 'approved' => '1']
            ) ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>

  <?php endif; ?>

  <?php if ($jsonstore->sidebarWidget->contact->show && is_homepage()): ?>

  <section class="widget" id="contact-form">
    <h3 class="title"><?php echo $jsonstore->sidebarWidget->contact->title ?></h3>
    <div class="widget-contact-form pt-2">
      <?php
        if ($jsonstore->contactForm->placeholder) {
          $email_ph = 'Your ' . strtolower($jsonstore->contactForm->emailLabel);
          $message_ph = 'Your ' . strtolower($jsonstore->contactForm->messageLabel);
        }
      ?>
      <form id="contactForm" action="<?php echo url_for('form_post.php') ?>" method="post" name="contactForm">
        <div class="form-group mb-0">
          <label for="email" class="d-none">Email</label>
          <input type="email" name="email" id="email" value="<?php echo $email ?>" class="form-control" <?php echo ($email_ph ? 'placeholder="'.$email_ph.'"' : '') ?>>
          <span class="text-danger field-validation-error"></span>
        </div>
        <div class="form-group mb-0">
          <label for="message" class="d-none">Message</label>
          <textarea name="message" id="message" class="form-control" rows="3" <?php echo ($message_ph ? 'placeholder="'.$message_ph.'"' : '') ?>><?php echo $message ?></textarea>
          <span class="text-danger field-validation-error"></span>
        </div>
        <div class="form-group my-0">
          <div class="form-group-captcha col px-0 d-flex align-items-start justify-content-start bg-light">
            <img src="<?php echo $_SESSION['captcha']['image_src'] ?>" style="z-index:500">
            <input type="text" name="captcha" id="captcha" class="captcha-field align-self-end bg-light border-0" placeholder="<?php echo $jsonstore->captcha->placeholder ?>">
            <button type="submit" class="btn btn-lg btn-default ml-auto rounded-0" name="contactFormSubmit" id="contactSubmit">
              <span class="spinner-grow spinner-grow-sm d-none mr-3" role="status" aria-hidden="true"></span>
              <?php echo $jsonstore->contactForm->buttonText ?>
            </button>
          </div>
          <span class="text-danger field-validation-error"><?php echo $captcha_err ?></span>
        </div>
        <div class="response response--shade"></div>
      </form>

    </div>
  </section>

  <?php endif; ?>

</div>