<?php

require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once DUCKY_ROOT . '/includes/functions.php';
require_once DUCKY_ROOT . '/templates/setup-layout.php';
require_once DUCKY_ROOT . '/db/interface.php';

use DuckyCMS\AlertType;
use function DuckyCMS\DB\get_setting;
use function DuckyCMS\DB\set_setting;
use function DuckyCMS\dcms_alert;
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
      ob_start();
      echo dcms_alert(
        'Site URL set to ' . htmlspecialchars($url),
        AlertType::success
      );
      echo '<a class="button" href="' . $create_admin_user_url . '">Continue to Create Admin User</a>';
      return ob_get_clean();
    } catch (PDOException $e) {
      ob_start();
      echo dcms_alert(
        'Error saving site URL: <code>' . htmlspecialchars($e->getMessage()) . '</code>',
        AlertType::danger
      );
      return ob_get_clean();
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
$site_url    = $site_url ?: $default_url;

ob_start();
?>
<?php if ($message && $site_url): ?>
  <form method="post">
    <div>
      <label for="site_url">Site URL:</label>
      <input type="text" id="site_url" name="site_url" value="<?= htmlspecialchars($site_url) ?>" required>
    </div>
    <button class="button outline small" type="submit">Update</button>
  </form>
<?php endif; ?>
<?php if ($message): ?>
  <?= $message ?>
<?php else: ?>
  <p>
    <span>Detected base URL:</span>
    <br/>
    <code><?= htmlspecialchars($default_url) ?></code>
  </p>
  <form method="post">
    <div>
      <label for="site_url">Set URL:</label>
      <input type="text" id="site_url" name="site_url" value="<?= htmlspecialchars($site_url) ?>" required>
    </div>
    <button class="button" type="submit">Save</button>
  </form>
<?php endif;
render_layout('Set Site URL', ob_get_clean());
?>