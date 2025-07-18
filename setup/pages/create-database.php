<?php

namespace DuckyCMS\Setup;

use PDOException;
use Random\RandomException;
use function DuckyCMS\DB\create_setup_nonce;
use function DuckyCMS\DB\initialize_database;
use function DuckyCMS\dcms_db_exists;
use function DuckyCMS\dcms_get_base_url;

define('NONCE_INITIAL_USED_STATE', 0);

if (realpath(__FILE__) !== realpath($_SERVER['SCRIPT_FILENAME'])) {
  exit('Nope.');
}

require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once DUCKY_ROOT . '/includes/functions.php';
require_once DUCKY_ROOT . '/templates/admin-layout.php';
require_once DUCKY_ROOT . '/db/interface.php';

session_start();

if (isset($_SESSION['db_path']) && !file_exists($_SESSION['db_path'])) {
  unset($_SESSION['db_path']);
}

function dcms_create_db(): string {
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['db_name'])) {
    $db_base = basename($_POST['db_name']);

    if (!preg_match('/^[a-zA-Z0-9_-]+$/', $db_base)) {
      return '<p>Invalid database name. Use only letters, numbers, dashes, or underscores.</p>';
    }

    $db_name = $db_base . '.sqlite';
    $db_path = DUCKY_ROOT . "/db/$db_name";

    if (file_exists($db_path)) {
      return '<p>Database already exists.</p>';
    }

    if (!is_dir(dirname($db_path)) && !mkdir(dirname($db_path), 0755, true)) {
      return '<p>Failed to create database directory.</p>';
    }

    try {
      initialize_database(require DUCKY_ROOT . '/db/schema.php', $db_path);

      try {
        $nonce = bin2hex(random_bytes(32));
        $created_at = time();

        create_setup_nonce($nonce, $created_at, NONCE_INITIAL_USED_STATE, $db_path);

        $_SESSION['setup_nonce'] = $nonce;
        $_SESSION['db_path']     = $db_path;
      } catch (RandomException $e) {
        return '<p>Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
      }

      $next_url = dcms_get_base_url() . 'setup/pages/set-site-domain.php';
      return '<p>Database created successfully! <a href="' . $next_url . '">Continue to Set Site URL</a>.</p>';
    } catch (PDOException $e) {
      return '<p>Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
    }
  }

  return '';
}

$message = dcms_create_db();

ob_start();
?>
  <section>
    <h2>Step 1: Create SQLite Database</h2>
    <?= $message ?>
    <p>We'll generate a lightweight SQLite DB to store your site content. Just pick a name.</p>
    <?php if (!dcms_db_exists()) : ?>
      <form method="post">
        <label for="db-name">Database File Name:</label>
        <input id="db-name" name="db_name" type="text" placeholder="ducky" required>
        <span>.sqlite</span>
        <button type="submit">Create Database</button>
      </form>
    <?php endif; ?>
  </section>
  <?php
render_layout('DuckyCMS Database Setup', ob_get_clean());