<?php
require_once '../src/initialize.php';

$target = $_POST['target'] ?? '';

switch($target) {
  case 'like':
          user_like_post_handler($_POST);
  default:
          return false;
}

function user_like_post_handler($ajax_post) {
  global $session;
  // Check isLoggedIn >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
  if ($session->isLoggedIn()) {
    $action = $ajax_post['action'] ?? '';
    $user_id = $ajax_post['user_id'] ?? 0;
    $post_id = $ajax_post['post_id'] ?? 0;
  
    if ($action == 'create') {
      $like = App\Classes\Like::getUserPostLike($user_id, $post_id);
      if (!$like) {
        $like = new App\Classes\Like($ajax_post);
        if ($like->save()) {
          exit(json_encode(['action' => 'created']));
        }
      }
      exit(json_encode(['action' => 'error']));

    } elseif ($action == 'delete') {
      $like = App\Classes\Like::getUserPostLike($user_id, $post_id);
      if ($like) {
        if ($like->delete()) {
          exit(json_encode(['action' => 'deleted']));
        }
      }
      exit(json_encode(['action' => 'error']));
    }
  } // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Check isLoggedIn
}

?>