<?php
/**
 * The initial setup page. The first page of the application.
 */

namespace DuckyCMS\Setup;

require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once DUCKY_ROOT . '/includes/functions.php';
require_once DUCKY_ROOT . '/templates/admin-layout.php';

use function DuckyCMS\dcms_db_exists;
use function DuckyCMS\dcms_get_base_url;

$has_db       = dcms_db_exists();
$base_url = dcms_get_base_url();
$step_one_url = $base_url . 'setup/pages/step-one.php';
$login_url    = $base_url . 'auth/login.php';

ob_start();
?>
  <section>
    <h2>Welcome to DuckyCMS Setup</h2>
    <p>This little duck needs your help getting started. Ready?</p>

    <?php if ($has_db) : ?>
      <p>
        Looks like you already have a database.
        <a href="<?= $login_url ?>">Login to DuckyCMS</a>.
      </p>
      <p>
        If you want to create a new database, delete the current one and return to this page.
      </p>
    <?php endif; ?>
    <a href="<?= $step_one_url; ?>">Let’s Begin 🐣</a>
  </section>

  <?php render_layout('DuckyCMS Setup', ob_get_clean()); ?>