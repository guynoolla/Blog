<?php

function td_actions_column_fst($post, $is_admin=false) {

  if (url_contain('staff/posts/index')) {
    $url = 'staff/posts/index.php';
  } elseif (url_contain('staff/posts/published')) {
    $url = 'staff/posts/published.php';
  } elseif (url_contain('staff/post/approved')) {
    $url = 'staff/posts/approved.php';
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
            echo ($post->approved ? '&ndash;' : 'on moderation')
          ?></small>
        </a>
      <?php else: ?>
        <?php if ($post->approved): ?>
          <a class="btn btn-sm disabled text-center d-block">&ndash;</a>
        <?php else: ?>
          <a class="btn-lk btn-lk--primary" href="<?php echo url_for($url . '?id=' . $post->id . '&cmd=unpublish')
          ?>">unpublish</a><?php
        endif;
      endif;
    endif; ?>
  </td><?php

  $output = ob_get_contents();
  ob_end_clean();

  return $output;
}

function td_actions_column_snd($post, $is_admin) {
  if ($is_admin) {

    if (url_contain('staff/posts/index')) {
      $url = 'staff/posts/index.php';
    } elseif (url_contain('staff/posts/published')) {
      $url = 'staff/posts/published.php';
    } elseif (url_contain('staff/posts/approved')) {
      $url = 'staff/posts/approved.php';
    }

    ob_start();

    ?><td scope="colgroup">

    <?php if (!$post->published): ?>
      <a class="btn-lk btn-lk--primary" href="<?php echo url_for($url . '?id=' . $post->id . '&cmd=publish')
      ?>">publish</a>

    <?php elseif ($post->published && !$post->approved): ?>
      <a class="btn-lk btn-lk--success" href="<?php echo url_for($url . '?id=' . $post->id . '&cmd=approve')
      ?>">prove</a>
    
    <?php elseif ($post->published && $post->approved): ?>
      <a class="btn-lk btn-lk--warning" href="<?php echo url_for($url . '?id=' . $post->id . '&cmd=disprove')
      ?>">disprove</a>

    <?php else: ?>
      <a class="btn btn-sm disabled text-center d-block">&ndash;</a>
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
    $output .= '<td class="text-secondary">draft</td>';
  } elseif ($post->published == 1 && $post->approved == 0) {
    $output .= '<td class="text-primary">published</td>';
  } elseif ($post->published == 1 && $post->approved == 1) {
    $output .= '<td class="text-success">approved</td>';
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

function td_post_topic($post) {
  ob_start();
  
  ?><td scope="col">
    <span class="h5 font-italic"><a href="<?php echo url_for('topic/' . u($post->topic) . '?id=' . $post->topic_id)
    ?>"><?php echo $post->topic ?></a></span>
  </td><?php

  $output = ob_get_contents();
  ob_end_clean();

  return $output;
}

function td_post_date($post) {
  ob_start();

  ?><td scope="col">
    <span class="h5"><?php echo date('M j, Y', strtotime($post->updated_at)) ?></span>
  </td><?php

  $output = ob_get_contents();
  ob_end_clean();

  return $output;
}