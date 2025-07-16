<?php
/**
 * Define root path
 */

if (!defined('DUCKY_ROOT')) {
  define('DUCKY_ROOT', realpath(__DIR__));
}

use function DuckyCMS\dcms_db_exists;
use function DuckyCMS\dcms_get_base_url;

require_once DUCKY_ROOT . '/includes/functions.php';

/**
 * If we don't have a DB then redirect to setup (unles we're in CLI)
 *
 * Note: PHP_SAPI is a magic const that tells us how the script is being run
 */
if (
  PHP_SAPI !== 'cli' &&
  !dcms_db_exists() &&
  !str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/setup/')
) {
  require_once DUCKY_ROOT . '/includes/functions.php';
  header('Location: ' . dcms_get_base_url() . 'setup/pages/welcome.php');
  exit;
}