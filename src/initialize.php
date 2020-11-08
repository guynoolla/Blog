<?php
declare(strict_types=1);

ob_start(); // turn on output buffering

// Paths
define("PRIVATE_PATH", dirname(__FILE__));
define("PROJECT_PATH", dirname(PRIVATE_PATH));
define("PUBLIC_PATH", PROJECT_PATH . "/public_html");
define("SHARED_PATH", PRIVATE_PATH . "/shared");

// Assign the root URL to a PHP constant
if ($_SERVER['SERVER_NAME'] == 'localhost') {
  $public_end = strpos($_SERVER['SCRIPT_NAME'], '/public_html') + 12;
  $doc_root = substr($_SERVER['SCRIPT_NAME'], 0, $public_end);
} else {
  $doc_root = '';
}
define("WWW_ROOT", $doc_root);

// Image
define("MAX_FILE_SIZE", 614400); // 600 KB, 0.6 MB
define("POST_IMG_MAX_NUM", 10);
define("IMAGES_PATH", PUBLIC_PATH . '/assets/images' );
// SMTP
define("SMTP_HOST", "localhost");
define("SMTP_PORT", "465");
define("SMTP_USERNAME", "");
define("SMTP_PASSWORD", "");
define("SMTP_SECURE", "");
define('ADMIN_EMAIL', 'yusupovgz@yandex.ru');
/**
 * Secret key to generate a User token
 * Can be used for any other 256-bit key requirement
 * This key generated by https://randomkeygen.com/
 * You should get another key in randomkeygen.com for more security
 */
define("SECRET_KEY", "jaItYP0KmKp6wcWCCkCKrxMEVT0yIdqn");

// Functions
require_once('functions.php');
require_once('db_credentials.php');
require_once('db_functions.php');
require_once('validation_functions.php');
require_once('status_error_functions.php');
require_once(PROJECT_PATH . '/vendor/autoload.php');
require('debug.php');

$database = db_connect();
App\Classes\DatabaseObject::setDatabase($database);

// Is used globally in pages
$session = new App\Classes\Session;

$json = file_get_contents(PUBLIC_PATH . '/staff/site/user-site-data.json');
// Is used globally in pages
$jsonstore = json_decode($json);
$jsonarray = json_decode($json, true);

// Pagination
define("DASHBOARD_PER_PAGE", $jsonstore->perPageNumBackend);
define("FRONTEND_PER_PAGE", $jsonstore->perPageNumFrontend);
define("TABLE_SIZE", $jsonstore->tableSize);

function pass_to_js() {
  global $session;
  global $jsonstore;

  $server['baseUrl'] = WWW_ROOT;
  $server['isLoggedIn'] = $session->isLoggedIn();
  $server['isAuthor'] = $session->isAuthor();
  $server['isAdmin'] = $session->isAdmin();
  $server['userId'] = $session->isLoggedIn() ? $session->getUserId() : 0;
  $server['singlePost'] = (url_contain('/post/')) ? $_GET['id'] : false;
  $server['dashboardMain'] = (url_contain('staff/index')) ? true : false;
  $server['postFontSize'] = $jsonstore->postFontSize;
  $server['slider'] = $jsonstore->slider;
  $server['maxFileSize'] = MAX_FILE_SIZE;
  $server['postImgMaxNum'] = POST_IMG_MAX_NUM;

  $script = '<script>';
  $script .= 'var server = ' . json_encode($server);
  $script .= '</script>';

  return $script;
}

?>