<?php

namespace DuckyCMS\Setup;

use PDOException;
use function DuckyCMS\DB\get_user_by_username;
use function DuckyCMS\DB\update_user_session_token;
use function DuckyCMS\dcms_get_base_url;

/**
 * Exit if not accessed directly
 */
if (realpath(__FILE__) !== realpath($_SERVER['SCRIPT_FILENAME'])) {
  exit('Nope.');
}

/**
 * Includes
 */
require_once '../bootstrap.php';
require_once '../includes/functions.php';
require_once '../templates/setup-layout.php';
require_once '../db/interface.php';

session_start();

/**
 * Handle POST login, returns message
 *
 * @return string
 * @throws
 */
function handle_login(): string
{
  $request_method = $_SERVER['REQUEST_METHOD'] ?? '';

  if ($request_method !== 'POST') {
    return '';
  }

  $username = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';
  $db_path  = DUCKY_ROOT . '/db/ducky.sqlite';

  if (!file_exists($db_path)) {
    return '<p>Database not found.</p>';
  }

  try {
    $user = get_user_by_username($username, $db_path);

    if ($user && password_verify($password, $user['password'])) {
      session_regenerate_id(true);
      $token = bin2hex(random_bytes(32));
      $created_at = time();
      
      update_user_session_token($user['id'], $token, $created_at, $db_path);

      $_SESSION['username'] = $user['username'];
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['session_token'] = $token;

      header('Location: ' . dcms_get_base_url() . 'dashboard/index.php');
      exit;
    }

    return '<p>Invalid username or password.</p>';
  } catch (PDOException $e) {
    return '<p>Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
  }
}

$message = handle_login();

ob_start();
?>
  <form method="post">
    <label for="username">Username:</label><input id="username" name="username" type="text" required><br>
    <label for="password">Password:</label><input id="password" name="password" type="password" required><br>
    <button>Log In</button>
  </form>
  <?php if (!empty($message)) echo $message; ?>
  <?php render_layout("DuckyCMS Login", ob_get_clean()); ?>