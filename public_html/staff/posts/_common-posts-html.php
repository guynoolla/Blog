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
          <small class="text-dark"><?php
            echo ($post->proved ? '&mdash;' : 'on moderation')
          ?></small>
        </a>
      <?php else: ?>
        <a class="btn-lk btn-lk--primary" href="<?php echo url_for($url . '?id=' . $post->id . '&cmd=unpublish')
        ?>">unpublish</a><?php
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
