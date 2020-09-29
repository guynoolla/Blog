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
  case 'own_post_by_status':
        own_post_by_status($_POST['data']);
  case 'user_post_by_status':
        user_post_by_status($_POST['data']);
  default:
        exit(json_encode(['target' => 'error']));
}

function own_post_by_status($data) {
  global $session;
  parse_str($data, $params);
  $term = $params['s'] ?? "";
  
  if ($params['status'] == 'any') {
    $current_page = $_GET['page'] ?? 1;
    $per_page = DASHBOARD_PER_PAGE;
    $total_count = Post::countAll([
      'user_id' => $session->getUserId(),
      ["( title LIKE '%?%' OR body LIKE '%?%' )", $term, $term]
    ]);
    $pagination = new Pagination($current_page, $per_page, $total_count);
    
    $sql = "SELECT p.*, u.username, t.id AS tid, t.name AS topic";
    $sql .= " FROM `posts` AS p";
    $sql .= " LEFT JOIN `users` AS u ON p.user_id = u.id";
    $sql .= " LEFT JOIN `topics` AS t ON p.topic_id = t.id";
    $sql .= " WHERE p.user_id='{$session->getUserId()}'";
    $sql .= " AND ( title LIKE '%$term%' OR body LIKE '%$term%' )";
    $sql .= " ORDER BY p.updated_at DESC";
    $sql .= " LIMIT {$per_page}";
    $sql .= " OFFSET {$pagination->offset()}";
    $posts = Post::findBySql($sql);    
  }

  require './posts/_common-posts-html.php';
  ob_start();

  ?><table class="table table-bordered table-hover table-light <?php echo $table_size ?>">
    <thead class="bg-muted-lk text-muted">
      <tr>
        <th scope="col">#</th>
        <th scope="col">Title</th>
        <th scope="col">Topic</th>
        <th scope="col">Status</th>
        <th scope="col">Edited</th>
        <th scope="colgroup" colspan="3">Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($posts as $key => $post): ?>
        <tr>
          <th scope="row"><?php echo $key + 1 ?></th>
          <?php echo td_post_title($post) ?>
          <?php echo td_post_topic($post) ?>
          <?php echo td_post_status($post) ?>
          <?php echo td_post_date($post) ?>
          <?php echo td_actions_column_fst($post, $session->isAdmin(), url_for('staff/posts/index')) ?>
          <?php echo td_actions_column_snd($post, $session->isAdmin(), url_for('staff/posts/index')) ?>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table><?php

  $output = ob_get_contents();
  ob_end_clean();

  $output = $output ? $output : "false";
  $pag = $pagination->pageLinks('staff/posts/index.php');
  $page = $pag ? $pag : "false";
  
  if ($output) {
    exit(json_encode(['success', $output, $pag]));
  } else {
    exit(json_encode(['failed']));
  }
}

function user_post_by_status($data) {
  global $session;
  parse_str($data, $params);
  $term = $params['s'] ?? "";

  if ($params['status'] == 'approved') {
    $current_page = 1;
    $per_page = DASHBOARD_PER_PAGE;
    $total_count = Post::countAll([
      'published' => '1',
      'user_id' => ['!=' => $session->getUserId()],
      ["( title LIKE '%?%' OR body LIKE '%?%' )", $term, $term]
    ]);
    $pagination = new Pagination($current_page, $per_page, $total_count);

    $current_page = $_GET['page'] ?? 1;
    $per_page = DASHBOARD_PER_PAGE;
    $total_count = Post::countAll([
      'approved' => '1',
      'user_id' => ['!=' => $session->getUserId()],
      ["( title LIKE '%?%' OR body LIKE '%?%' )", $term, $term]
    ]);
    $pagination = new Pagination($current_page, $per_page, $total_count);
  
    $sql = "SELECT p.*, u.username, t.id AS tid, t.name AS topic";
    $sql .= " FROM `posts` AS p";
    $sql .= " LEFT JOIN `users` AS u ON p.user_id = u.id";
    $sql .= " LEFT JOIN `topics` AS t ON p.topic_id = t.id";
    $sql .= " WHERE p.approved='1'";
    $sql .= " AND p.user_id != '{$session->getUserId()}'";
    $sql .= " AND ( title LIKE '%$term%' OR body LIKE '%$term%' )";
    $sql .= " ORDER BY p.updated_at DESC";
    $sql .= " LIMIT {$per_page} OFFSET {$pagination->offset()}";
    $posts = Post::findBySql($sql);
   
    require './posts/_common-posts-html.php';
    ob_start();

    ?><table class="table table-bordered table-hover table-light table-md">
      <thead class="bg-muted-lk text-muted">
        <tr>
          <th scope="col">#</th>
          <th scope="col">Title</th>
          <th scope="col">Topic</th>
          <th scope="col">Author</th>
          <th scope="col">Status</th>
          <th scope="col">Edited</th>
          <th scope="colgroup" colspan="1">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($posts as $key => $post): ?>
          <tr>
            <th scope="row"><?php echo $key + 1 ?></th>
            <?php echo td_post_title($post) ?>
            <?php echo td_post_topic($post) ?>
            <td><?php echo (User::findById($post->user_id))->username ?></td>
              <?php
              if ($post->published == 0): ?>
                <td class="text-secondary font-weight-bold">draft</td><?php
              elseif ($post->published == 1 && $post->approved == 0): ?>
                <td class="text-danger font-weight-bold">published</td><?php
              elseif ($post->published == 1 && $post->approved == 1): ?>
                <td class="text-success font-weight-bold">approved</td><?php
              endif; ?>
            </td>
            <?php echo td_post_date($post) ?>
            <?php echo td_actions_column_snd($post, $session->isAdmin(), url_for('staff/post/approved')); ?>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table><?php

    $output = ob_get_contents();
    ob_end_clean();

    $output = $output ? $output : "false";
    $pag = $pagination->pageLinks('staff/posts/approved/index.php');
    $page = $pag ? $pag : "false";
    
    if ($output) {
      exit(json_encode(['success', $output, $pag]));
    } else {
      exit(json_encode(['failed']));
    }
  }
}