<section class="carousel" role="listbox">
  <div class="carousel-content">
    <div class="left-right-overlay"></div>
    <div class="slider">
      <?php foreach($posts as $post): ?>
        <div>
          <img src="<?php echo url_for('assets/images/' . $post->image) ?>" />
          <div class="slider-post-text">
            <a href="<?php echo url_for('topic/' . u($post->topic) . '?id=' . $post->tid) ?>" class="category"><?php echo $post->topic ?></a>
            <h2><a href="<?php echo url_for('post/' . u($post->title)) . '?id=' . $post->id ?>"><?php echo $post->title ?></a></h2>
            <div class="read-more"><a href="<?php echo url_for('post/' . u($post->title)) . '?id=' . $post->id ?>">Read More</a></div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="slider-nav">
      <a class="slider-btn next"><i class="fa fa-chevron-right"></i></a>
      <a class="slider-btn prev"><i class="fa fa-chevron-left"></i></a>
    </div>
  </div>
</section>