<?php
/**
 * Define root path
 */

use function DuckyCMS\dcms_get_base_url;

if (!defined('DUCKY_ROOT')) {
  define('DUCKY_ROOT', realpath(__DIR__));
}

/**
 * If we don't have a DB then redirect to setup (unles we're in CLI)
 *
 * Note: PHP_SAPI is a magic const that tells us how the script is being run
 */
if (
  PHP_SAPI !== 'cli' &&
  !file_exists(DUCKY_ROOT . '/db/ducky.sqlite') &&
  !str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/setup/')
) {
  require_once DUCKY_ROOT . '/includes/functions.php';
  header('Location: ' . dcms_get_base_url() . 'setup/pages/welcome.php');
  exit;
}