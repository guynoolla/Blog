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
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
  <!-- Add the slick-theme.css if you want default styling -->
  <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/gh/kenwheeler/slick@1.8.1/slick/slick.css"/>
  <!-- Add the slick-theme.css if you want default styling -->
  <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/gh/kenwheeler/slick@1.8.1/slick/slick-theme.css"/>
  <link rel="stylesheet" href="<?php echo url_for('assets/css/style.css') ?>">
</head>
<body>

<header id="topScrollElement">
  <nav class="navbar navbar-expand-md navbar-light fixed-top bg-light" id="hideByScroll">
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
            <a class="nav-link" href="#scrollTestContact">Contact</a>
          </li>
          <?php include '_navbar-dropdown.php' ?>
        </ul>
        <form class="form-inline mt-2 mt-md-0">
          <input class="form-control mr-sm-2" type="text" placeholder="Search" aria-label="Search">
          <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
        </form>
      </div>
    </div>
  </nav>
  <section class="logo">
    <div class="logo-content">
      <h1 class="brand"><a href="/">Light Kite</a></h1>
      <div class="description">Just another theme by Gainulla</div>
    </div>
  </section>
</header>

<div> <!--Content wrapper div open-->