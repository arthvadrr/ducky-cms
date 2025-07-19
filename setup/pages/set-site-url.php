<?php

require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once DUCKY_ROOT . '/includes/functions.php';
require_once DUCKY_ROOT . '/templates/admin-layout.php';
require_once DUCKY_ROOT . '/db/interface.php';

use function DuckyCMS\DB\get_setting;
use function DuckyCMS\DB\set_setting;
use function DuckyCMS\dcms_get_base_url;
use function DuckyCMS\Setup\render_layout;

session_start();

function dcms_handle_site_url_update(): string
{
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['site_url'])) {
    $url                   = trim($_POST['site_url']);
    $create_admin_user_url = dcms_get_base_url() . 'setup/pages/create-admin-user.php';

    try {
      set_setting('site_url', $url);
      return '<p>Set to <code>' . htmlspecialchars($url) . '</code>. <a href="' . $create_admin_user_url . '">Continue to Create Admin User</a>.</p>';
    } catch (PDOException $e) {
      return '<p style="color: red;">Error saving site URL: ' . htmlspecialchars($e->getMessage()) . '</p>';
    }
  }

  return '';
}

$message = dcms_handle_site_url_update();

try {
  $site_url = get_setting('site_url');
} catch (PDOException $e) {
  $site_url = '';
}

$default_url = dcms_get_base_url();
$site_url    = get_setting('site_url') ?: $default_url;

ob_start();
?>

  <h2>Set Site URL</h2>
<?php if ($message && $site_url): ?>
  <form method="post">
    <p>Detected base URL: <?= htmlspecialchars($default_url) ?></p>
    <label for="site_url">Site URL:</label>
    <input type="text" id="site_url" name="site_url" value="<?= htmlspecialchars($site_url) ?>" required>
    <button type="submit">Update</button>
  </form>
<?php endif; ?>
<?php if ($message): ?>
  <?= $message ?>
<?php else: ?>
  <form method="post">
    <p>Detected base URL: <?= htmlspecialchars($default_url) ?></p>
    <label for="site_url">Site URL:</label>
    <input type="text" id="site_url" name="site_url" value="<?= htmlspecialchars($site_url) ?>" required>
    <button type="submit">Save</button>
  </form>
<?php endif;
render_layout('DuckyCMS Setup', ob_get_clean());
?>