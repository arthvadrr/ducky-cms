<?php
/**
 * Set up DB connection
 */

namespace DuckyCMS\Layout;

require_once '../../templates/layout.php';

$page_title = 'DuckyCMS Setup';
ob_start();
?>
  <h2>Step 1: Database Setup</h2>
  <p>We’ll ask for your database info next (but not really yet).</p>
  <form>
    <label for="db-host">DB Host:</label><input id="db-host" type="text" placeholder="localhost"><br>
    <label for="db-name">DB Name:</label><input id="db-name" type="text" placeholder="ducky"><br>
    <button>Next</button>
  </form>
  <?php render_layout($page_title, ob_get_clean()); ?>