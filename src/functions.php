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

function page_back_link($link='Back', $class_list) {
  $url = '';
  if (isset($_SERVER['HTTP_REFERER']) && isset($_SERVER['HTTP_HOST'])) {
    $url = parse_url($_SERVER['HTTP_REFERER']);
    if ($url['host'] == $_SERVER['HTTP_HOST']) {
      $url = $_SERVER['HTTP_REFERER'];
    } else {
      $url = '';
    }
  }
  $output = "<a href=\"" . ($url ? $url : url_for('index.php')) . "\"";
  $output .= " class=\"" . $class_list . "\" >";
  $output .= $link;
  $output .= '</a>';
  
  return $output;
}

function url_contain($string) {
  if (strpos($_SERVER['REQUEST_URI'], $string) !== false) {
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
  return "{$_SERVER['REQUEST_SCHEME']}://{$_SERVER['HTTP_HOST']}";
}

?>