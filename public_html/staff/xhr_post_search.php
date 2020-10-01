<?php
use App\Classes\User;
use App\Classes\Post;
use App\Classes\Topic;
use App\Classes\Pagination;

require_once '../../src/initialize.php';

$target = $_POST['target'] ?? "";

if (!$session->isLoggedIn()) {
  exit;
}

switch($target) {
  case 'own_post_by_search':
        own_post_data($_POST['data']);
  case 'own_post_by_status':
        own_post_data($_POST['data']);
  case 'own_post_by_topic':
        own_post_data($_POST['data']);
  case 'own_post_by_date':
        own_post_data($_POST['data']);
  case 'user_post_by_search':
        user_post_data($_POST['data']);
  case 'user_post_by_topic':
        user_post_data($_POST['data']);
  case 'user_post_by_author':
        user_post_data($_POST['data']);
  case 'user_post_by_date':
        user_post_data($_POST['data']);
  default:
        exit(json_encode(['target' => 'error']));
}

function user_post_data($data) {
  global $session;
  
  parse_str($data, $params);
  $type = $params['type'] ?? "";
  $value = $params['value'] ?? "";

  if (trim($params['pathname'], '/') == 'staff/posts/approved.php') {
    $status = 'approved';
    $cond_arr = [
      'approved' => '1',
    ];
    $cond_str = "approved = '1'";

  } else if (trim($params['pathname'], '/') == 'staff/posts/published.php') {
    $status = 'published';
    $cond_arr = [
      'published' => '1',
      'approved' => '0',
    ];
    $cond_str = "published = '1' AND approved = '0'"; 

  } else if (trim($params['pathname'], '/') == 'staff/posts/drafts.php') {
    $status = 'draft';
    $cond_arr = [
      'published' => '0',
    ];
    $cond_str = "published = '0'";
  }

  if ($type == 'author') {
    $total_count = Post::countAll(array_merge($cond_arr, [
      'user_id' => $value
    ]));

  } else if ($type == 'topic') {

    $total_count = Post::countAll(array_merge($cond_arr, [
      'user_id' => ['!=' => $session->getUserId()],
      'topic_id' => $value
    ]));

  } else if ($type == 'search') {
    
    if ($value != "") {  // search
      $total_count = Post::countAll(array_merge($cond_arr, [
        'user_id' => ['!=' => $session->getUserId()],
        ["( title LIKE '%?%' OR body LIKE '%?%' )", $value, $value]
      ]));

    } elseif ($value == "") { // default
      $total_count = Post::countAll(array_merge($cond_arr, [
        'user_id' => ['!=' => $session->getUserId()]
      ]));
    }

  } elseif ($type == 'date') {

    $created = date('Y-m-d', strtotime($value));
    $date_next = date('Y-m-d', strtotime('+ 1 day', strtotime($value)));

    $total_count = Post::countAll(array_merge($cond_arr, [
      'user_id' => ['!=' => $session->getUserId()],
      ["( created_at >= '?' AND created_at < '?' )", $created, $date_next]
    ]));

  } // conditions on $type

  $current_page = $params['page'] ?? 1;
  $per_page = DASHBOARD_PER_PAGE;
  $pagination = new Pagination($current_page, $per_page, $total_count);

  $sql = "SELECT p.*, t.id AS tid, t.name AS topic,";
  $sql .= " u.username, u.email AS user_email, u.email_confirmed AS ue_confirmed";
  $sql .= " FROM `posts` AS p";
  $sql .= " LEFT JOIN `users` AS u ON p.user_id = u.id";
  $sql .= " LEFT JOIN `topics` AS t ON p.topic_id = t.id";
  $sql .= " WHERE {$cond_str}";
  if ($type == 'author') {
    $sql .= " AND p.user_id = {$value}";
  } else {
    $sql .= " AND p.user_id != '{$session->getUserId()}'";
    if ($type == 'topic') {
      $sql .= " AND p.topic_id = {$value}";
    } else if ($type == 'search' && $value != "") {
      $sql .= " AND ( p.title LIKE '%$value%' OR p.body LIKE '%$value%' )";
    } else if ($type == 'date') {
      $sql .= " AND ( p.created_at >= '{$created}' AND p.created_at < '{$date_next}' )";
    }
  }
  $sql .= " ORDER BY p.updated_at DESC";
  $sql .= " LIMIT {$per_page} OFFSET {$pagination->offset()}";
  $posts = Post::findBySql($sql);
  
  $page_url = url_for($params['pathname']);

  require './posts/_common-posts-html.php';
  ob_start();

  if ($status == 'approved') {

    ?><table class="table table-bordered table-hover table-light <?php echo $table_size ?>">
      <thead class="bg-muted-lk text-muted">
        <tr>
          <th scope="col">#</th>
          <th scope="col">Title</th>
          <th scope="col">Topic</th>
          <th scope="col">Author</th>
          <th scope="col">Email</th>
          <th scope="col">Created</th>
          <th scope="colgroup" colspan="1">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($posts as $key => $post): ?>
          <tr>
            <th scope="row"><?php echo $key + 1 ?></th>
            <?php echo td_post_title($post) ?>
            <?php echo td_post_topic($post, $params['access']) ?>
            <?php echo td_post_author($post, $params['access']) ?>
            <?php echo td_post_author_email($post) ?>
            <?php echo td_post_date($post, $params['access']) ?>
            <?php echo td_actions_column_snd($post, $session->isAdmin(), $page_url); ?>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table><?php

  } else if ($status == 'published') { ?>

    <table class="table table-bordered table-hover table-light <?php echo $table_size ?>">
      <thead class="bg-muted-lk text-muted">
        <tr>
          <th scope="col">#</th>
          <th scope="col">Title</th>
          <th scope="col">Author</th>
          <th scope="col">Email</th>
          <th scope="col">Created</th>
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
    <table class="table table-bordered table-hover table-light <?php echo $table_size ?>">
      <thead class="bg-muted-lk text-muted">
        <tr>
          <th scope="col">#</th>
          <th scope="col">Title</th>
          <th scope="col">Topic</th>
          <th scope="col">Author</th>
          <th scope="col">Email</th>
          <th scope="col">Created</th>
          <th scope="col">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($posts as $key => $post): ?>
          <tr>
            <th scope="row"><?php echo $key + 1 ?></th><?php
            echo td_post_title($post);
            echo td_post_topic($post, $params['access']);
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

  } // <-- render table by status

  $output = ob_get_contents();
  ob_end_clean();

  $pag = [
    'total_count' => $total_count,
    'html' => $pagination->pageLinks($params['pathname'])
  ];

  if ($output) {
    exit(json_encode(['success', $output, $pag]));
  } else {
    exit(json_encode(['failed']));
  }

} // <-- user posts search function

