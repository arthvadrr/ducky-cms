<?php
/**
 * Renders the full HTML layout for a page.
 *
 * @param string $title The page title shown in the title tag and header.
 * @param string $content The HTML content to insert into the main section.
 * @param string|null $current_menu Optional: which menu item is current ('dashboard'|'pages'|'settings').
 */

namespace DuckyCMS\Setup;

use function DuckyCMS\dcms_get_base_url;
use function DuckyCMS\dcms_menu_item;
use function DuckyCMS\dcms_render_ducky_logo;
use function DuckyCMS\dcms_require_module;

function dcms_render_dashboard_layout(string $title = '', string $content = '', $current_menu = null): void
{
  dcms_require_module('partials');
  $base_url      = dcms_get_base_url();
  $dashboard_url = $base_url . 'admin/';
  $pages_url     = $base_url . 'admin/pages-index/';
  $settings_url  = $base_url . 'admin/settings/';
  ?>
  <!DOCTYPE html>
  <html lang="en" style="background-color:#23272d;">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A cozy handcrafted CMS.">
    <meta name="author" content="Ducky">
    <link rel="stylesheet" href=<?= $base_url . "dist/admin.css" ?>>
    <title><?= htmlspecialchars($title) ?></title>
  </head>
  <body>
  <aside>
    <div class="branding">
      <?= dcms_render_ducky_logo(["width" => 45]) ?>
      <span class="site-title">ducky-cms</span>
    </div>
    <nav role="navigation" aria-label="Main menu">
      <div class="nav-inner">
        <ul>
          <li>
            <?= dcms_menu_item([
              'name'  => 'Dashboard',
              'width' => 30,
              'href'  => $dashboard_url,
              'is_current' => ($current_menu === 'dashboard')
            ]) ?>
          </li>
          <li>
            <?= dcms_menu_item([
              'name'  => 'Pages',
              'width' => 30,
              'href'  => $pages_url,
              'is_current' => ($current_menu === 'pages')
            ]) ?>
          </li>
        </ul>
        <ul>
          <li>
            <?= dcms_menu_item([
              'name'  => 'Settings',
              'width' => 30,
              'href'  => $settings_url,
              'is_current' => ($current_menu === 'settings')
            ]) ?>
          </li>
        </ul>
      </div>
    </nav>
  </aside>
  <main>
    <?= $content ?>
  </main>
  </body>
  </html>
  <?php
}