<?php
/**
 * This file is step two of the setup. It creates the admin user.
 */

namespace DuckyCMS\Setup;

use PDO;
use PDOException;
use function DuckyCMS\dcms_get_base_url;

/**
 * Exit if not accessed directly.
 */
if (realpath(__FILE__) !== realpath($_SERVER['SCRIPT_FILENAME'])) {
  exit('Nope.');
}

require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once DUCKY_ROOT . '/templates/admin-layout.php';
require_once DUCKY_ROOT . '/includes/functions.php';

/**
 * Make session available if it exists and make sure we have a db path from step 1.
 */
session_start();

$db_path = $_SESSION['db_path'] ?? null;
$nonce   = $_SESSION['setup_nonce'] ?? null;

if (!$db_path || !$nonce || !file_exists($db_path)) {
  header('Location: ' . dcms_get_base_url() . 'auth/login.php');
  exit;
}

$pdo = new PDO('sqlite:' . $db_path);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $pdo->prepare("SELECT token, created_at, used FROM setup_nonce WHERE token = :token LIMIT 1");
$stmt->execute([':token' => $nonce]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row || $row['used'] || $row['created_at'] < (time() - 86400)) {
  header('Location: ' . dcms_get_base_url() . 'auth/login.php');
  exit;
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

  global $pdo, $nonce;

  $user_insert = "
      INSERT INTO users (username, password) 
      VALUES (:username, :password)
  ";

  $stmt            = $pdo->prepare($user_insert);
  $hashed_password = password_hash($password, PASSWORD_DEFAULT);

  try {
    $stmt->execute([':username' => $username, ':password' => $hashed_password]);
    $pdo->exec("UPDATE setup_nonce SET used = 1 WHERE token = " . $pdo->quote($nonce));
    $base_url = dcms_get_base_url();
    header('Location: ' . $base_url . 'auth/login.php');
    exit;
  } catch (PDOException $error) {
    return '<p>Error creating user: ' . htmlspecialchars($error->getMessage()) . '</p>';
  }
}

$message = dcms_create_admin_user();

ob_start();
?>
  <section>
    <h2>Create Admin User</h2>
    <p>Pick a username and password.</p>
    <form method="post">
      <label for="username">Username</label>
      <input id="username" name="username" type="text" placeholder="ducky_admin" autocomplete="off" required>
      <label for="password">Password</label>
      <input id="password" name="password" type="password" placeholder="••••••••••••" autocomplete="off" required>
      <button type="submit">Create User</button>
    </form>
    <?php if (!empty($message)) echo '<div class="message">' . $message . '</div>'; ?>
  </section>

  <?php render_layout('DuckyCMS Create Admin User', ob_get_clean()); ?>