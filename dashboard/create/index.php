<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once DUCKY_ROOT . '/templates/setup-layout.php';
require_once DUCKY_ROOT . '/includes/functions.php';
require_once DUCKY_ROOT . '/db/interface.php';

use function DuckyCMS\DB\dcms_create_page;
use function DuckyCMS\dcms_get_base_url;
use function DuckyCMS\Setup\render_layout;

$message = '';
$title   = '';
$slug    = '';
$content = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title   = trim($_POST['title'] ?? '');
  $slug    = trim($_POST['slug'] ?? '');
  $content = $_POST['content'] ?? '';

  if ($title && $slug) {
    $result = dcms_create_page($title, $slug, $content);

    if (is_int($result)) {
      header("Location: " . dcms_get_base_url() . "dashboard/edit/?id=$result");
      exit();
    }

    $message = "<p>$result</p>";
  } else {
    $message = '<p>Title and slug are required.</p>';
  }
}

$pages_url = dcms_get_base_url() . 'dashboard/pages-index/';

ob_start();
?>
  <h2>Create Page</h2>
  <a href="<?= $pages_url ?>">Back to Pages</a>
<?= $message ?>
  <form method="post">
    <label for="title">Title:</label>
    <input type="text" id="title" name="title" value="<?= htmlspecialchars($title) ?>" required>

    <label for="slug">Slug:</label>
    <input type="text" id="slug" name="slug" value="<?= htmlspecialchars($slug) ?>" required>

    <label for="content">HTML Content:</label>
    <textarea id="content" name="content" rows="10" cols="50"><?= htmlspecialchars($content) ?></textarea>

    <button type="submit">Create Page</button>
  </form>
<?php
$page_content = ob_get_clean();

render_layout('Create Page', $page_content);