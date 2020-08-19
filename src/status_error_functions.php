<?php
declare(strict_types=1);

function require_login($redirect='login.php') {
  global $session;
  if (!$session->isLoggedIn()) {
    redirect_to(url_for($redirect));
  } else {
    // Do nothing, let the rest of the page proceed   
  }
}

function display_errors($errors=array(), $top_text="Please fix the following errors:") {
  $output = '';
  if (!empty($errors)) {
    $output .= "<div class=\"msg errors\">";
    $output .= $top_text;
    $output .= "<ul>";
    foreach($errors as $error) {
      $output .= "<li>" . h($error) . "</li>";
    }
    $output .= "</ul>";
    $output .= "</div>";
  }
  return $output;
}

function display_session_message($class_list='informer') {
  global $session;
  $msg = $session->message();
  if(isset($msg) && $msg != '') {
    $session->clearMessage();
    return "<div class=\"" . $class_list . "\">" . h($msg) . "</div>";
  }
}

?>