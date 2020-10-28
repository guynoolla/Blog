<?php
use App\Classes\File;

require_once '../src/initialize.php';

if (is_post_request()) {

  if (isset($_FILES['file']) && $session->isAuthor()) {
    $pid = $_POST['pid'] ?? 0;
    if (!is_int((int) $pid)) exit('param error');

    $image = new File($_FILES['file']);
    $image->subfolder("post-{$pid}");
    
    if ($image->isFileSelected() == true) {
      $image->handleUpload('file');

      if ($image->error) {
        exit (json_encode(['error', $image->error]));
      } else {
        $image->resizeImage();
        $file = $image->getFileInfo();
        $img = '<img srcset="' . \App\Classes\Post::responsive("/{$file['date_path']}/{$file['img']}") . '" alt="" class="my-3">';
        exit (json_encode(['success', $img]));
      }
    }

  } else {

    $email = $_POST['email'] ?? '';
    $message = $_POST['message'] ?? '';
    $captcha = $_POST['captcha'] ?? '';
  
    if ($email && $message && $captcha) {
      
      if (strtoupper($captcha) == strtoupper($_SESSION['captcha']['code'])) {
        $mailer = new \App\Contracts\Mailer;
        $text = strip_tags($message);
        
        try {
          $mailer->send(ADMIN_EMAIL, $jsonstore->siteName, $text, $message);
          $session->message('Thank you for your message!');
          redirect_to(url_for('index.php'));
  
        } catch(Exception $e) {
          $alert = 'Sorry, server error occured. Please try again later.';
        }
  
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

}

?>