<?php
/**
 * Renders the full HTML layout for a page.
 *
 * @param string $title The page title shown in the title tag and header.
 * @param string $content The HTML content to insert into the main section.
 */

namespace DuckyCMS\Setup;

use function DuckyCMS\dcms_get_base_url;
use function DuckyCMS\dcms_require_module;
use function DuckyCMS\render_ducky_logo;

function render_dashboard_layout(string $title = '', string $content = ''): void
{
  dcms_require_module('partials');
  $base_url = dcms_get_base_url();
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
      <?= render_ducky_logo(["width" => 50]) ?>
      <span class="site-title">ducky-cms</span>
    </div>
    <nav>
      <div>
        <ul>
          <li><a href="#">Pages</a></li>
        </ul>
        <ul>
          <li><a href="#">Settings</a></li>
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