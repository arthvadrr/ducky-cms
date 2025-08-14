<?php
require_once __DIR__ . '/../../../bootstrap.php';

/*
 * Load required modules using lazy loading
 */

use function DuckyCMS\DB\execute_query;
use function DuckyCMS\DB\get_page_by_id;
use function DuckyCMS\DB\update_page;
use function DuckyCMS\dcms_get_base_url;
use function DuckyCMS\dcms_require_login;
use function DuckyCMS\dcms_require_module;
use function DuckyCMS\Setup\dcms_render_setup_layout;

dcms_require_module('db');
dcms_require_module('templates');
dcms_require_module('auth');

dcms_require_login();

$page_id = $_GET['id'] ?? null;
if (!$page_id || !is_numeric($page_id)) {
  header('Location: /admin/pages/');
  exit;
}

$page = get_page_by_id((int)$page_id);
if (!$page) {
  header('Location: /admin/pages/');
  exit;
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title   = $_POST['title'] ?? '';
  $slug    = $_POST['slug'] ?? '';
  $content = $_POST['content'] ?? '';

  // Optional status update via select (only draft/published here)
  $newStatus = $_POST['status'] ?? ($page['status'] ?? 'draft');
  if (!in_array($newStatus, ['draft', 'published'], true)) {
    $newStatus = $page['status'] ?? 'draft';
  }

  $result = update_page($page_id, $title, $slug, $content);

  // If content update succeeded and status changed, persist it
  if ($result === true && ($page['status'] ?? null) !== $newStatus) {
    execute_query("UPDATE pages SET status = :s WHERE id = :id", [':s' => $newStatus, ':id' => (int)$page_id]);
  }

  if ($result === true) {
    $message = '<p>Page updated successfully.</p>';
    $page    = get_page_by_id((int)$page_id);
  } elseif (is_string($result)) {
    $message = "<p>$result</p>";
  } else {
    $message = '<p>Failed to update page.</p>';
  }
}

$pages_url   = dcms_get_base_url() . 'admin/pages/?status=' . urlencode($page['status'] ?? 'published');
$trash_url   = dcms_get_base_url() . 'admin/pages/trash/';
$restore_url = dcms_get_base_url() . 'admin/pages/restore/';

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

    <label for="status">Status:</label>
    <select id="status" name="status">
      <option value="draft" <?= ($page['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
      <option value="published" <?= ($page['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
    </select>

    <button type="submit">Update Page</button>
  </form>
<?php if (($page['status'] ?? '') !== 'trash'): ?>
  <form method="post" action="<?= $trash_url ?>" onsubmit="return confirm('Move to trash?');">
    <input type="hidden" name="id" value="<?= (int)$page['id'] ?>">
    <button type="submit">Move to trash</button>
  </form>
<?php else: ?>
  <form method="post" action="<?= $restore_url ?>">
    <input type="hidden" name="id" value="<?= (int)$page['id'] ?>">
    <button type="submit">Restore to draft</button>
  </form>
  <form method="post" action="<?= dcms_get_base_url() . 'admin/pages/delete/' ?>"
        onsubmit="return confirm('Delete forever?');">
    <input type="hidden" name="id" value="<?= (int)$page['id'] ?>">
    <button type="submit" style="color: red;">Delete forever</button>
  </form>
<?php endif; ?>
<?php
dcms_render_setup_layout('Edit Page', ob_get_clean());