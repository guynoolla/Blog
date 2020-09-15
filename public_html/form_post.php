<?php

require_once '../src/initialize.php';

if (is_post_request()) {
  $email = $_POST['email'] ?? '';
  $message = $_POST['message'] ?? '';

  if ($email && $message) {
    $mailer = new \App\Contracts\Mailer;
    $text = strip_tags($message);
    
    $mailer->send(ADMIN_EMAIL,'Contact Form', $text, $message);
    $session->message('Thank you for your message!');
    redirect_to(url_for('index.php'));
  }
}

?>