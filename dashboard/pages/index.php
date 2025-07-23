<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once DUCKY_ROOT . '/db/interface.php';
require_once DUCKY_ROOT . '/includes/functions.php';
require_once DUCKY_ROOT . '/templates/admin-layout.php';

use function DuckyCMS\dcms_get_base_url;
use function DuckyCMS\DB\dcms_get_all_pages;
use function DuckyCMS\Setup\render_layout;

$pages           = dcms_get_all_pages();
$create_page_url = dcms_get_base_url() . 'dashboard/pages/create.php';

ob_start();
?>
  <aside>
    <a href="<?= $create_page_url ?>">Create New Page</a>
  </aside>
  <ul>
    <?php foreach ($pages as $page): ?>
      <li>
        <strong><?= htmlspecialchars($page['title']) ?></strong>
        <br>
        <code>/<?= htmlspecialchars($page['slug']) ?></code>
        <br>
        <a href="<?= dcms_get_base_url() . 'dashboard/pages/edit.php?id=' . $page['id'] ?>">Edit</a>
      </li>
    <?php endforeach; ?>
  </ul>
<?php
render_layout('Pages', ob_get_clean());