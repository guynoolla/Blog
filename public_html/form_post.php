<?php

require_once '../src/initialize.php';

if (is_post_request()) {
  $email = $_POST['email'] ?? '';
  $message = $_POST['message'] ?? '';
  $captcha = $_POST['captcha'] ?? '';

  if ($email && $message && $captcha) {
    
    if (strtoupper($captcha) == strtoupper($_SESSION['captcha']['code'])) {
      $mailer = new \App\Contracts\Mailer;
      $text = strip_tags($message);
      
      $mailer->send(ADMIN_EMAIL, $jsonstore->header->siteName, $text, $message);
      $session->message('Thank you for your message!');
      redirect_to(url_for('index.php'));
    
    } else {
      $session->store([
        'fp_captcha_err' => 'Captcha validation failed, try again.',
        'fp_field_email' => $email,
        'fp_field_message' => $message,
      ]);
      redirect_to(url_for('index.php#widget-contact-form'));
    }
  }
}

?>