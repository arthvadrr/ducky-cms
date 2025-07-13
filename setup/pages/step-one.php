<?php
/**
 * This file is step one of the setup. It inits the DB.
 */

namespace DuckyCMS\SetupLayout;

/**
 * Exit if not accessed directly
 */
if (realpath(__FILE__) !== realpath($_SERVER['SCRIPT_FILENAME'])) {
  exit('Nope.');
}

use PDO;
use PDOException;

require_once '../../templates/layout.php';

if (!defined('DUCKY_ROOT')) {
  define('DUCKY_ROOT', dirname(__DIR__, 2));
}

/**
 * We need to store the db name and future settings for later steps
 */
session_start();
$page_title = 'DuckyCMS Database Setup';
$message    = '';


/**
 * Handle db creation. Session is used to store the db name for the next step
 */
$schema = require DUCKY_ROOT . '/db/schema.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $db_base = basename($_POST['db_name']);
  $db_name = $db_base . '.sqlite';
  $db_path = __DIR__ . "/../../db/$db_name";

  if (file_exists($db_path)) {
    $message = '<p>Database file already exists. Choose a different name.</p>';
  } else {
    if (!file_exists(dirname($db_path))) {
      mkdir(dirname($db_path), 0755, true);
    }

    try {
      $db = new PDO("sqlite:$db_path");
      $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $db->exec($schema);
      $_SESSION['db_path'] = $db_path;
      $message             = '<p>Database created successfully!</p><p><a href="step-two.php">Continue to Step 2</a></p>';
    } catch (PDOException $error) {
      $message = '<p>Error: ' . htmlspecialchars($error->getMessage()) . '</p>';
    }
  }
}

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
  <?php

render_layout($page_title, ob_get_clean());
?>