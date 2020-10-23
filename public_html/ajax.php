<?php

require_once '../src/initialize.php';

$target = $_POST['target'] ?? '';

switch($target) {
  case 'like':
        $session->isLoggedIn() ? user_like_post($_POST) : null;
        break;
  case 'posts_by_ids':
        $session->isLoggedIn() ? cookie_ids_posts($_POST) : null;
        break;
  case 'contact_form':
        contact_form_submit($_POST);
        break;
  case 'is_already_exist':
        is_already_exist($_POST);
        break;
  case 'validate_captcha':
        validate_captcha($_POST);
        break;
  case 'user_site_data':
        $session->isAdmin() ? user_site_data($_POST) : null;
  default:
        exit(json_encode(['target' => 'error']));
}

function user_site_data($data) {
  $json = $data['json'] ?? false;
  $path = PUBLIC_PATH . '/staff/site/user-site-data.json';
  //$path2 = PUBLIC_PATH . '/user-site-data.json';
  
  if ($json == "false") {
    exit(json_encode(['okey', file_get_contents($path)]));
  
  } else {
    list ($is_json, $data) = is_json($json);
  
    if ($is_json) {
      $err_code = site_setting_removed($data, $path);
      $err1_msg = "To remove site settings property is forbidden, please reload data!";
      $err2_msg = "To add a new settings property is not allowed, please reload data!";
      $err_msg = [];
      if ($err_code == 1) $err_msg[] = $err1_msg;
      if ($err_code == 2) $err_msg[] = $err2_msg;
      if ($err_code == 3) { $err_msg[] = $err1_msg; $err_msg[] = $err2_msg; }

      if ($err_code > 0) {
        exit(json_encode(['error', $err_msg]));
      
      } else {
        $data = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        if (file_put_contents($path, $data) !== false) {
          exit(json_encode(['done', file_get_contents($path)]));
        } else {
          exit(json_encode(['error','Server failed to update json file.']));
        }
      }

    } else {
      exit(json_encode(['error','Submitted data is not a true json string.']));
    }
  } 
}

function is_already_exist($data) {
  global $session;

  $field = $data['field'] ?? '';
  $value = $data['value'] ?? '';
  $table = $data['table'] ?? '';

  if ($field && $value && $table) {
    if ($table == 'users') {
      $uid = $session->isLoggedIn() ? $session->getUserId() : "0";

      if ($field == 'username') {
        $unique = has_unique_username($value, $uid);
      } else if ($field == 'email') {
        $unique = has_unique_email($value, $uid);
      }
      exit(($unique ? 'true' : 'false'));
    }
  }
  exit('error');
}

function contact_form_submit($data) {
  global $jsonstore;

  $email = $data['email'] ?? '';
  $message = $data['message'] ?? '';
  $captcha = $data['captcha'] ?? '';

  if ($email && $message && $captcha) {

    if (isset($_SESSION['captcha_valid'])) {
      $valid = $_SESSION['captcha_valid'];
      unset($_SESSION['captcha_valid']);
    } else {
      $valid = "";
    }

    if (strtoupper($captcha) == strtoupper($valid)) {
      $mailer = new \App\Contracts\Mailer;
      $text = strip_tags($message);
      try {
        $mailer->send(ADMIN_EMAIL, $jsonstore->siteName, $text, $message);
        $status = 'success';
        $alert = $jsonstore->contactForm->alertSuccess;
      
      } catch(Exception $e) {
        $status = 'failed';
        $alert = 'Sorry, server error occured. Please try again later.';
      }

      include("./simple-php-captcha.php");
      $_SESSION['captcha'] = simple_php_captcha();
      $image_src = $_SESSION['captcha']['image_src'];
  
      exit(json_encode([$status, ['alert' => $alert, 'image_src' => $image_src]]));
    }

    exit(json_encode(['error', ['alert' => 'Something went wrong.']]));;
  }
}

function validate_captcha($data) {
  $captcha = $data['captcha'] ?? '';

  if ($captcha) {
    if (strtoupper($captcha) == strtoupper($_SESSION['captcha']['code'])) {
      $_SESSION['captcha_valid'] = $captcha;
      exit(json_encode(['true']));

    } else {
      include("./simple-php-captcha.php");
      $_SESSION['captcha'] = simple_php_captcha();
      $image_src = $_SESSION['captcha']['image_src'];
  
      exit(json_encode(['false', $image_src]));
    }
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
        $arr[$j]['excerpt'] = $post->excerpt();
        $arr[$j]['created_at'] = date('F j, Y', strtotime($post->created_at));
        $arr[$j]['format'] = $post->format;
        if ($post->format == 'image') $arr[$j]['image'] = \App\Classes\Post::responsive($post->image);
        else if ($post->format == 'video') $arr[$j]['video'] = $post->getEntryVideo();
        $arr[$j]['category'] = htmlspecialchars($post->category);
        $arr[$j]['username'] = htmlspecialchars($post->username);
        $arr[$j]['to_single'] = url_for('post/' . urlencode($post->title) . '?id=' . $post->id);
        $arr[$j]['to_author'] = url_for('author/' . urlencode($post->username) . '?uid=' . $post->user_id);
        $arr[$j]['to_on_date'] = url_for('ondate/pub/?ymd=' . urlencode(date('Y-m-d', strtotime($post->created_at))));
        $arr[$j]['to_category'] = url_for('category/' . urlencode($post->category) . '?tid=' . $post->category_id);
        $j++;
      }
      $pag = [
        'total_count' => $total_count,
        'html' => $pagination->pageLinks('staff/index.php')
      ];
      exit(json_encode(['success', $arr, $pag]));
    }

  }
  
  exit(json_encode(['empty']));
}

function site_setting_removed($data, $origin_path) {
  $origin = json_decode(file_get_contents($origin_path), true);
  $origin_keys = array_keys_multi($origin);
  $data_keys = array_keys_multi($data);
  $err_code = 0; 

  foreach ($origin_keys as $setting) {
    if (!in_array($setting, $data_keys)) {
      $err_code += 1;
      break;
    }

    $origin_cnt = array_count_values($origin_keys);
    $data_cnt = array_count_values($data_keys);
    
    if ($data_cnt[$setting] != $origin_cnt[$setting]) {
      $err_code += 1;
      break;
    }
  }

  foreach ($data_keys as $setting) {
    if (!in_array($setting, $origin_keys)) {
      $err_code += 2;
      break;
    }
  }

  return $err_code;
}

?>