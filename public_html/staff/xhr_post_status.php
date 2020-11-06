<?php
use App\Classes\Post;

require_once '../../src/initialize.php';

if (!$session->isAuthor()) {
  exit;
}

$pathname = $_GET['pathname'] ?? "";
$key = $_GET['key'] ?? "";
$cmd = $_GET['cmd'] ?? "";
$pid = $_GET['pid'] ?? "";
$target = $key . '_' . $cmd;
$is_admin = $session->isAdmin();
$is_author = $session->isAuthor();
$access = accessType($pathname, $is_author, $is_admin);

switch($target) {
  case 'fst_col_unpublish':
        ($pid && $pathname) ? actions_column_fst($_GET, $is_admin, $access) : null;
        break;
  case 'snd_col_publish':
  case 'snd_col_approve':
  case 'snd_col_disapprove':
        ($pid && $pathname) ? actions_column_snd($_GET, $is_admin, $access) : null;
        break;
  default:
        exit(json_encode(['target' => 'error']));
}

function actions_column_fst($data, $is_admin, $access) {
  $post = Post::findById($data['pid']);

  if ($post) {
    require './posts/_common-posts-html.php';

    if ($post->setStatus($data['cmd'])) {
      if ($post->save()) {
        exit(json_encode([
          'success',
          td_actions_column_fst($post, $is_admin, url_for($data['pathname'])),
          td_actions_column_snd($post, $is_admin, url_for($data['pathname'])),
          td_post_status($post, $access)
        ]));
      }
    }
  }

  exit(json_encode(['failed']));
}

function actions_column_snd($data, $is_admin, $access) {
  $post = Post::findById($data['pid']);

  if ($post) {
    require './posts/_common-posts-html.php';

    if ($post->setStatus($data['cmd'])) {
      if ($post->save()) {
        exit(json_encode([
          'success',
          td_actions_column_fst($post, $is_admin, url_for($data['pathname'])),
          td_actions_column_snd($post, $is_admin, url_for($data['pathname'])),
          td_post_status($post, $access)
        ]));
      }
    }
  }

  exit(json_encode(['failed']));
}

function accessType($pathname, $is_author, $is_admin) {
  if ($is_author && trim($pathname) == 'staff/posts/index.php') {
    return 'own_post';
  } elseif ( $is_admin && (
      trim($pathname) == 'staff/posts/drafts.php' ||
      trim($pathname) == 'staff/posts/published.php' ||
      trim($pathname) == 'staff/posts/approved.php'
  )) {
    return 'user_post';
  }
  return "";
}

?>