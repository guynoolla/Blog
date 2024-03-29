<?php
use App\Classes\Post;
use App\Classes\Pagination;

require_once '../../src/initialize.php';

if (!$session->isLoggedIn()) {
  exit;
}

switch($_GET['target']) {
  case 'own_post_by_search':
  case 'own_post_by_status':
  case 'own_post_by_category':
  case 'own_post_by_date_order':
  case 'own_post_by_title_order':
  case 'own_post_by_date':
          $session->isAuthor() ? own_post_data($_GET['data']) : null;
          break;
  case 'user_post_by_date_order':
  case 'user_post_by_title_order':
  case 'user_post_by_author_order':
  case 'user_post_by_search':
  case 'user_post_by_category':
  case 'user_post_by_author':
  case 'user_post_by_date':
          $session->isAdmin() ? user_post_data($_GET['data']) : null;
          break;
  default:
          exit(json_encode(['target' => 'error']));
}

/*
 -- Admin search user post data -------------------------------------------- */

function user_post_data($data) {
  global $session;
  
  parse_str($data, $params);
  $type = $params['type'] ?? "";
  $value = $params['value'] ?? "";

  if (trim($params['pathname'], '/') == 'staff/posts/approved.php') {
    $status = 'approved';
    $cond_arr = ['approved' => '1'];
    $cond_str = "p.approved = '1'";
    $date_attr = 'published_at';

  } else if (trim($params['pathname'], '/') == 'staff/posts/published.php') {
    $status = 'published';
    $cond_arr = ['published' => '1','approved' => '0'];
    $cond_str = "p.published = '1' AND p.approved = '0'"; 
    $date_attr = 'published_at';

  } else if (trim($params['pathname'], '/') == 'staff/posts/drafts.php') {
    $status = 'draft';
    $cond_arr = ['published' => '0'];
    $cond_str = "p.published = '0'";
    $date_attr = 'updated_at';
  }

  if ($type == 'author') {
    $cond_arr = array_merge(['user_id' => $value], $cond_arr);
    $cond_str .= " AND p.user_id = {$value}";

  } else {
    $cond_arr = array_merge(
      ['user_id' => ['!=' => $session->getUserId()]],
      $cond_arr     
    );
    $cond_str .= " AND p.user_id != {$session->getUserId()}";
  }

  if ($type == 'category') {
    $cond_arr = array_merge(['category_id' => $value], $cond_arr);
    $cond_str .= " AND p.category_id = {$value}";

  } else if ($type == 'search') {
    if ($value != "") {
      $cond_arr = array_merge(
        [["( title LIKE '%?%' OR body LIKE '%?%' )", $value, $value]],
        $cond_arr
      );
      $cond_str .= " AND (p.title LIKE '%{$value}%' OR p.body LIKE '%{$value}%')";
    } else {
      // no conditions to add
    }

  } elseif ($type == 'date') {
    $valdate = date('Y-m-d', strtotime($value));
    $nextday = date('Y-m-d', strtotime('+ 1 day', strtotime($value)));
    $cond_arr = array_merge(
      [["({$date_attr} >= '?' AND {$date_attr} < '?')", $valdate, $nextday]],
      $cond_arr
    );
    $cond_str .= " AND (p.{$date_attr} >= '{$valdate}' AND p.{$date_attr} < '{$nextday}')";

  } elseif ($type == 'date_order' || $type == 'title_order' || $type == 'author_order') {

    $cond_arr = $cond_arr;
    $cond_str = $cond_str;
  }

  $total_count = Post::countAll($cond_arr);
  $current_page = $params['page'] ?? 1;
  $per_page = DASHBOARD_PER_PAGE;
  $pagination = new Pagination($current_page, $per_page, $total_count);

  $order = (strtoupper($value) == 'ASC') ? 'DESC' : 'ASC';

  $sql = "SELECT p.*, t.id AS tid, t.name AS category,";
  $sql .= " u.username, u.email AS user_email, u.email_confirmed AS ue_confirmed";
  $sql .= " FROM `posts` AS p";
  $sql .= " LEFT JOIN `users` AS u ON p.user_id = u.id";
  $sql .= " LEFT JOIN `categories` AS t ON p.category_id = t.id";
  $sql .= $cond_str ? " WHERE {$cond_str}" : "";
  if ($type == 'date_order') $sql .= " ORDER BY p.{$date_attr} {$order}"; 
  else if ($type == 'title_order') $sql .= " ORDER BY p.title {$order}";
  else if ($type == 'author_order') $sql .= " ORDER BY u.username {$order}";
  else $sql .= " ORDER BY p.published_at DESC";
  $sql .= " LIMIT {$per_page} OFFSET {$pagination->offset()}";
  $posts = Post::findBySql($sql);
  
  $page_url = url_for($params['pathname']);
  require './posts/_common-posts-html.php';

  ob_start();

  if ($status == 'approved') {

    ?><table class="table table-bordered table-hover table-light <?php echo TABLE_SIZE ?>">
      <thead class="bg-muted-lk text-muted">
        <tr>
          <th scope="col">#</th>
          <th scope="col"><a href="#title" class="click-load" data-access="user_post" data-value="<?php echo $order ?>" data-type="title_order">Title</a></th>
          <th scope="col">Category</th>
          <th scope="col"><a href="#author" class="click-load" data-access="user_post" data-value="<?php echo $order ?>" data-type="author_order">Author</a></th>
          <th scope="col">Email</th>
          <th scope="col"><a href="#created" class="click-load" data-access="user_post" data-value="<?php echo $order ?>" data-type="date_order">Published</a></th>
          <th scope="colgroup" colspan="1">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($posts as $key => $post): ?>
          <tr>
            <th scope="row"><?php echo $key + 1 ?></th>
            <?php echo td_post_title($post) ?>
            <?php echo td_post_category($post, $params['access']) ?>
            <?php echo td_post_author($post, $params['access']) ?>
            <?php echo td_post_author_email($post) ?>
            <?php echo td_post_date($post, $params['access']) ?>
            <?php echo td_actions_column_snd($post, $session->isAdmin(), $page_url); ?>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table><?php

  } else if ($status == 'published') { ?>

    <table class="table table-bordered table-hover table-light <?php echo TABLE_SIZE ?>">
      <thead class="bg-muted-lk text-muted">
        <tr>
          <th scope="col">#</th>
          <th scope="col"><a href="#title" class="click-load" data-access="user_post" data-value="<?php echo $order ?>" data-type="title_order">Title</a></th>
          <th scope="col"><a href="#author" class="click-load" data-access="user_post" data-value="<?php echo $order ?>" data-type="author_order">Author</a></th>
          <th scope="col">Email</th>
          <th scope="col"><a href="#created" class="click-load" data-access="user_post" data-value="<?php echo $order ?>" data-type="date_order">Published</a></th>
          <th scope="colgroup" colspan="2">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($posts as $key => $post): ?>
          <tr>
            <th scope="row"><?php echo $key + 1 ?></th><?php
            echo td_post_title($post);
            echo td_post_author($post, $params['access']);
            echo td_post_author_email($post);
            echo td_post_date($post, $params['access']);
            echo td_actions_column_fst($post, $session->isAdmin(), $page_url);
            echo td_actions_column_snd($post, $session->isAdmin(), $page_url);
          ?></tr>
        <?php endforeach; ?>
      </tbody>
    </table><?php

  } else if ($status == 'draft') { ?>

    <div class="loadPostsJS" data-access="user_post">
    <table class="table table-bordered table-hover table-light <?php echo TABLE_SIZE ?>">
      <thead class="bg-muted-lk text-muted">
        <tr>
          <th scope="col">#</th>
          <th scope="col"><a href="#title" class="click-load" data-access="user_post" data-value="<?php echo $order ?>" data-type="title_order">Title</a></th>
          <th scope="col">Category</th>
          <th scope="col"><a href="#author" class="click-load" data-access="user_post" data-value="<?php echo $order ?>" data-type="author_order">Author</a></th>
          <th scope="col">Email</th>
          <th scope="col"><a href="#created" class="click-load" data-access="user_post" data-value="<?php echo $order ?>" data-type="date_order">Updated</a></th>
          <th scope="col">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($posts as $key => $post): ?>
          <tr>
            <th scope="row"><?php echo $key + 1 ?></th><?php
            echo td_post_title($post);
            echo td_post_category($post, $params['access']);
            echo td_post_author($post, $params['access']);
            echo td_post_author_email($post);
            echo td_post_date($post, $params['access']); ?>
            <td><a class="btn-lk btn-lk--secondary" href="<?php
              echo url_for('staff/posts/edit.php?id=' . $post->id)
            ?>">Edit</a></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table><?php

  } // <-- render table by post status

  $output = ob_get_contents();
  ob_end_clean();

  $pag = [
    'total_count' => $total_count,
    'html' => $pagination->pageLinks($params['pathname'])
  ];

  if ($output) exit(json_encode(['success', $output, $pag]));
  else exit(json_encode(['failed']));

} // <-- function

