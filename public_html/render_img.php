<?php

$width = $_GET['w'] ?? 0;
$image = $_GET['img'] ?? '';
$image = $image ? urldecode($image) : '';
$dir = './assets/images';
$valid_widths = [420, 640, 800, 1025];

if (!file_exists($dir.$image) || !in_array($width, $valid_widths)) {
  exit('Wrong parameters.');
} else {
  $ext = substr(strrchr( $image, '.'), 1);
}

list($wori, $hori) = getimagesize($dir.$image);

if ($width > $wori) {
  $render = $dir.$image;
} else {
  list($noextimg, $ext) = explode('.', $image);
  $filename = "{$dir}{$noextimg}_{$width}.{$ext}";
  if (file_exists($filename)) {
    $render = $filename; // resized image
  } else {
    $render = $dir.$image; // original image
  }
}

header("Content-Type: image/{$ext}");
header('Content-Length: ' . filesize($render));
readfile($render);

?>