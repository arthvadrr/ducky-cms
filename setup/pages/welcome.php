<?php
/**
 * The initial setup page. The first page of the application.
 */

namespace DuckyCMS\Setup;

require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once DUCKY_ROOT . '/includes/functions.php';
require_once DUCKY_ROOT . '/templates/setup-layout.php';

use function DuckyCMS\dcms_db_exists;
use function DuckyCMS\dcms_get_base_url;

$has_db        = dcms_db_exists();
$base_url      = dcms_get_base_url();
$create_db_url = $base_url . 'setup/pages/create-database.php';
$login_url     = $base_url . 'auth/login.php';

ob_start();
?>
  <section>
    <?php if ($has_db) : ?>
      <p>
        Looks like you already have a database.
        <a href="<?= $login_url ?>">Login to DuckyCMS</a>.
      </p>
      <p>
        If you want to create a new database, delete the current one and return to this page.
      </p>
    <?php else: ?>
      <p>This little ducky needs your help getting started. Ready?</p>
      <a class="button" href="<?= $create_db_url; ?>">Let’s Begin 🐣</a>
    <?php endif; ?>
  </section>

  <?php render_layout('Get Started', ob_get_clean()); ?>