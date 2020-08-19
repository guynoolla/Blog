<?php

function dd($value, $execute=0, $msg='') {
  echo '<b>' . $msg . '</b>';
  echo '<pre>' . print_r($value, true) . '</pre>';
  if (!$execute) exit;
}

?>