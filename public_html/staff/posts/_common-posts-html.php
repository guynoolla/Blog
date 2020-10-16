<?php

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

  ?><td scope="colgroup"><?php 
    if (!$post->published):
      
      ?><a class="btn-lk btn-lk--secondary" href="<?php echo url_for($url . '?id=' . $post->id . '&cmd=edit')
      ?>" data-key="fst_col" data-cmd="false" data-pid="<?php echo $post->id
      ?>">edit</a><?php
    
    else:
      if (!$is_admin):

        if ($post->approved): ?>
          <a class="btn-lk btn-lk--light disabled text-center d-block"
            data-key="fst_col" data-cmd="false" data-pid="<?php echo $post->id ?>"
          ><small class="text-dark">&ndash;</small></a><?php
        else: ?>
          <a class="btn-lk btn-lk--primary" href="<?php echo url_for($url . '?id=' . $post->id . '&cmd=publish')
          ?>" data-key="fst_col" data-cmd="unpublish" data-pid="<?php echo $post->id
          ?>">unpublish</a><?php
        endif;

      else:

        if ($post->approved): ?>
          <a data-key="fst_col" data-cmd="false" data-pid="<?php echo $post->id
          ?>" class="btn btn-sm disabled text-center d-block">&ndash;</a><?php
        else: ?>
          <a class="btn-lk btn-lk--primary" href="<?php echo url_for($url . '?id=' . $post->id . '&cmd=unpublish')
          ?>" data-key="fst_col" data-cmd="unpublish" data-pid="<?php echo $post->id ?>">unpublish</a><?php
        endif;

      endif;
    endif;
  ?></td><?php

  return ob_get_clean();
}

function td_actions_column_snd($post, $is_admin, $url="") {

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
    ?>" data-key="snd_col" data-cmd="publish" data-pid="<?php echo $post->id ?>">publish</a>

  <?php elseif ($is_admin && $post->published && !$post->approved): ?>
    <a class="btn-lk btn-lk--success" href="<?php echo url_for($url . '?id=' . $post->id . '&cmd=approve')
    ?>" data-key="snd_col" data-cmd="approve" data-pid="<?php echo $post->id ?>">approve</a>
  
  <?php elseif ($is_admin && $post->published && $post->approved): ?>
    <a class="btn-lk btn-lk--warning" href="<?php echo url_for($url . '?id=' . $post->id . '&cmd=disapprove')
    ?>" data-key="snd_col" data-cmd="disapprove" data-pid="<?php echo $post->id ?>">disapprove</a>

  <?php else: ?>
    <a data-key="snd_col" data-cmd="false" data-pid="<?php echo $post->id ?>" class="btn btn-sm btn--text disabled">
      <?php echo $post->published ? 'awaiting <br>approval' : '&ndash;' ?>
    </a>
  <?php endif; ?>
  
  </td><?php

  return ob_get_clean();
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

  return ob_get_clean();
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
    $output .= "<td class=\"text-secondary\" data-pid=\"{$post->id}\"><a href=\"#draft\" class=\"click-load\" data-type=\"status\" data-value=\"draft\" data-access=\"{$access}\">draft</a></td>";
  } elseif ($post->published == 1 && $post->approved == 0) {
    $output .= "<td class=\"text-primary\" data-pid=\"{$post->id}\"><a href=\"#published\" class=\"click-load\" data-type=\"status\" data-value=\"published\" data-access=\"{$access}\">published</a></td>";
  } elseif ($post->published == 1 && $post->approved == 1) {
    $output .= "<td class=\"text-success\" data-pid=\"{$post->id}\"><a href=\"#approved\" class=\"click-load\" data-type=\"status\" data-value=\"approved\" data-access=\"{$access}\">approved</a></td>";
  }

  return $output;
}

function td_post_category($post, $access) {
  return "<td scope=\"col\">
    <span class=\"h5 font-italic\"><a href=\"#{u($post->category)}\" class=\"click-load\"
      data-type=\"category\" data-value=\"{$post->category_id}\" data-access=\"{$access}\"
    >{$post->category}</a></span>
  </td>";
}

function td_post_date($post, $access) {
  if ($access == 'user_post') {
    if ($post->published == '1' || $post->approved == '1') {
      $date = $post->published_at;
    } elseif ($post->published == '0') {
      $date = $post->updated_at;
    }
  } elseif ($access == 'own_post') {
    $date = $post->updated_at;
  }

  return
    "<td scope=\"col\"><a href=\"#ondate\" data-type=\"date\" data-value=\"{$date}\" data-access=\"{$access}\" class=\"click-load h5\">" . date('M j, Y', strtotime($date)) . "</a></td>";
}

?>