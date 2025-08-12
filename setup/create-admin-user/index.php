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
use function DuckyCMS\dcms_alert;
use DuckyCMS\AlertType;

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
dcms_require_module('partials');

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
 * @returns array ['message' => string, 'success' => bool]
 */
function dcms_create_admin_user(): array
{
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    return ['message' => '', 'success' => false];
  }

  $username = trim($_POST['username']);
  $password = $_POST['password'];

  if (!$username || !$password) {
    return ['message' => dcms_alert('Username and password are both required.', AlertType::danger), 'success' => false];
  }

  if (!preg_match('/^[a-zA-Z0-9_-]{6,32}$/', $username)) {
    return ['message' => dcms_alert('Invalid username format. Use 6-32 characters: letters, numbers, dashes, or underscores only.', AlertType::danger), 'success' => false];
  }

  if (strlen($password) < 12 || strlen($password) > 128) {
    return ['message' => dcms_alert('Password must be between 12 and 128 characters.', AlertType::danger), 'success' => false];
  }

  global $nonce, $db_path;

  $hashed_password = password_hash($password, PASSWORD_DEFAULT);

  try {
    create_user($username, $hashed_password, $db_path);
    mark_setup_nonce_used($nonce, $db_path);
    return ['message' => dcms_alert('Admin user created successfully!', AlertType::success), 'success' => true];
  } catch (PDOException $e) {
    return ['message' => dcms_alert('Error creating user: ' . $e->getMessage(), AlertType::danger), 'success' => false];
  }
}

$result = dcms_create_admin_user();
$message = $result['message'];
$success = $result['success'];

ob_start();
?>
  <section>
    <?php if ($success): ?>
      <?= $message ?>
      <div style="margin-top: 1rem;">
        <a href="<?= dcms_get_base_url() ?>auth/login/" class="button">Continue to Login</a>
      </div>
    <?php else: ?>
      <p>Pick a <strong>username</strong> and a <strong>password</strong>.</p>
      <?php if (!empty($message)) echo $message; ?>
      <form method="post">
        <div>
          <label for="username">Username</label>
          <small id="username-help" class="form-text text-muted">Must be between 6 and 32 characters.</small>
          <input id="username" name="username" type="text" placeholder="ducky_admin" autocomplete="off" required aria-describedby="username-help">
        </div>
        <div>
          <label for="password">Password</label>
          <small id="password-help" class="form-text text-muted">Must be between 12 and 128 characters.</small>
          <input id="password" name="password" type="password" placeholder="••••••••••••" autocomplete="off" required aria-describedby="password-help">
        </div>
        <button class="button" type="submit">Create User</button>
      </form>
    <?php endif; ?>
  </section>
  <?php
dcms_render_setup_layout('Create Admin User', ob_get_clean());