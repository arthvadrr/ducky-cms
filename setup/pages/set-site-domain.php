<?php

require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once DUCKY_ROOT . '/includes/functions.php';
require_once DUCKY_ROOT . '/db/interface.php';

use function DuckyCMS\DB\get_setting;
use function DuckyCMS\DB\set_setting;
use function DuckyCMS\dcms_get_base_url;

session_start();


function dcms_handle_site_url_update(): string
{
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['site_url'])) {
    $url = trim($_POST['site_url']);

    try {
      set_setting('site_url', $url);
      return '<p>Site URL saved successfully! <a href="' . dcms_get_base_url() . 'setup/pages/create-admin-user.php">Continue to Create Admin User</a>.</p>';
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

$site_url = $site_url ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Set Site URL</title>
</head>
<body>
<h2>Set Site URL</h2>
<?= $message ?>
<form method="post">
  <label for="site_url">Site URL:</label>
  <input type="text" id="site_url" name="site_url" value="<?= htmlspecialchars($site_url) ?>" required>
  <button type="submit">Save</button>
</form>
</body>
</html>