<?php
/**
 * This file is step one of the setup. It inits the DB.
 *
 * TODO remove form if db successfully created or navigate to next
 */

namespace DuckyCMS\Setup;

use PDO;
use PDOException;
use function DuckyCMS\dcms_get_base_url;

/**
 * Exit if not accessed directly.
 */
if (realpath(__FILE__) !== realpath($_SERVER['SCRIPT_FILENAME'])) {
  exit('Nope.');
}

require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once DUCKY_ROOT . '/includes/functions.php';
require_once DUCKY_ROOT . '/templates/admin-layout.php';

/**
 * We need to store the db name and future settings for later steps.
 */
session_start();

/**
 * If for some reason the session has a db name that no longer exists, unset it.
 */
if (isset($_SESSION['db_path']) && !file_exists($_SESSION['db_path'])) {
  unset($_SESSION['db_path']);
}

$schema = require_once DUCKY_ROOT . '/db/schema.php';

/**
 * Handle db creation.
 *
 * @returns string $message
 */
function dcms_init_db(string $schema): string
{
  $request_method = $_SERVER['REQUEST_METHOD'] ?? '';

  if ($request_method !== 'POST') {
    return '';
  }

  $db_base = basename($_POST['db_name']);

  if (!preg_match('/^[a-zA-Z0-9_-]+$/', $db_base)) {
    return '<p>Invalid database name. Use only letters, numbers, dashes, or underscores.</p>';
  }

  $db_name = $db_base . '.sqlite';
  $db_path = DUCKY_ROOT . "/db/$db_name";

  if (file_exists($db_path)) {
    return '<p>Database file already exists. Choose a different name.</p>';
  }

  if (!file_exists(dirname($db_path))) {
    if (!mkdir(dirname($db_path), 0755, true) && !is_dir(dirname($db_path))) {
      return '<p>Failed to create database directory.</p>';
    }
  }

  try {
    $db = new PDO("sqlite:$db_path");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec($schema);
    $_SESSION['db_path'] = $db_path;
    $step_two_url = dcms_get_base_url() . 'setup/pages/step-two.php';

    return '<p>Database created successfully! <a href="' . $step_two_url . '">Continue to Step 2</a>.</p>';
  } catch (PDOException $error) {
    return '<p>Error: ' . htmlspecialchars($error->getMessage()) . '</p>';
  }
}

$message = dcms_init_db($schema);

ob_start();
?>
  <section>
    <h2>Step 1: Create SQLite Database</h2>
    <p>We'll generate a lightweight SQLite DB to store your site content. Just pick a name.</p>
    <form method="post">
      <label for="db-name">Database File Name:</label>
      <input id="db-name" name="db_name" type="text" placeholder="ducky" required>
      <span>.sqlite</span>
      <button type="submit">Create Database</button>
    </form>
    <?php if (!empty($message)) echo $message; ?>
  </section>

  <?php render_layout('DuckyCMS Database Setup', ob_get_clean()); ?>