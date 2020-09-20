<?php

require_once '../src/initialize.php';

$target = $_POST['target'] ?? '';

switch($target) {
  case 'like':
        $session->isLoggedIn() ? user_like_post($_POST) : null;
  case 'posts_by_ids':
        $session->isLoggedIn() ? cookie_ids_posts($_POST) : null;
  case 'contact_form':
        contact_form_submit($_POST);
  default:
        exit(json_encode(['target' => 'error']));
}

function contact_form_submit($data) {
  $email = $data['email'] ?? '';
  $message = $data['message'] ?? '';

  if ($email && $message) {
    $mailer = new \App\Contracts\Mailer;
    $text = strip_tags($message);
    
    $mailer->send(ADMIN_EMAIL,'Contact Form', $text, $message);
    $message = 'Thank you for your message!';
    
    exit(json_encode(['success', $message]));
  }
}

function user_like_post($data) {
  $post_id = $data['post_id'] ?? 0;
  $user_id = $data['user_id'] ?? 0;
  $action = $data['action'] ?? "";

  if ($post_id && $user_id && $action) {
    $like = \App\Classes\Like::get($post_id, $user_id);

    if ($like->process($action)) {
      exit(json_encode(['action' => $action . 'd'])); // created || deleted
    } else {
      exit(json_encode(['action' => 'error']));
    }
  }
}

function cookie_ids_posts($data) {
  $ids = (array) json_decode($data['ids']);
  $uid = $data['user_id'];
  $per_page = $data['per_page'] ?? 0;
  $offset = $data['offset'] ?? 0;
  $page = $data['page'] ?? 0;

  $likes = \App\Classes\Like::userLikesForLast30Days($uid);
  $liked_ids = [];

  if (is_array($likes)) {
    foreach($likes as $like) {
      $liked_ids[] = $like->post_id;
    }
  }

  if (is_array($ids)) {
    $pids = array_merge($liked_ids, $ids);
    $pids = array_unique($pids);
  }

  if (!empty($pids)) {

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
        $arr[$j]['to_on_date'] = url_for('ondate/pub/?ymd=' . urlencode(date('Y-m-d', strtotime($post->created_at))));
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