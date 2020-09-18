<section class="carousel" role="listbox">
  <div class="carousel-content">
    <div class="left-right-overlay"></div>
    <div class="slider">
      <?php if (isset($carousel_posts)) {
        foreach ($carousel_posts as $post): ?>
          <div>
            <div class="ard ard--square">
              <img srcset="<?php echo \App\Classes\Post::responsive($post->image, 3) ?>" alt="<?php echo $post->title ?>">
            </div>
            <div class="slider-post-text">
              <a href="<?php echo url_for('topic/' . u($post->topic) . '?tid=' . $post->topic_id) ?>" class="category"><?php echo $post->topic ?></a>
              <h2><a href="<?php echo url_for('post/' . u($post->title)) . '?id=' . $post->id ?>"><?php echo $post->title ?></a></h2>
              <div class="read-more"><a href="<?php echo url_for('post/' . u($post->title)) . '?id=' . $post->id ?>">Read More</a></div>
            </div>
          </div><?php
        endforeach;
      } ?>
    </div>
    <div class="slider-nav">
      <a class="slider-btn next"><i class="fa fa-chevron-right"></i></a>
      <a class="slider-btn prev"><i class="fa fa-chevron-left"></i></a>
    </div>
  </div>
</section>