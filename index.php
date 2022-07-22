<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once(dirname(__FILE__) . '/cores/autoload.php');
include_once(dirname(__FILE__) . '/routes/web.php');

Route::navigate();