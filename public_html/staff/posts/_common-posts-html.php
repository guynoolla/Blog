<?php

function td_action_edit($post, $is_admin=false) {

  if (url_contain('staff/posts/index')) {
    $url = 'staff/posts/index.php';
  } elseif (url_contain('staff/posts/published')) {
    $url = 'staff/posts/published.php';
  } elseif (url_contain('staff/post/proved')) {
    $url = 'staff/posts/proved.php';
  }

  ob_start();

  ?><td scope="colgroup">
    <?php if (!$post->published): ?>
      <a class="btn-lk btn-lk--secondary" href="<?php echo url_for($url . '?id=' . $post->id . '&cmd=edit')
        ?>">edit</a>
    <?php else:
      if (!$is_admin): ?>
        <a class="btn-lk btn-lk--light disabled text-center d-block">
          <small class="text-dark">on moderation</small>
        </a>
      <?php else:
        if ($url == 'staff/posts/index.php'): ?>
          <a class="btn-lk btn-lk--primary" href="<?php echo url_for($url . '?id=' . $post->id . '&cmd=unpublish')
          ?>">unpublish</a>
        <?php elseif ($url == 'staff/posts/published.php'): ?>
          <a class="btn-lk btn-lk--secondary" href="<?php echo url_for($url . '?id=' . $post->id . '&cmd=edit')
          ?>">edit</a>
        <?php endif;
      endif;
    endif; ?>
  </td><?php

  $output = ob_get_contents();
  ob_end_clean();

  return $output;
}

function td_action_prove($post, $is_admin) {
  if ($is_admin) {

    if (url_contain('staff/posts/index')) {
      $url = 'staff/posts/index.php';
    } elseif (url_contain('staff/posts/published')) {
      $url = 'staff/posts/published.php';
    } elseif (url_contain('staff/posts/proved')) {
      $url = 'staff/posts/proved.php';
    }

    ob_start();

    ?><td scope="colgroup">

    <?php if (!$post->published): ?>
      <a class="btn-lk btn-lk--primary" href="<?php echo url_for($url . '?id=' . $post->id . '&cmd=publish')
      ?>">publish</a>

    <?php elseif ($post->published && !$post->proved): ?>
      <a class="btn-lk btn-lk--success" href="<?php echo url_for($url . '?id=' . $post->id . '&cmd=prove')
      ?>">prove</a>
    
    <?php elseif ($post->published && $post->proved): ?>
      <a class="btn-lk btn-lk--warning" href="<?php echo url_for($url . '?id=' . $post->id . '&cmd=disprove')
      ?>">disprove</a>

    <?php else: ?>
      <a class="btn btn-sm disabled text-center d-block">-</a>
    <?php endif; ?>
    
    </td><?php

    $output = ob_get_contents();
    ob_end_clean();

    return $output;
  }

  return '';
}

function td_post_status($post) {
  $output = '';  

  if ($post->published == 0) {
    $output .= '<td class="text-secondary font-weight-bold">draft</td>';
  } elseif ($post->published == 1 && $post->proved == 0) {
    $output .= '<td class="text-primary font-weight-bold">published</td>';
  } elseif ($post->published == 1 && $post->proved == 1) {
    $output .= '<td class="text-success font-weight-bold">proved</td>';
  }

  return $output;
}

function td_post_title($post, $group=false) {
  ob_start();
  
  ?><td scope="colgroup">
    <span class="h5">
      <a href="<?php echo url_for('preview/' . u($post->title) . '?id=' . $post->id)
      ?>"><?php echo h($post->title) ?></a>
    </span>
    </td><?php
  if ($group !== false): ?>
    <td scope="colgroup"><em><a href="<?php echo url_for('preview/' . u($post->title) . '?id=' . $post->id)
      ?>">view</a></em>
    </td><?php
  endif;

  $output = ob_get_contents();
  ob_end_clean();

  return $output;
}

function page_back_button($link='Back', $class_list='btn btn-outline-primary ml-auto mb-1 ml-1') {
  $url = '';

  if (isset($_SERVER['HTTP_REFERER']) && isset($_SERVER['HTTP_HOST'])) {
    $url = parse_url($_SERVER['HTTP_REFERER']);
    if ($url['host'] == $_SERVER['HTTP_HOST']) {
      $url = $_SERVER['HTTP_REFERER'];
    } else {
      $url = '';
    }
  }

  $output = "<a href=\"" . ($url ? $url : url_for('index.php')) . "\"";
  $output .= " class=\"" . $class_list . "\" >";
  $output .= $link;
  $output .= '</a>';
  
  return $output;
}
