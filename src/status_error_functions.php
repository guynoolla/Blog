<?php
declare(strict_types=1);

function require_login($redirect='staff/login.php') {
  global $session;
  if (!$session->isLoggedIn()) {
    redirect_to(url_for($redirect));
  } else {
    // Do nothing, let the rest of the page proceed   
  }
}

function display_errors($errors=array(), $top_text="Please fix the following errors") {
  //msg-lk errors-lk
  $output = '';
  if (!empty($errors)) {
    $output .= "<div class=\"form-errors\">";
    $output .= "<ul>";
    $output .= '<h4>' . $top_text . '</h4>';
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

  if (isset($msg) && $msg != '') {
    $session->clearMessage();
    $output = '';
    $output .= "<div class=\"" . $class_list . "\">";
    if (strpos($class_list, 'alert-dismissible') !== false) {
      $output .= "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>";
    }
    $output .=  h($msg) . "</div>";
    
    return $output;
  }
}

?>