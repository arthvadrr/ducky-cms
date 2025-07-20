<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../db/interface.php';
require_once __DIR__ . '/../bootstrap.php';

use function DuckyCMS\DB\dcms_get_all_pages;
use function DuckyCMS\dcms_get_base_url;
use function DuckyCMS\Setup\render_layout;

$pages           = dcms_get_all_pages();
$create_page_url = dcms_get_base_url() . 'setup/pages/create-page.php';

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
      </li>
    <?php endforeach; ?>
  </ul>
<?php
render_layout('Pages', ob_get_clean());