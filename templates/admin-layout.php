<?php
/**
 * Renders the full HTML layout for a page.
 *
 * @param string $title The page title shown in the title tag and header.
 * @param string $content The HTML content to insert into the main section.
 */

namespace DuckyCMS\SetupLayout;

function render_layout(string $title = '', string $content = ''): void
{
  ?>
  <!DOCTYPE html>
  <html lang="us">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A cozy handcrafted CMS.">
    <meta name="author" content="Ducky">
    <title><?= htmlspecialchars($title) ?></title>
  </head>
  <body>
  <header>
    <h1><?= htmlspecialchars($title) ?></h1>
  </header>
  <main>
    <?= $content ?>
  </main>
  <footer>
    <p>&copy; <?= date('Y') ?> DuckyCMS</p>
  </footer>
  </body>
  </html>
  <?php
}