/*
 -- Author search own post data ----------------------------------------------*/

function own_post_data($data) {
  global $session;

  parse_str($data, $params);
  $type = $params['type'] ?? "";
  $value = $params['value'] ?? "";

  if ($type == 'status') {

    if ($value ==  'draft') {
      $cond_arr = ['published' => '0'];
      $cond_str = "p.published = '0'";

    } else if ($value == 'published') {
      $cond_arr = ['published' => '1','approved' => '0'];
      $cond_str = "p.published = '1' AND p.approved = '0'";

    } else if ($value == 'approved') {
      $cond_arr = ['approved' => '1'];
      $cond_str = "p.approved = '1'"; 
    }

  } else if ($type == 'category') {

    $cond_arr = ['category_id' => $value];
    $cond_str = "p.category_id = {$value}";

  } elseif ($type == 'search') {
    
    if ($value != "") {
      $cond_arr = [
        ["(title LIKE '%?%' OR body LIKE '%?%')", $value, $value]
      ];
      $cond_str = "(p.title LIKE '%{$value}%' OR p.body LIKE '%{$value}%')";
    } else {
      $cond_arr = [];
      $cond_str = "";
    }

  } elseif ($type == 'date') {

    $updated = date('Y-m-d', strtotime($value));
    $nextday = date('Y-m-d', strtotime('+ 1 day', strtotime($value)));
    $cond_arr = [
      ["(updated_at >= '?' AND updated_at < '?')", $updated, $nextday]
    ];
    $cond_str = "(p.updated_at >= '{$updated}' AND p.updated_at < '{$nextday}')";

  } elseif ($type == 'date_order' || $type == 'title_order') {

    $cond_arr = [];
    $cond_str = "";
  }

  $cond = array_merge(['user_id' => $session->getUserId()], $cond_arr);
  $total_count = Post::countAll($cond);
  $current_page = $params['page'] ?? 1;
  $per_page = DASHBOARD_PER_PAGE;
  $pagination = new Pagination($current_page, $per_page, $total_count);

  $order = (strtoupper($value) == 'ASC') ? 'DESC' : 'ASC';
  
  $sql = "SELECT p.*, u.username, t.id AS tid, t.name AS category";
  $sql .= " FROM `posts` AS p";
  $sql .= " LEFT JOIN `users` AS u ON p.user_id = u.id";
  $sql .= " LEFT JOIN `categories` AS t ON p.category_id = t.id";
  $sql .= " WHERE p.user_id='{$session->getUserId()}'";
  $sql .= $cond_str ? " AND {$cond_str}" : "";
  if ($type == 'date_order') $sql .= " ORDER BY p.updated_at {$order}";
  else if ($type == 'title_order') $sql .= " ORDER BY p.title {$order}";
  else $sql .= " ORDER BY p.updated_at DESC";
  $sql .= " LIMIT {$per_page} OFFSET {$pagination->offset()}";
  $posts = Post::findBySql($sql);

  $page_url = url_for($params['pathname']);
  require './posts/_common-posts-html.php';

  ob_start();

  ?><table class="table table-bordered table-hover table-light <?php echo TABLE_SIZE ?>">
    <thead class="bg-muted-lk text-muted">
      <tr>
        <th scope="col">#</th>
        <th scope="col"><a href="#title" class="click-load"  data-access="own_post" data-value="<?php echo $order ?>" data-type="title_order">Title</a></th>
        <th scope="col">Category</th>
        <th scope="col">Status</th>
        <th scope="col"><a href="#created" class="click-load" data-access="own_post" data-value="<?php echo $order ?>" data-type="date_order">Updated</a></th>
        <th scope="colgroup" colspan="3">Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($posts as $key => $post): ?>
      <tr>
        <th scope="row"><?php echo $key + 1 ?></th>
        <?php echo td_post_title($post) ?>
        <?php echo td_post_category($post, $params['access']) ?>
        <?php echo td_post_status($post, $params['access']) ?>
        <?php echo td_post_date($post, $params['access']) ?>
        <?php echo td_actions_column_fst($post, $session->isAdmin(), $page_url) ?>
        <?php echo td_actions_column_snd($post, $session->isAdmin(), $page_url) ?>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table><?php

  $output = ob_get_contents();
  ob_end_clean();
 
  $pag = [
    'total_count' => $total_count,
    'html' => $pagination->pageLinks($params['pathname'])
  ];

  if ($output) exit(json_encode(['success', $output, $pag]));
  else exit(json_encode(['failed']));

} // <-- function