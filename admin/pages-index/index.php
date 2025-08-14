<?php
require_once __DIR__ . '/../../bootstrap.php';

/*
 * Load required modules using lazy loading
 */
use function DuckyCMS\dcms_require_module;
dcms_require_module('db');
dcms_require_module('templates');

use function DuckyCMS\dcms_get_base_url;
use function DuckyCMS\DB\dcms_get_all_pages;
use function DuckyCMS\Setup\dcms_render_setup_layout;

$pages           = dcms_get_all_pages();
$create_page_url = dcms_get_base_url() . 'admin/create/';

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
        <a href="<?= dcms_get_base_url() . 'admin/edit/?id=' . $page['id'] ?>">Edit</a>
      </li>
    <?php endforeach; ?>
  </ul>
<?php
dcms_render_setup_layout('Pages', ob_get_clean());