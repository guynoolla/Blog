<?php
use App\Classes\Like;

require_once '../src/initialize.php';

$target = $_POST['target'] ?? '';

switch($target) {
  case 'like':
          user_like_post_handler($_POST);
  default:
          return false;
}

function user_like_post_handler($ajax) {
  global $session;

  // Check isLoggedIn >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
  if ($session->isLoggedIn()) {
    $like = new Like($ajax);
 
    if ($like->process($ajax['action'])) {
      exit(json_encode(['action' => $ajax['action'] . 'd']));
    } else {
      exit(json_encode(['action' => 'error']));
    }
  } // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Check isLoggedIn
}

?>