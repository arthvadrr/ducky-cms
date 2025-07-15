<?php
/**
 * The initial setup page. The first page of the application.
 */

namespace DuckyCMS\Setup;

require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once DUCKY_ROOT . '/includes/functions.php';
require_once DUCKY_ROOT . '/templates/admin-layout.php';

use function DuckyCMS\dcms_get_base_url;

ob_start();
?>
  <section>
    <h2>Welcome to DuckyCMS Setup</h2>
    <p>This little duck needs your help getting started. Ready?</p>
    <a href="<?= dcms_get_base_url() . 'setup/pages/step-one.php' ?>">Let’s Begin 🐣</a>
  </section>

  <?php render_layout('DuckyCMS Setup', ob_get_clean()); ?>