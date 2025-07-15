<?php

namespace DuckyCMS\Setup;

use PDO;
use PDOException;
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
require_once '../templates/admin-layout.php';

session_start();

/**
 * Handle POST login, returns message
 * @return string
 */
function handle_login(): string {
  $request_method = $_SERVER['REQUEST_METHOD'] ?? '';

  if ($request_method !== 'POST') {
    return '';
  }

  $username = $_POST['email'] ?? '';
  $password = $_POST['password'] ?? '';
  $db_path  = DUCKY_ROOT . '/db/ducky.sqlite';

  if (!file_exists($db_path)) {
    return '<p>Database not found.</p>';
  }

  try {
    $pdo = new PDO('sqlite:' . $db_path);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = :username');
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
      $_SESSION['user'] = $user['username'];
      header('Location: ' . dcms_get_base_url() . 'dashboard/dashboard.php');
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
  <h2>Login</h2>
  <form method="post">
    <label for="email">Email:</label><input id="email" name="email" type="text" required><br>
    <label for="password">Password:</label><input id="password" name="password" type="password" required><br>
    <button>Log In</button>
  </form>
  <?php if (!empty($message)) echo $message; ?>
  <?php render_layout("Login to DuckyCMS", ob_get_clean()); ?>