<?php

//-----------------------------------------------
// Define root folder
//-----------------------------------------------
if (!defined('MAESTRANO_ROOT')) {
  define("MAESTRANO_ROOT", realpath(dirname(__FILE__) . '/../../'));
}

require_once(MAESTRANO_ROOT . '/app/init/soa.php');

$maestrano = MaestranoService::getInstance();

error_log("init after maestrano");

if ($maestrano->isSoaEnabled() and $maestrano->getSoaUrl()) {
    $filepath = MAESTRANO_ROOT . '/var/_data_sequence';
    
    if (file_exists($filepath)) {
        $timestamp = trim(file_get_contents($filepath));
        $current_timestamp = round(microtime(true) * 1000);
        error_log("timestamp = ". $timestamp);
        error_log("current timestamp = ". $current_timestamp);
        if (!empty($timestamp)) {
            $mno_entity = new MnoSoaBaseEntity($opts['db_connection']);
            $mno_entity->getUpdates($timestamp);
        }
    }
    file_put_contents($filepath, $current_timestamp);
}

?>