function own_post_data($data) {
  global $session;

  parse_str($data, $params);
  $type = $params['type'] ?? "";
  $value = $params['value'] ?? "";

  if ($type == 'status') { // search by status

    if ($value ==  'draft') {
      $total_count = Post::countAll([
        'user_id' => $session->getUserId(),
        'published' => '0'
      ]);

    } else if ($value == 'published') {
      $total_count = Post::countAll([
        'user_id' => $session->getUserId(),
        'published' => '1',
        'approved' => '0'
      ]);

    } else if ($value == 'approved') {
      $total_count = Post::countAll([
        'user_id' => $session->getUserId(),
        'approved' => '1'
      ]);        
    }

  } else if ($type == 'topic') { // search by topic

    $total_count = Post::countAll([
      'user_id' => $session->getUserId(),
      'topic_id' => $value
    ]);

  } elseif ($type == 'search') { // search by term
    
    if ($value != "") { // search
      $total_count = Post::countAll([
        'user_id' => $session->getUserId(),
        ["( title LIKE '%?%' OR body LIKE '%?%' )", $value, $value]
      ]);

    } elseif ($value == "") { // default
      $total_count = Post::countAll([
        'user_id' => $session->getUserId(),
      ]);
    }

  } elseif ($type == 'date') {

    $created = date('Y-m-d', strtotime($value));
    $date_next = date('Y-m-d', strtotime('+ 1 day', strtotime($value)));

    $total_count = Post::countAll([
      'user_id' => $session->getUserId(),
      ["( created_at >= '?' AND created_at < '?' )", $created, $date_next]
    ]);

  } // conditions on $type

  $current_page = $params['page'] ?? 1;
  $per_page = DASHBOARD_PER_PAGE;
  $pagination = new Pagination($current_page, $per_page, $total_count);
  
  $sql = "SELECT p.*, u.username, t.id AS tid, t.name AS topic";
  $sql .= " FROM `posts` AS p";
  $sql .= " LEFT JOIN `users` AS u ON p.user_id = u.id";
  $sql .= " LEFT JOIN `topics` AS t ON p.topic_id = t.id";
  $sql .= " WHERE p.user_id='{$session->getUserId()}'";
  if ($type == 'status') {
    if ($value ==  'draft') {
      $sql .= " AND p.published = '0'";
    } else if ($value == 'published') {
      $sql .= " AND p.published = '1' AND p.approved = '0'";
    } else if ($value == 'approved') {
      $sql .= " AND p.approved = '1'";
    }
  } else if ($type == 'topic') {
    $sql .= " AND p.topic_id = {$value}";
  } else if ($type == 'search' && $value != "") {
    $sql .= " AND ( p.title LIKE '%$value%' OR p.body LIKE '%$value%' )";
  } else if ($type == 'date') {
    $sql .= " AND ( p.created_at >= '{$created}' AND p.created_at < '{$date_next}' )";
  }
  $sql .= " ORDER BY p.updated_at DESC";
  $sql .= " LIMIT {$per_page} OFFSET {$pagination->offset()}";
  $posts = Post::findBySql($sql);

  $page_url = url_for($params['pathname']);

  require './posts/_common-posts-html.php';
  ob_start();

  ?><table class="table table-bordered table-hover table-light <?php echo $table_size ?>">
    <thead class="bg-muted-lk text-muted">
      <tr>
        <th scope="col">#</th>
        <th scope="col">Title</th>
        <th scope="col">Topic</th>
        <th scope="col">Status</th>
        <th scope="col">Created</th>
        <th scope="colgroup" colspan="3">Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($posts as $key => $post): ?>
        <tr>
          <th scope="row"><?php echo $key + 1 ?></th>
          <?php echo td_post_title($post) ?>
          <?php echo td_post_topic($post, $params['access']) ?>
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

  if ($output) {
    exit(json_encode(['success', $output, $pag]));
  } else {
    exit(json_encode(['failed']));
  }

} // <-- own posts search function