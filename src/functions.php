<?php
declare(strict_types=1);

function url_for($script_path) {
  if ($script_path[0] != '/') {
    $script_path = "/" . $script_path;
  }
  return WWW_ROOT . $script_path;
}

function u($string="") {
  return urlencode($string);
}

function raw_u($string="") {
  return rawurlencode($string);
}

function h($string="") {
  return htmlspecialchars($string);
}

function error_404() {
  header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
  exit();
}

function error_500() {
  header($_SERVER['SERVER_PROTOCOL'] . " 500 Internal Server Error");
  exit();
}

function redirect_to($location) {
  header("Location: " . $location);
  exit;
}

function is_post_request() {
  return $_SERVER['REQUEST_METHOD'] == 'POST';
}

function is_get_request() {
  return $_SERVER['REQUEST_METHOD'] == 'GET';
}

// PHP on Windows does not have a money_format() function.
// This is a super-simple replacement
if(!function_exists('money_format')) {
  function money_format($format, $number) {
    return '$' . number_format($number, 2);
  }
}

function url_contain($value) {
  $arr = [];
  if (is_string($value)) {
    $arr[] = $value;
  } elseif (is_array($value)) {
    $arr = $value;
  }
  foreach($arr as $value) {
    if (strpos($_SERVER['REQUEST_URI'], $value) !== false) {
      return true;
    }
  }
  return false;
}

function is_homepage() {
  if ($_SERVER['REQUEST_URI'] == '/' || $_SERVER['REQUEST_URI'] == '/index.php') {
    return true;
  } else {
    return false;
  }
}

function is_active($unique_url_part, $link, $icon='', $url_params='', $num=false) {
  $url = url_for($unique_url_part . '.php' . $url_params);
  if (url_contain($unique_url_part)) {
    $str = '<a href="' . $url . '" class="active">';
  } else {
    $str = '<a href="' . $url . '">';
  }
  $str .= '<i class="' . $icon . ' active"></i>' . $link;
  if ($num !== false) {
    $str .= '<span class="show-number">&nbsp;' . $num . '</span>';
  }
  $str .= '</a>';

  return $str;
}

function url_split_by_slash() {
  $uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
  return explode('/', $uri_parts[0]);
}

function get_base_url() {
  $base = "{$_SERVER['REQUEST_SCHEME']}://{$_SERVER['HTTP_HOST']}";
  if ($_SERVER['SERVER_NAME'] == 'localhost') {
    $base .= url_for('/');
  }
  return $base;
}

function page_back_url() {
  if (isset($_SERVER['HTTP_REFERER']) && isset($_SERVER['HTTP_HOST'])) {
    $url = parse_url($_SERVER['HTTP_REFERER']);
    if ($url['host'] == $_SERVER['HTTP_HOST']) {
      return $_SERVER['HTTP_REFERER'];
    } else {
      return '';
    }
  }  
}

function page_back_button($link='Back', $class_list='btn btn-outline-primary my-1 ml-1') {
  $url = page_back_url();
  $output = "<a href=\"" . ($url ? $url : url_for('index.php')) . "\"";
  $output .= " class=\"" . $class_list . "\" >";
  $output .= $link;
  $output .= '</a>';
  return $output;
}

function no_gaps_between($str) {
  $arr = explode(",", $str);
  foreach($arr as $key => $value) {
    $arr[$key] = trim($value);
  }
  return implode(",", $arr);
}

?>