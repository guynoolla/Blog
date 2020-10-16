<?php

function dd($value, $execute=0, $msg='') {
  echo '<b>' . $msg . '</b>';
  echo '<pre>' . print_r($value, true) . '</pre>';
  
  if (!$execute) exit;
}

error_reporting(E_ALL);
ini_set('display_errors', '1');

function err_handle($err_no, $err_str, $err_file, $err_line) {
  $msg = "$err_str in $err_file on line $err_line";
  
  if ($err_no == E_NOTICE || $err_no == E_WARNING) {
    throw new ErrorException($msg, $err_no);
  } else {
    echo $msg;
  }
}

set_error_handler('err_handle');

?>