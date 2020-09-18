<?php

use App\Classes\User;
use App\Classes\Post;
use App\Classes\Topic;

if (url_contain(['post/','preview/'])) {
  $user = User::findById($post->user_id);
}

$posts = App\Classes\Post::findWhere(
  ['approved' => '1'],
  'ORDER BY RAND() LIMIT 3'
);

$topics = Topic::findAll();

?>
<div class="sidebar-content mt-2 pt-1">

  <section class="widget">
    <h3 class="title">Follow Us</h3>

    <div class="social-links-widget more-space-between">
      <?php include '_social_links_list.php' ?>
    </div>
  </section>

  <?php if (url_contain(['post/','preview/']) && $user->about_appear): ?>
    <section class="widget mt-5">
      <h3 class="title mb-3">About Author</h3>

      <div class="about-widget">
        <div class="about-image d-flex align-items-center justify-content-center">
          <img class="rounded-circle" src="<?php echo url_for('assets/images' . $user->about_image) ?>" alt="About Us">
        </div>
        <div class="about-text">
          <p><?php echo $user->about_text ?></p>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <section class="widget mt-4">
    <h3 class="title">Recent Posts</h3>

    <div class="recent-posts-widget">
      <?php $i = 1; foreach ($carousel_posts as $post): ?>
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

  <section class="widget">
    <div class="search-widget pt-4 pb-3">
      <form role="search" method="get" class="form-search" action="<?php echo url_for('index.php') ?>">
        <div class="input-group">
          <label class="screen-reader-text" for="s">Search for:</label>
          <input type="text" class="form-control search-query" placeholder="Search…" value="" name="s" title="Search for:">
          <div class="input-group-append">
            <button type="submit" class="btn btn-default" name="submit" id="searchSubmit" value="Search">Search</button>
          </div>
        </div>
      </form>
    </div>
  </section>

  <section class="widget">
    <h3 class="title">Topics</h3>
    
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

  <section class="widget my-4" id="widget-contact-form">
    <h3 class="title"><?php echo $jsonstore->contactForm->title ?></h3>
    <div class="widget-contact-form">
      <div class="alert alert-dismissible d-none rounded-0"></div>
      <form id="contactForm" action="<?php echo url_for('form_post.php') ?>" method="post">
        <div class="form-group mb-0">
          <label for="email" class="d-none">Email</label>
          <input type="email" name="email" class="form-control rounded-0" id="email" placeholder="<?php echo $jsonstore->contactForm->emailPlaceholder ?>">
          <span class="text-danger field-validation-error"></span>
        </div>
        <div class="form-group">
          <label for="message" class="d-none">Message</label>
          <textarea name="message" class="form-control rounded-0" id="message" rows="3" placeholder="<?php echo $jsonstore->contactForm->messagePlaceholder ?>"></textarea>
          <span class="text-danger field-validation-error"></span>
        </div>
        <button type="submit" class="btn btn-lg btn-default float-right rounded-0" name="submit" id="contactSubmit">
          <span class="spinner-grow spinner-grow-sm d-none" role="status" aria-hidden="true"></span>
          <?php echo $jsonstore->contactForm->buttonText ?>
        </button>
      </form>
    </div>
  </section>

</div>