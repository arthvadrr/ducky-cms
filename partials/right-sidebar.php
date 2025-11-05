<?php
/**
 * Renders a right sidebar for admin pages.
 *
 * @param string $content The HTML content to insert into the sidebar.
 */

namespace DuckyCMS;

function dcms_render_right_sidebar(string $content = ''): string
{
  ob_start();
  ?>
  <aside class="right-sidebar">
    <?= $content ?>
  </aside>
  <?php
  return ob_get_clean();
}
