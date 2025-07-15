<?php
/**
 * Define root path
 */
if (!defined('DUCKY_ROOT')) {
  define('DUCKY_ROOT', realpath(__DIR__));
}

/**
 * If we don't have a DB then redirect to setup
 */
if (
  PHP_SAPI !== 'cli' && // skip the redirect check in CLI mode
  !file_exists(DUCKY_ROOT . '/db/ducky.sqlite') &&
  !str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/setup/')
) {
  header('Location: /ducky-cms/setup/intro.php');
  exit;
}