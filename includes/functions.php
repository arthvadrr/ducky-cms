<?php

namespace DuckyCMS;

use PDOException;
use function DuckyCMS\DB\get_setting;

/**
 * Dynamically define the BASE_URL to support installs in different directories or environments.
 * This constructs the URL using the current protocol, host, and path — minus the script name —
 * so links and redirects work reliably without needing to hardcode a base path.
 */

if (!defined('DUCKY_ROOT')) {
  define('DUCKY_ROOT', dirname(__DIR__));
}

function dcms_get_base_url(): string {
  $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
  $host = $_SERVER['HTTP_HOST'];

  return "$protocol://$host/";
}

function dcms_db_exists(): bool {
  return !empty( glob(DUCKY_ROOT . '/db/*.sqlite') );
}

/**
 * Get the user set URL
 *
 * @return string
 */
function dcms_get_site_url(): string {
  $default_url = dcms_get_base_url();
  $db_files = glob(DUCKY_ROOT . '/db/*.sqlite');

  if (empty($db_files)) {
    return $default_url;
  }

  require_once DUCKY_ROOT . '/db/interface.php';
  
  try {
    $site_url = get_setting('site_url');
    return $site_url ?? $default_url;
  } catch (PDOException) {
    return $default_url;
  }
}