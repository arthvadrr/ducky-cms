<?php
/**
 * This file is step two of the setup. It creates the admin user.
 */

namespace DuckyCMS\Setup;

use PDOException;
use function DuckyCMS\DB\create_user;
use function DuckyCMS\DB\get_setup_nonce;
use function DuckyCMS\DB\mark_setup_nonce_used;
use function DuckyCMS\dcms_get_base_url;

/**
 * Exit if not accessed directly.
 */
if (realpath(__FILE__) !== realpath($_SERVER['SCRIPT_FILENAME'])) {
  exit('Nope.');
}

require_once dirname(__DIR__, 2) . '/bootstrap.php';

/*
 * Load required modules using lazy loading
 */
use function DuckyCMS\dcms_require_module;
dcms_require_module('db');
dcms_require_module('templates');

/**
 * Make session available if it exists and make sure we have a db path from step 1.
 */
session_start();

$db_path = $_SESSION['db_path'] ?? null;
$nonce   = $_SESSION['setup_nonce'] ?? null;

if (!$db_path || !$nonce || !file_exists($db_path)) {
  header('Location: ' . dcms_get_base_url() . 'auth/login/');
  exit;
}

try {
  $row = get_setup_nonce($nonce, $db_path);

  if (!$row || $row['used'] || $row['created_at'] < (time() - 86400)) {
    header('Location: ' . dcms_get_base_url() . 'auth/login/');
    exit;
  }
} catch (PDOException $e) {
  header('Location: ' . dcms_get_base_url() . 'auth/login/');
  exit($e);
}

/**
 * Handle adding the admin user to the db.
 *
 * @returns string $message
 */
function dcms_create_admin_user(): string
{
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    return '';
  }

  $username = trim($_POST['username']);
  $password = $_POST['password'];

  if (!$username || !$password) {
    return '<p>Username and password are both required.</p>';
  }

  if (!preg_match('/^[a-zA-Z0-9_-]{3,20}$/', $username)) {
    return '<p>Invalid username format. Use 3-20 characters: letters, numbers, dashes, or underscores only.</p>';
  }

  if (strlen($password) < 12) {
    return '<p>Password must be at least 12 characters for security.</p>';
  }

  global $nonce, $db_path;

  $hashed_password = password_hash($password, PASSWORD_DEFAULT);

  try {
    create_user($username, $hashed_password, $db_path);
    mark_setup_nonce_used($nonce, $db_path);
    $base_url = dcms_get_base_url();
    header('Location: ' . $base_url . 'auth/login/');
    exit;
  } catch (PDOException $e) {
    return '<p>Error creating user: ' . htmlspecialchars($e->getMessage()) . '</p>';
  }
}

$message = dcms_create_admin_user();

ob_start();
?>
  <section>
    <p>Pick a username and password.</p>
    <form method="post">
      <div>
        <label for="username">Username</label>
        <input id="username" name="username" type="text" placeholder="ducky_admin" autocomplete="off" required>
      </div>
      <div>
        <label for="password">Password</label>
        <input id="password" name="password" type="password" placeholder="••••••••••••" autocomplete="off" required>
      </div>
      <button class="button" type="submit">Create User</button>
    </form>
    <?php if (!empty($message)) echo '<div class="message">' . $message . '</div>'; ?>
  </section>
  <?php
render_layout('Create Admin User', ob_get_clean());