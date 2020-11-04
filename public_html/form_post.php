<?php
use App\Classes\Post;
use App\Classes\File;
use App\Contracts\Mailer;

require_once '../src/initialize.php';

if (is_post_request()) {

  if (isset($_POST['dropzone'])) {
    // Check is Author >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    if (!$session->isAuthor()) exit;
    // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Check is Author

    if ($_POST['dropzone'] == 'upload' && isset($_FILES['file'])) {
      $image = new File($_FILES['file']);
      $image->temporaryDir($session->getUserId());
      
      $image->handleUpload('file');
  
      if ($image->error) {
        exit (json_encode(['error', $image->error]));   
      } else {
        $image = Post::shortcode($image->getFileInfo()['img']);
        exit (json_encode(['success', $image]));
      }

    } else if ($_POST['dropzone'] == 'remove' && isset($_POST['image'])) {
      $file = new File;
      $temp = $file->getTemporaryDir();
      $image = Post::bodyShortcodeImage($_POST['image'])[0];
      $image = "{$temp}/{$image}";
      
      if ($file->remove($image)) {
        exit(json_encode(['success', $_POST['image']]));
      } else {
        exit(json_encode(['error', 'image remove failed']));
      }
    }

  } else {
    $email = $_POST['email'] ?? '';
    $message = $_POST['message'] ?? '';
    $captcha = $_POST['captcha'] ?? '';
  
    if ($email && $message && $captcha) {
      
      if (strtoupper($captcha) == strtoupper($_SESSION['captcha']['code'])) {
        $mailer = new Mailer;
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