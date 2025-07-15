<?php
/**
 * The initial setup page. The first page of the application.
 */
namespace DuckyCMS\SetupLayout;

require_once '../../templates/admin-layout.php';

$page_title = 'DuckyCMS Setup';

ob_start();
?>
  <section>
    <h2>Welcome to DuckyCMS Setup</h2>
    <p>This little duck needs your help getting started. Ready?</p>
    <a href="step-one.php">Let’s Begin 🐣</a>
  </section>
  <?php render_layout($page_title, ob_get_clean()); ?>