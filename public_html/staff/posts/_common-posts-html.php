<?php

$header_mb = 'mb-1'; 
$table_size = 'table-md';

function td_actions_column_fst($post, $is_admin=false, $url="") {

  if (!$url) {
    if (url_contain('staff/posts/index')) {
      $url = 'staff/posts/index.php';
    } elseif (url_contain('staff/posts/published')) {
      $url = 'staff/posts/published.php';
    } elseif (url_contain('staff/post/approved')) {
      $url = 'staff/posts/approved.php';
    }
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

function td_actions_column_snd($post, $is_admin, $url="") {
  if ($is_admin) {

    if (!$url) {
      if (url_contain('staff/posts/index')) {
        $url = 'staff/posts/index.php';
      } elseif (url_contain('staff/posts/published')) {
        $url = 'staff/posts/published.php';
      } elseif (url_contain('staff/posts/approved')) {
        $url = 'staff/posts/approved.php';
      }
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

function td_post_author($post, $access) {
  return
    "<td><a href=\"#{$post->username}\" class=\"click-load\" data-type=\"author\" data-value=\"{$post->user_id}\" data-access=\"{$access}\">{$post->username}</a></td>";
}

function td_post_author_email($post) {
  return
    "<td><a href=\"mailto:{$post->user_email}\" class=\"" . ($post->ue_confirmed ? 'text-success' : '') . "\">{$post->user_email}</a></td>";
}

function td_post_status($post, $access) {
  $output = '';

  if ($post->published == 0) {
    $output .= "<td class=\"text-secondary\"><a href=\"#draft\" class=\"click-load\" data-type=\"status\" data-value=\"draft\" data-access=\"{$access}\">draft</a></td>";
  } elseif ($post->published == 1 && $post->approved == 0) {
    $output .= "<td class=\"text-primary\"><a href=\"#published\" class=\"click-load\" data-type=\"status\" data-value=\"published\" data-access=\"{$access}\">published</a></td>";
  } elseif ($post->published == 1 && $post->approved == 1) {
    $output .= "<td class=\"text-success\"><a href=\"#approved\" class=\"click-load\" data-type=\"status\" data-value=\"approved\" data-access=\"{$access}\">approved</a></td>";
  }

  return $output;
}

function td_post_topic($post, $access) {
  return "<td scope=\"col\">
    <span class=\"h5 font-italic\"><a href=\"#{u($post->topic)}\" class=\"click-load\"
      data-type=\"topic\" data-value=\"{$post->topic_id}\" data-access=\"{$access}\"
    >{$post->topic}</a></span>
  </td>";
}

function td_post_date($post, $access) {
  return
    "<td scope=\"col\"><a href=\"#ondate\" data-type=\"date\" data-value=\"{$post->created_at}\" data-access=\"{$access}\" class=\"click-load h5\">" . date('M j, Y', strtotime($post->created_at)) . "</a></td>";
}

?>