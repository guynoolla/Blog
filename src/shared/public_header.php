<?php

if (!isset($page_title)) {
  $page_title = 'Blog';
}

?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $page_title ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Open+Sans:ital,wght@0,300;1,300&display=swap" rel="stylesheet">
  <!-- Awesome fonts replaced with awesome svg -->
  <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css"> -->
  <!-- Add the slick-theme.css if you want default styling -->
  <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/gh/kenwheeler/slick@1.8.1/slick/slick.css"/>
  <!-- Add the slick-theme.css if you want default styling -->
  <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/gh/kenwheeler/slick@1.8.1/slick/slick-theme.css"/>
  <link rel="stylesheet" href="<?php echo url_for('assets/css/style.css') ?>">
  <link rel="apple-touch-icon" sizes="180x180" href="<?php echo url_for('/apple-touch-icon.png') ?>">
  <link rel="icon" type="image/png" sizes="32x32" href="<?php echo url_for('/favicon-32x32.png') ?>">
  <link rel="icon" type="image/png" sizes="16x16" href="<?php echo url_for('/favicon-16x16.png') ?>">
  <link rel="manifest" href="<?php echo url_for('/site.webmanifest') ?>">
</head>
<body>

<header id="topScrollElement">
  <nav class="nav-ini-hide navbar navbar-expand-md navbar-light fixed-top bg-light" id="hideByScroll">
    <div class="container">
      <!-- <a class="navbar-brand d-sm-none" href="#">Kite</a> -->
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarCollapse">
        <ul class="navbar-nav mr-auto siteNavJS">
          <li class="nav-item active">
            <a class="nav-link" href="<?php echo url_for('/') ?>">Home <span class="sr-only">(current)</span></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#widget-contact-form">Contact</a>
          </li>
          <?php $header_type = 'public'; include '_navbar_dropdown.php'; ?>
        </ul>
        <form id="navSearchForm" action="<?php echo url_for('index.php') ?>" method="get" class="form-inline mt-2 mt-md-0">
          <input name="s" class="form-control mr-sm-2 search-field-lk hide" type="text" placeholder="Search" aria-label="Search">
          <label>
            <input type="submit" name="b" value="search" style="display:none">
            <span class="svg-search-btn">
              <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="search" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-search fa-w-16 fa-3x"><path fill="currentColor" d="M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6.1-34zM208 336c-70.7 0-128-57.2-128-128 0-70.7 57.2-128 128-128 70.7 0 128 57.2 128 128 0 70.7-57.2 128-128 128z" class="svg-gray"></path></svg>
            </span>
          </label>
        </form>
      </div>
    </div>
  </nav>
  <section class="logo">
    <div class="logo-content">
      <h1 class="brand"><a href="/"><?php echo $jsonstore->header->siteName ?></a></h1>
      <div class="description"><?php echo $jsonstore->header->siteDescription ?></div>
    </div>
  </section>
</header>

<div><!--Content wrapper div open-->

  <div class="container-md">
    <div class="row">
      <div class="col">
        <?php echo display_session_message('alert alert-success alert-dismissible py-3 my-2 text-center h4 '); ?>
      </div>
    </div>
  </div>

<!--div wrapper div must be closed in Footer-->