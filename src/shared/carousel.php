<?php if (isset($carousel_posts)): ?>

  <section class="carousel" role="listbox">
    
    <div class="carousel-spinner d-none d-flex align-items-center justify-content-center">
      <div class="spinner-grow" style="width:2rem; height:2rem;" role="status">
        <span class="sr-only">Loading...</span>
      </div>
    </div>

    <div class="carousel-content">
      <div class="left-right-overlay"></div>

      <div class="slider" data-slides_to_show="<?php 
        echo $jsonstore->slider->slidesToShow ?>" data-slides_to_scroll="<?php
        echo $jsonstore->slider->slidesToScroll
      ?>">
        <?php if (isset($carousel_posts)) {
          foreach ($carousel_posts as $post): ?>
            <div>
              <div class="ard ard--square">
                <img srcset="<?php echo \App\Classes\Post::responsive($post->image, 2) ?>" alt="<?php echo $post->title ?>">
              </div>
              <div class="slider-post-text">
                <a href="<?php echo url_for('category/' . u($post->category) . '?tid=' . $post->category_id) ?>" class="category"><?php echo $post->category ?></a>
                <h2><a href="<?php echo url_for('post/' . u($post->title)) . '?id=' . $post->id ?>"><?php echo $post->title ?></a></h2>
                <div class="read-more"><a href="<?php echo url_for('post/' . u($post->title)) . '?id=' . $post->id ?>">Read More</a></div>
              </div>
            </div><?php
          endforeach;
        } ?>
      </div>
      <div class="slider-nav">
        <a class="slider-btn next" style="width:2rem;height:2rem;">
          <!-- <i class="fa fa-chevron-right"></i> -->
          <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="chevron-right" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" class="svg-icon svg-inline--fa fa-chevron-right fa-w-10 fa-3x"><path fill="currentColor" d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z" class=""></path></svg>
        </a>
        <a class="slider-btn prev" style="width:2rem;height:2rem;">
          <!-- <i class="fa fa-chevron-left"></i> -->
          <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="chevron-left" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" class="svg-icon svg-inline--fa fa-chevron-left fa-w-10 fa-3x"><path fill="currentColor" d="M34.52 239.03L228.87 44.69c9.37-9.37 24.57-9.37 33.94 0l22.67 22.67c9.36 9.36 9.37 24.52.04 33.9L131.49 256l154.02 154.75c9.34 9.38 9.32 24.54-.04 33.9l-22.67 22.67c-9.37 9.37-24.57 9.37-33.94 0L34.52 272.97c-9.37-9.37-9.37-24.57 0-33.94z" class=""></path></svg>
        </a>
      </div>
    </div>

  </section>
  
<?php endif; ?>