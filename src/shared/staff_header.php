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
</head>
<body>

  <header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top no-border-bottom">
      <div class="container">
        <a class="navbar-brand w-25" href="<?php echo url_for('/') ?>">Light Kite</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
      
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav">
            <?php include '_navbar_dropdown.php' ?>
          </ul>
        </div>

      </div>
    </nav>
  </header>

  <div class="container-xl">
    <div class="page-admin">

      <div class="row">
        <div class="topbox col-12 pt-3">
          <?php echo display_session_message('alert alert-success alert-dismissible py-3 my-2 text-center h4 '); ?>
        </div>
      </div>

    <!--div1 Must be closed in Footer-->
  <!--div2 Must be closed in Footer-->