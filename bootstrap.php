<?php
/**
 * Exit if not accessed directly
 */
if (realpath(__FILE__) !== realpath($_SERVER['SCRIPT_FILENAME'])) {
  exit('Nope.');
}

if (!defined('DUCKY_ROOT')) {
  define('DUCKY_ROOT', realpath(__DIR__));
}