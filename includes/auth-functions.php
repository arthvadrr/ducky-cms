<?php

namespace DuckyCMS;

use PDOException;
use function DuckyCMS\DB\get_db_connection;
use function DuckyCMS\DB\get_setting;

/**
 * Get the user-configured site URL from database settings.
 * Falls back to the base URL if no custom URL is set or database is unavailable.
 *
 * @return string The configured site URL or base URL as fallback
 */
function dcms_get_site_url(): string {
  $default_url = dcms_get_base_url();
  $db_files = glob(DUCKY_ROOT . '/db/*.sqlite');

  if (empty($db_files)) {
    return $default_url;
  }

  dcms_require_module('db');
  
  try {
    $site_url = get_setting('site_url');
    return $site_url ?? $default_url;
  } catch (PDOException) {
    return $default_url;
  }
}

/**
 * Require user authentication to access protected resources.
 * Redirects to login page if user is not authenticated or session is invalid.
 * Validates session token against database for security.
 *
 * @return void
 */
function dcms_require_login(): void {
  session_start();

  if (empty($_SESSION['user_id']) || empty($_SESSION['session_token'])) {
    header('Location: /auth/login/');
    exit;
  }

  dcms_require_module('db');
  $pdo = get_db_connection();
  $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE id = :id AND session_token = :token");
  $stmt->execute([
    ':id' => $_SESSION['user_id'],
    ':token' => $_SESSION['session_token']
  ]);

  if (!$stmt->fetchColumn()) {
    session_destroy();
    header('Location: /auth/login/');
    exit;
  }
}