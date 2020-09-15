<?php
require_once '../../src/initialize.php';

require_login();

$session->logout();
redirect_to(url_for('index.php'));

?>