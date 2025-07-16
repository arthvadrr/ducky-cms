<?php

namespace DuckyCMS\Setup;

include_once '../includes/functions.php';

use PDO;
use function DuckyCMS\dcms_get_base_url;

session_start();
session_regenerate_id(true);

if (!isset($_SESSION['user_id'], $_SESSION['session_token'])) {
  if (!defined('DUCKY_ROOT')) {
    define('DUCKY_ROOT', dirname(__DIR__));
  }
  require_once DUCKY_ROOT . '/includes/functions.php';
  header('Location: ' . dcms_get_base_url() . 'auth/login.php');
  exit;
}

if (!defined('DUCKY_ROOT')) {
  define('DUCKY_ROOT', dirname(__DIR__));
}

$pdo  = new PDO('sqlite:' . DUCKY_ROOT . '/db/ducky.sqlite');
$stmt = $pdo->prepare("SELECT session_token FROM users WHERE id = :id");
$stmt->execute([':id' => $_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || $user['session_token'] !== $_SESSION['session_token']) {
  header('Location: ' . dcms_get_base_url() . 'auth/login.php');
  exit;
}

require_once DUCKY_ROOT . '/templates/admin-layout.php';

$logout_url = dcms_get_base_url() . 'auth/logout.php';

ob_start();
?>
  <h2>Dashboard</h2>
  <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>! You’ve made it to the DuckyCMS dashboard.</p>
  <ul>
    <li><a href="#">Manage Pages</a></li>
    <li><a href="#">View Posts</a></li>
    <li><a href=<?= $logout_url ?>>Logout</a></li>
  </ul>
  <?php
render_layout('DuckyCMS Dashboard', ob_get_clean());