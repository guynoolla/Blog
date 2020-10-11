<?php

if (!isset($page_title) || $page_title == '') {
  $page_title = 'Blog';
}

?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $page_title ?></title>
  <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css"> -->
  <link rel="stylesheet" href="<?php echo url_for('assets/css/style.css') ?>">
  <link rel="stylesheet" href="<?php echo url_for('assets/css/admin.css') ?>">
  <link rel="apple-touch-icon" sizes="180x180" href="<?php echo url_for('/apple-touch-icon.png') ?>">
  <link rel="icon" type="image/png" sizes="32x32" href="<?php echo url_for('/favicon-32x32.png') ?>">
  <link rel="icon" type="image/png" sizes="16x16" href="<?php echo url_for('/favicon-16x16.png') ?>">
  <link rel="manifest" href="<?php echo url_for('/site.webmanifest') ?>">
  <script src="<?php echo url_for('assets/js/vendor.js') ?>"></script>
</head>
<body>

  <header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top no-border-bottom">
      <div class="container-xl">
        <a class="navbar-brand w-25" href="<?php echo url_for('/') ?>"><?php echo $jsonstore->site->siteName ?></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
      
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav">
            <?php $header_type = 'dashboard'; include '_navbar_dropdown.php'; ?>
          </ul>
        </div>

      </div>
    </nav>
  </header>

  <div class="container-xl containerJS">
    <div class="page-admin">

      <div class="row">
        <div class="topbox col-12 pt-3">
          <?php echo display_session_message(); ?>
        </div>
      </div>

    <!--div1 Must be closed in Footer-->
  <!--div2 Must be closed in Footer-->