<?php
use App\Classes\User;
use App\Contracts\Mailer;

require_once '../../src/initialize.php';

// Check Logged In >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
require_login();
// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Check Logged In

$email = $_GET['email'] ?? '';

if ($email) {

  $email_confirm_token = User::createEmailConfirmToken($email);
  
  if (!$email_confirm_token) {
    error_500();
  
  } else {
    $url = get_base_url() . '/email/confirm.php?token=' . $email_confirm_token;
    $text = 'Please click on the following URL to confirm your email address: ' . $url;
    $html = '<h1>Email confirmation</h1>';
    $html .= '<p>This email sent from '. $_SERVER['SERVER_NAME'] .'.<br> Please, <a href="'. $url .'">';
    $html .= 'click here to confirm your email address</a> or copy and paste the url below <br>' . $url;
    $html .= '<br>to the browser address bar and hit enter.</p>';
    
    try {
      $mailer = new Mailer;
      $mailer->send($email, 'Email confirmation', $text, $html);
    } catch(Exception $e) {
      exit('Swiftmailer fail to mail. Something wrong with SMTP.');
    }

    redirect_to(url_for('email/user_mailed.php'));
  }

}
?>