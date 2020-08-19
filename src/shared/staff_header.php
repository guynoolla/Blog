<?php
if (!isset($page_title)) {
  $page_title = 'Blog';
}

?>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />

  <!-- Custom Styles -->
  <link rel="stylesheet" href="<?php echo url_for('/assets/css/style.css') ?>">

  <!-- Admin Styling -->
  <link rel="stylesheet" href="<?php echo url_for('/assets/css/admin.css') ?>">

  <link rel="stylesheet" href="<?php echo url_for('/assets/styles/style.css') ?>">

  <title><?php echo (isset($page_title)) ? $page_title : 'Blog' ?></title>
</head>

<body>
  <?php include SHARED_PATH .'/_navbar.php' ?>