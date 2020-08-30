<?php

function td_colgroup_actions($post) {
  ob_start();

  ?><td scope="colgroup">

  <?php if (!$post->published): ?>
    <a class="btn-lk btn-lk--secondary" href="<?php echo url_for('/staff/posts/edit.php?id=' . $post->id) ?>">
      edit
    </a>
  <?php else: ?>
    <a class="btn btn-sm disabled text-center d-block">-</a>
  <?php endif; ?>

  </td><?php

  $output = ob_get_contents();
  ob_end_clean();

  return $output;
}

function td_colgroup_actions_admin($post) {
  ob_start();

  ?><td scope="colgroup"><?php

  if ($post->published && $post->proved): ?>
    <a class="btn-lk btn-lk--warning" href="<?php echo url_for('/staff/posts/proved.php?id=' . $post->id . '&cmd=disprove') ?>">
      disprove
    </a>
  <?php elseif ($post->published && !$post->proved): ?>
    <a class="btn-lk btn-lk--primary" href="<?php echo url_for('/staff/posts/unproved.php?id=' . $post->id . '&cmd=prove') ?>">
      prove
    </a>
  <?php else: ?>
    <a class="btn btn-sm disabled text-center d-block">-</a>
  <?php endif; ?>
  
  </td><?php

  $output = ob_get_contents();
  ob_end_clean();

  return $output;
}

function td_post_status($post) {
  $output = '';  

  if ($post->published == 0) {
    $output .= '<td class="text-secondary font-weight-bold">draft</td>';
  } elseif ($post->published == 1 && $post->proved == 0) {
    $output .= '<td class="text-danger font-weight-bold">published</td>';
  } elseif ($post->published == 1 && $post->proved == 1) {
    $output .= '<td class="text-success font-weight-bold">proved</td>';
  }

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
