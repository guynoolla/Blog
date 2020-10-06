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

function display_session_message($dismissible=true) {
  global $session;

  list ($msg, $type) = $session->message();

  if (isset($msg) && $msg != "") {
    switch($type) {
      case 'success': $alert_type = 'alert-success';
                    break;
      case 'danger': $alert_type = 'alert-danger';
                    break;
      case 'warning': $alert_type = 'alert-warning';
                    break;
      case 'info': $alert_type = 'alert-info';
                  break;
      default: $alert_type = "";
    }
    $styles = 'py-3 my-2 mx-sm-3 text-center h4';
    $styles .= $dismissible ? ' alert-dismissible' : '';

    $session->clearMessage();
    $output = "<div class=\"alert {$alert_type} {$styles}\">";

    if ($dismissible) {
      $output .= '<button type="button" class="close" data-dismiss="alert">';
      $output .= '&times;';
      $output .= '</button>';
    }

    $output .=  h($msg) . '</div>';
    return $output;
  }
}

?>