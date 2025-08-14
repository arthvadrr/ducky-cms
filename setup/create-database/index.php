<?php

namespace DuckyCMS\Setup;

use DuckyCMS\AlertType;
use PDOException;
use Random\RandomException;
use function DuckyCMS\DB\create_setup_nonce;
use function DuckyCMS\DB\initialize_database;
use function DuckyCMS\dcms_alert;
use function DuckyCMS\dcms_db_exists;
use function DuckyCMS\dcms_get_base_url;

define('NONCE_INITIAL_USED_STATE', 0);

if (realpath(__FILE__) !== realpath($_SERVER['SCRIPT_FILENAME'])) {
  exit('Nope.');
}

require_once dirname(__DIR__, 2) . '/bootstrap.php';

/*
 * Load required modules using lazy loading
 */
use function DuckyCMS\dcms_require_module;
dcms_require_module('db');
dcms_require_module('templates');
dcms_require_module('partials');

session_start();

if (isset($_SESSION['db_path']) && !file_exists($_SESSION['db_path'])) {
  unset($_SESSION['db_path']);
}

function dcms_create_db(): string
{
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db_name = 'ducky.sqlite';
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
        $nonce      = bin2hex(random_bytes(32));
        $created_at = time();

        create_setup_nonce($nonce, $created_at, NONCE_INITIAL_USED_STATE, $db_path);

        $_SESSION['setup_nonce'] = $nonce;
        $_SESSION['db_path']     = $db_path;
      } catch (RandomException $e) {
        return '<p>Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
      }

      $next_url = dcms_get_base_url() . 'setup/set-site-url/';
      return '<p>Database created successfully!</p> <a class="button" href="' . $next_url . '">Continue to Set Site URL</a>';
    } catch (PDOException $e) {
      return '<p>Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
    }
  }

  return '';
}

$message      = dcms_create_db();
$set_site_url = dcms_get_base_url() . 'setup/set-site-url/';

ob_start();

if (!dcms_db_exists()) : ?>
  <p>This will generate a lightweight SQLite DB named <code>ducky.sqlite</code> to store your site content.</p>
  <form method="post">
    <button class="button" type="submit">Create Database</button>
  </form>
<?php endif;

echo $message;

if (dcms_db_exists() && empty($message)) : ?>
  <?= dcms_alert('Database already exists.', AlertType::warning) ?>
  <a class="button" href="<?= $set_site_url ?>">Continue to Set Site URL</a>
<?php endif;

dcms_render_setup_layout('Create SQLite Database', ob_get_clean());