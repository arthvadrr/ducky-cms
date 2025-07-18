<?php
/**
 * This file is step one of the setup. It inits the DB.
 *
 * TODO remove form if db successfully created or navigate to next
 */

namespace DuckyCMS\Setup;

use PDOException;
use Random\RandomException;
use function DuckyCMS\DB\create_setup_nonce;
use function DuckyCMS\DB\initialize_database;
use function DuckyCMS\dcms_db_exists;
use function DuckyCMS\dcms_get_base_url;

define('NONCE_INITIAL_USED_STATE', 0);

/**
 * Exit if not accessed directly.
 */
if (realpath(__FILE__) !== realpath($_SERVER['SCRIPT_FILENAME'])) {
  exit('Nope.');
}

require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once DUCKY_ROOT . '/includes/functions.php';
require_once DUCKY_ROOT . '/templates/admin-layout.php';
require_once DUCKY_ROOT . '/db/interface.php';

/**
 * If we already have a database, redirect to login
 */
if (dcms_db_exists()) {
  header('Location: ' . dcms_get_base_url() . 'auth/login.php');
  exit();
}

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

  /**
   * Prevent multiple submissions
   */
  if (file_exists($db_path)) {
    return '<p>Database already exists.</p>';
  }

  /**
   * Ensure the target directory exists or create it.
   * If it still doesn't exist after mkdir, fail early.
   */
  if (!is_dir(dirname($db_path)) && !mkdir(dirname($db_path), 0755, true)) {
    return '<p>Failed to create database directory.</p>';
  }

  try {
    initialize_database($schema, $db_path);

    /**
     * Save a nonce to the DB and the user's session.
     * It ensures only the original setup flow can create the admin user.
     */
    try {
      $nonce      = bin2hex(random_bytes(32));
      $created_at = time();

      create_setup_nonce($nonce, $created_at, NONCE_INITIAL_USED_STATE, $db_path);

      $_SESSION['setup_nonce'] = $nonce;
      $_SESSION['db_path']     = $db_path;
    } catch (RandomException $error) {
      return "<p>$error</p>";
    }

    $set_site_domain_url = dcms_get_base_url() . 'setup/pages/set-site-domain.php';

    return '<p>Database created successfully! <a href="' . $set_site_domain_url . '">Continue to Set Site URL</a>.</p>';
  } catch (PDOException $e) {
    return '<p>Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
  }
}

$message = dcms_init_db($schema);

ob_start();
?>
  <section>
    <h2>Step 1: Create SQLite Database</h2>
    <p>We'll generate a lightweight SQLite DB to store your site content. Just pick a name.</p>
    <?php if (!dcms_db_exists()) : ?>
      <form method="post">
        <label for="db-name">Database File Name:</label>
        <input id="db-name" name="db_name" type="text" placeholder="ducky" required>
        <span>.sqlite</span>
        <button type="submit">Create Database</button>
      </form>
    <?php endif;
    if (!empty($message)) echo $message; ?>
  </section>
  <?php
render_layout('DuckyCMS Database Setup', ob_get_clean());