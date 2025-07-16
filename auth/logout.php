<?php
session_start();

/**
 * Clear all session variables
 */
$_SESSION = [];

/**
 * Delete session cookie if it exists
 */
if (ini_get('session.use_cookies')) {
  $params = session_get_cookie_params();
  $session_name = session_name();
  setcookie($session_name, '', time() - 69,
    $params['path'], $params['domain'],
    $params['secure'], $params['httponly']
  );
}

/**
 * Destroy the session
 */
session_destroy();

/**
 * Redirect to login page
 */
header('Location: login.php');
exit;