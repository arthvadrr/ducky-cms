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

function render_layout(string $title = '', string $content = ''): void
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
    <link rel="stylesheet" href=<?= $base_url . "dist/build.css" ?>>
    <title><?= htmlspecialchars($title) ?></title>
  </head>
  <body>
  <div class="content">
    <div class="content-inner">
      <header>
        <span class="branding">
          <?= render_ducky_logo() ?>
          <span class="site-title">ducky-cms</span>
        </span>
      </header>
      <main>
        <h1>
          <?= htmlspecialchars($title) ?>
        </h1>
        <?= $content ?>
      </main>
      <footer>
        <span>&copy; <?= date('Y') ?> ducky-cms</span>
      </footer>
    </div>
  </div>
  </body>
  </html>
  <?php
}