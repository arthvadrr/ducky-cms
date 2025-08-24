<?php

require_once dirname(__DIR__, 2) . '/bootstrap.php';

/*
 * Load required modules using lazy loading
 */

use DuckyCMS\AlertType;
use function DuckyCMS\dcms_alert;
use function DuckyCMS\dcms_db_exists;
use function DuckyCMS\dcms_get_base_url;
use function DuckyCMS\dcms_require_module;
use function DuckyCMS\Setup\dcms_render_setup_layout;

dcms_require_module('db');
dcms_require_module('templates');
dcms_require_module('partials');

session_start();

/**
 * If a database already exists, do not allow access to this page
 * We simply redirect to login (or other appropriate entry point) without any DB queries.
 */
if (dcms_db_exists()) {
  header('Location: ' . dcms_get_base_url() . 'auth/login/');
  exit;
}

function dcms_handle_site_url_update(): array
{
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['site_url'])) {
    $url = trim($_POST['site_url']);

    $create_db_url = dcms_get_base_url() . 'setup/create-database/';

    /**
     * DB does not exist: bind the site URL to the session to be saved during DB creation
     */
    $_SESSION['pending_site_url'] = $url;

    // Prepare alert HTML (only the alert message, no button)
    $alert_html = dcms_alert(
      'Site URL set to ' . htmlspecialchars($url) . '. It will be saved when you create the database.',
      AlertType::success
    );

    return [
      'alert' => $alert_html,
      'create_database_url' => $create_db_url,
    ];
  }

  return [];
}

$result              = dcms_handle_site_url_update();
$alert_html          = $result['alert'] ?? '';
$create_database_url = $result['create_database_url'] ?? '';
$default_url         = dcms_get_base_url();
$site_url            = !empty($_SESSION['pending_site_url']) ? $_SESSION['pending_site_url'] : '';
$site_url            = $site_url ?: $default_url;

ob_start();
?>
<?php if ($alert_html): ?>
  <?= $alert_html ?>
<?php endif; ?>
<?php if ($alert_html && $site_url): ?>
  <form method="post">
    <div>
      <label for="site_url">Site URL:</label>
      <input type="text" id="site_url" name="site_url" value="<?= htmlspecialchars($site_url) ?>" required>
    </div>
    <button class="button outline small" type="submit">Update</button>
  </form>
  <?php if ($create_database_url): ?>
    <a class="button" href="<?= $create_database_url ?>">Continue to Create Database</a>
  <?php endif; ?>
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

dcms_render_setup_layout('Set Site URL', ob_get_clean());
?>