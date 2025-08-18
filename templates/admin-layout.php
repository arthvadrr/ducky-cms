<?php
/**
 * Renders the full HTML layout for a page.
 *
 * @param string $title The page title shown in the title tag and header.
 * @param string $content The HTML content to insert into the main section.
 * @param string|null $current_menu Optional: which menu item is current
 */

namespace DuckyCMS\Setup;

use function DuckyCMS\dcms_get_base_url;
use function DuckyCMS\dcms_render_ducky_logo;
use function DuckyCMS\dcms_render_menu;
use function DuckyCMS\dcms_require_module;

function dcms_render_dashboard_layout(
  string      $title = '',
  string      $content = '',
  string|null $current_menu_item = null): void
{
  dcms_require_module('partials');
  $base_url      = dcms_get_base_url();
  $dashboard_url = $base_url . 'admin/';
  $pages_url     = $base_url . 'admin/pages/';
  $settings_url  = $base_url . 'admin/settings/';
  $logout_url    = $base_url . 'auth/logout/';
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
      <?= dcms_render_ducky_logo(["width" => 35]) ?>
      <span class="site-title">ducky-cms</span>
    </div>
    <nav role="navigation" aria-label="Main menu">
      <div class="nav-inner">
        <?= dcms_render_menu([
          [
            'name'       => 'Dashboard',
            'width'      => 25,
            'href'       => $dashboard_url,
            'is_current' => ($current_menu_item === 'dashboard')
          ],
          [
            'name'       => 'Pages',
            'width'      => 25,
            'href'       => $pages_url,
            'is_current' => in_array($current_menu_item, ['pages','pages-create','pages-drafts','pages-trash'], true),
            'children'   => [
              [
                'name'       => 'Create Page',
                'href'       => $pages_url . 'create/',
                'is_current' => ($current_menu_item === 'pages-create'),
              ],
              [
                'name'       => 'Drafts',
                'href'       => $pages_url . '?status=draft',
                'is_current' => ($current_menu_item === 'pages-drafts'),
              ],
              [
                'name'       => 'Published',
                'href'       => $pages_url . '?status=published',
                'is_current' => ($current_menu_item === 'pages-published'),
              ],
              [
                'name'       => 'Trash',
                'href'       => $pages_url . '?status=trash',
                'is_current' => ($current_menu_item === 'pages-trash'),
              ],
            ],
          ]
        ])
        ?>
        <?= dcms_render_menu([
          [
            'name'       => 'Settings',
            'width'      => 25,
            'href'       => $settings_url,
            'is_current' => ($current_menu_item === 'settings')
          ],
          [
            'name'  => 'Logout',
            'width' => 25,
            'href'  => $logout_url,
          ],
        ]);
        ?>
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