<?php
namespace DuckyCMS\Setup;

use function DuckyCMS\dcms_get_base_url;

session_start();

if (!isset($_SESSION['user'])) {
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

require_once DUCKY_ROOT . '/templates/admin-layout.php';

ob_start();
?>
  <h2>Dashboard</h2>
  <p>Welcome, <?php echo htmlspecialchars($_SESSION['user']); ?>! You’ve made it to the DuckyCMS dashboard.</p>
  <ul>
    <li><a href="#">Manage Pages</a></li>
    <li><a href="#">View Posts</a></li>
    <li><a href="#">Logout</a></li>
  </ul>
<?php
render_layout('DuckyCMS Dashboard', ob_get_clean());