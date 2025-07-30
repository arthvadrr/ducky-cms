<?php

require_once __DIR__ . '/../../bootstrap.php';
require_once DUCKY_ROOT . '/templates/setup-layout.php';
require_once DUCKY_ROOT . '/includes/functions.php';
require_once DUCKY_ROOT . '/db/interface.php';

use function DuckyCMS\DB\get_page_by_id;
use function DuckyCMS\DB\update_page;
use function DuckyCMS\dcms_get_base_url;
use function DuckyCMS\dcms_require_login;
use function DuckyCMS\Setup\render_layout;

dcms_require_login();

$page_id = $_GET['id'] ?? null;
if (!$page_id || !is_numeric($page_id)) {
  header('Location: /dashboard/pages/index.php');
  exit;
}

$page = get_page_by_id((int)$page_id);
if (!$page) {
  header('Location: /dashboard/pages/index.php');
  exit;
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title   = $_POST['title'] ?? '';
  $slug    = $_POST['slug'] ?? '';
  $content = $_POST['content'] ?? '';

  $result = update_page($page_id, $title, $slug, $content);

  if ($result === true) {
    $message = '<p>Page updated successfully.</p>';
    $page    = get_page_by_id((int)$page_id);
  } elseif (is_string($result)) {
    $message = "<p>$result</p>";
  } else {
    $message = '<p>Failed to update page.</p>';
  }
}

$pages_url = dcms_get_base_url() . 'dashboard/pages';
$delete_url = dcms_get_base_url() . 'dashboard/pages/delete.php';

ob_start();
?>
  <a href="<?= $pages_url ?>">Back to Pages</a>
<?= $message ?>
  <form method="post">
    <label for="title">Title:</label>
    <input type="text" id="title" name="title" value="<?= htmlspecialchars($page['title']) ?>" required>

    <label for="slug">Slug:</label>
    <input type="text" id="slug" name="slug" value="<?= htmlspecialchars($page['slug']) ?>" required>

    <label for="content">Content (HTML):</label>
    <textarea id="content" name="content" rows="10"><?= htmlspecialchars($page['content']) ?></textarea>

    <button type="submit">Update Page</button>
  </form>
  <form method="post" action="<?= $delete_url ?>" onsubmit="return confirm('Are you sure you want to delete this page?');">
    <input type="hidden" name="id" value="<?= (int)$page['id'] ?>">
    <button type="submit" style="color: red;">Delete Page</button>
  </form>
<?php
render_layout('Edit Page', ob_get_clean());