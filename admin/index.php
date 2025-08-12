<?php

namespace DuckyCMS\Setup;

require_once __DIR__ . '/../bootstrap.php';

/*
 * Load required modules using lazy loading
 */
use function DuckyCMS\dcms_require_module;
dcms_require_module('db');
dcms_require_module('admin');

use PDOException;
use function DuckyCMS\DB\get_user_session_token;
use function DuckyCMS\dcms_get_base_url;

session_start();
session_regenerate_id(true);

if (!isset($_SESSION['user_id'], $_SESSION['session_token'])) {
  header('Location: ' . dcms_get_base_url() . 'auth/login/');
  exit;
}

try {
  $session_token = get_user_session_token($_SESSION['user_id']);

  if (!$session_token || $session_token !== $_SESSION['session_token']) {
    header('Location: ' . dcms_get_base_url() . 'auth/login/');
    exit;
  }
} catch (PDOException $e) {
  header('Location: ' . dcms_get_base_url() . 'auth/login/');
  exit($e);
}

$logout_url = dcms_get_base_url() . 'auth/logout/';
$pages_url  = dcms_get_base_url() . 'admin/pages-index/';

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
dcms_render_dashboard_layout('Dashboard', ob_get_clean(), 'dashboard');