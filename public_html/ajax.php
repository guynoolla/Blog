<?php

require_once '../src/initialize.php';

$target = $_POST['target'] ?? '';

switch($target) {
  case 'like':
        $session->isLoggedIn() ? user_like_post($_POST) : null;
  case 'posts_by_ids':
        $session->isLoggedIn() ? cookie_ids_posts($_POST) : null;
  default:
        exit(json_encode(['target' => 'error']));
}

function user_like_post($data) {
  global $session;

  $like = new \App\Classes\Like($data);

  if ($like->process($data['action'])) {
    exit(json_encode(['action' => $data['action'] . 'd'])); // created || deleted
  } else {
    exit(json_encode(['action' => 'error']));
  }
}

function cookie_ids_posts($data) {
  $ids = (array) json_decode($data['ids']);
  $uid = $data['user_id'];
  $per_page = $data['per_page'] ?? 0;
  $offset = $data['offset'] ?? 0;
  $page = $data['page'] ?? 0;

  foreach($ids as $id) {
    $like = new \App\Classes\Like([
      'post_id' => $id,
      'user_id' => $uid,
      'action' => 'create'
    ]);
    $like->process('create');
  }

  $likes = [];
  $likes = \App\Classes\Like::userLikesForLast30Days($uid);

  if (!empty($likes)) {

    $pids = [];
    foreach($likes as $like) {
      $pids[] = $like->post_id;
    }

    $total_count = \App\Classes\Post::countAll([
      'approved' => '1',
      ['id in (?)', implode(',', $pids)]
    ]);
    $pagination = new \App\Classes\Pagination($page, $per_page, $total_count);

    $posts = \App\Classes\Post::queryAllWhere($pids, $per_page, $offset);
    $arr = [];
    $j = 0;
  
    if ($posts) {
      foreach ($posts as $key => $post) {
        $arr[$j]['title'] = $post->title;
        $arr[$j]['excerpt'] = \App\Classes\Post::excerpt($post->body);
        $arr[$j]['created_at'] = date('F j, Y', strtotime($post->created_at));
        $arr[$j]['format'] = $post->format;
        if ($post->format == 'image') $arr[$j]['image'] = \App\Classes\Post::responsive($post->image);
        else if ($post->format == 'video') $arr[$j]['video'] = $post->getEntryVideo();
        $arr[$j]['topic'] = htmlspecialchars($post->topic);
        $arr[$j]['username'] = htmlspecialchars($post->username);
        $arr[$j]['to_single'] = url_for('post/' . urlencode($post->title) . '?id=' . $post->id);
        $arr[$j]['to_author'] = url_for('author/' . urlencode($post->username) . '?uid=' . $post->user_id);
        $arr[$j]['to_on_date'] = url_for('on-date/?pub=' . urlencode(date('Y-m-d', strtotime($post->created_at))));
        $arr[$j]['to_topic'] = url_for('topic/' . urlencode($post->topic) . '?tid=' . $post->topic_id);
        $j++;
      }
      $pag = [
        'total_count' => $total_count,
        'html' => $pagination->pageLinks('staff/index.php')
      ];
      exit(json_encode(['success', $arr, $pag]));
    }

  } else {
    exit(json_encode(['empty']));
  }
}

?>