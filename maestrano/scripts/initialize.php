<?php

require_once '../init.php';

require_once MAESTRANO_ROOT . '/connec/init.php';
define('DATA_SEQUENCE_FILE', realpath(MAESTRANO_ROOT . '/var/_data_sequence'));

// Open or create a file and returns its content
function openAndReadFile($file_path) {
  if(!file_exists($file_path)) {
    $fp = fopen($file_path, "w");
    fwrite($fp,"");
    fclose($fp);
  }
  return file_get_contents($file_path);
}

// Read the last update timestamp
function lastDataUpdateTimestamp() {
  $timestamp = openAndReadFile(DATA_SEQUENCE_FILE);
  return empty($timestamp) ? 0 : $timestamp;
}

// Update the update timestamp
function setLastDataUpdateTimestamp($timestamp) {
  file_put_contents(DATA_SEQUENCE_FILE, $timestamp);
}

require_once MAESTRANO_ROOT . '/scripts/init_script.php';
require_once MAESTRANO_ROOT . '/scripts/import_data.php';