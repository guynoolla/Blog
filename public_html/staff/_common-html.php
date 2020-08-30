<?php

function page_back_button($link='Back', $class_list='btn btn-outline-primary mb-1 ml-1') {
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
