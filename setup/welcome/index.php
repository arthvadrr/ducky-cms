<?php
/**
 * The initial setup page. The first page of the application.
 */

namespace DuckyCMS\Setup;

require_once dirname(__DIR__, 2) . '/bootstrap.php';

/*
 * Load required modules using lazy loading
 */
use function DuckyCMS\dcms_require_module;
dcms_require_module('templates');
dcms_require_module('partials');

use function DuckyCMS\dcms_db_exists;
use function DuckyCMS\dcms_get_base_url;

// Alert helpers
use function DuckyCMS\dcms_alert;
use DuckyCMS\AlertType;

$has_db        = dcms_db_exists();
$base_url      = dcms_get_base_url();
$create_db_url = $base_url . 'setup/create-database/';
$login_url     = $base_url . 'auth/login/';

ob_start();
?>
  <section>
    <h2>Alert Samples</h2>
    <div class="stack" style="display: grid; gap: .5rem; margin-bottom: 1rem;">
      <?= dcms_alert('This is an info alert — nice and neutral.') ?>
      <?= dcms_alert('Success! Something went right.', AlertType::success) ?>
      <?= dcms_alert('Heads up! You might want to check this.', AlertType::warning) ?>
      <?= dcms_alert('Uh-oh! Something went wrong.', AlertType::danger) ?>
    </div>

    <?php if ($has_db) : ?>
      <p>
        Looks like you already have a database.
      </p>
      <p>
        If you want to create a new database, delete the current one and return to this page.
      </p>
      <a class="button" href="<?= $login_url ?>">Log in to ducky-cms</a>
    <?php else: ?>
      <p>This little ducky needs your help getting started. Ready?</p>
      <a class="button" href="<?= $create_db_url; ?>">Let’s Begin 🐣</a>
    <?php endif; ?>
  </section>

  <?php dcms_render_setup_layout('Get Started', ob_get_clean()); ?>