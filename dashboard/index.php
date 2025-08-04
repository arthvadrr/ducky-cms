<?php

namespace DuckyCMS\Setup;

require_once __DIR__ . '/../bootstrap.php';
require_once DUCKY_ROOT . '/includes/functions.php';
require_once DUCKY_ROOT . '/db/interface.php';

use PDOException;
use function DuckyCMS\DB\get_user_session_token;
use function DuckyCMS\dcms_get_base_url;

session_start();
session_regenerate_id(true);

if (!isset($_SESSION['user_id'], $_SESSION['session_token'])) {
  header('Location: ' . dcms_get_base_url() . 'auth/login.php');
  exit;
}

try {
  $session_token = get_user_session_token($_SESSION['user_id']);

  if (!$session_token || $session_token !== $_SESSION['session_token']) {
    header('Location: ' . dcms_get_base_url() . 'auth/login.php');
    exit;
  }
} catch (PDOException $e) {
  header('Location: ' . dcms_get_base_url() . 'auth/login.php');
  exit($e);
}

require_once DUCKY_ROOT . '/templates/setup-layout.php';

$logout_url = dcms_get_base_url() . 'auth/logout.php';
$pages_url  = dcms_get_base_url() . 'dashboard/pages';

ob_start();
?>
  <h2>Dashboard</h2>
  <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>! You’ve made it to the DuckyCMS dashboard.</p>
  <ul>
    <li><a href="<?= $pages_url ?>">Pages</a></li>
    <li><a href="#">View Posts</a></li>
    <li><a href=<?= $logout_url ?>>Logout</a></li>
  </ul>
  <?php
render_layout('DuckyCMS Dashboard', ob_get_clean());