<?php
//-----------------------------------------------
// Define root folder and load base
//-----------------------------------------------
if (!defined('MAESTRANO_ROOT')) {
  define("MAESTRANO_ROOT", realpath(dirname(__FILE__) . '/../../'));
}
require_once MAESTRANO_ROOT . '/app/init/base.php';

//-----------------------------------------------
// Require your app specific files here
//-----------------------------------------------
define('APP_DIR', realpath(MAESTRANO_ROOT . '/../'));
chdir(APP_DIR);
define('NOAUTH',1);
require_once APP_DIR . "/include/init.php";

//-----------------------------------------------
// Perform your custom preparation code
//-----------------------------------------------
// If you define the $opts variable then it will
// automatically be passed to the MnoSsoUser object
// for construction
$opts = array();
$opts['db_connection'] = $db; # $db was setup in init.php

// Default  user domain
global $auth_session;
if(!isset($auth_session) || !isset($auth_session->domain_id)) {
	$auth_session->domain_id = 1;
}

?>