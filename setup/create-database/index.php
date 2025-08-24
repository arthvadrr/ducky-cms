<?php

namespace DuckyCMS\Setup;

use DuckyCMS\AlertType;
use PDOException;
use Random\RandomException;
use function DuckyCMS\DB\initialize_database;
use function DuckyCMS\DB\set_setting;
use function DuckyCMS\dcms_alert;
use function DuckyCMS\dcms_db_exists;
use function DuckyCMS\dcms_get_base_url;
use function DuckyCMS\dcms_require_module;

if (realpath(__FILE__) !== realpath($_SERVER['SCRIPT_FILENAME'])) {
  exit('Nope.');
}

require_once dirname(__DIR__, 2) . '/bootstrap.php';

/*
 * Load required modules using lazy loading
 */

dcms_require_module('db');
dcms_require_module('templates');
dcms_require_module('partials');

session_start();

function dcms_create_db(): string
{
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db_name = 'ducky.sqlite';
    $db_path = DUCKY_ROOT . "/db/$db_name";

    if (file_exists($db_path)) {
      return dcms_alert('Database already exists.', AlertType::warning);
    }

    if (!is_dir(dirname($db_path)) && !mkdir(dirname($db_path), 0755, true)) {
      return dcms_alert('Failed to create database directory.', AlertType::danger);
    }

    try {
      initialize_database(require DUCKY_ROOT . '/db/schema.php', $db_path);

      /**
       * If a site URL was provided earlier in the setup, persist it now that the DB exists
       */
      if (!empty($_SESSION['pending_site_url'])) {
        try {
          set_setting('site_url', (string)$_SESSION['pending_site_url'], $db_path);
        } catch (PDOException) {
          // Non-fatal: continue setup even if site_url fails to persist here
        }
        unset($_SESSION['pending_site_url']);
      }

      /**
       * After successful DB init, generate one-time setup token and store only its hash, expiry, and used flag in settings.
       */
      try {
        $token        = bin2hex(random_bytes(32));
        $token_hash   = hash('sha256', $token);
        $token_expiry = (string)(time() + 900); // 15 minutes expiry

        set_setting('setup_token_hash', $token_hash, $db_path);
        set_setting('setup_token_expiry', $token_expiry, $db_path);
        set_setting('setup_token_used', '0', $db_path);

        /**
         * Bind minimal context to session if needed later (no token value stored)
         */
        $_SESSION['db_initialized'] = true;

        /**
         * Redirect immediately to create-admin page with token in query
         */
        $redirect = dcms_get_base_url() . 'setup/create-admin-user/?token=' . urlencode($token);
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Location: ' . $redirect);
        exit;
      } catch (RandomException $e) {
        return dcms_alert('Error: ' . $e->getMessage(), AlertType::danger);
      }
    } catch (PDOException $e) {
      return dcms_alert('Error: ' . $e->getMessage(), AlertType::danger);
    }
  }

  return '';
}

$message = dcms_create_db();

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
  <a class="button" href="<?= dcms_get_base_url() . 'auth/login/' ?>">Continue to Login</a>
<?php endif;

dcms_render_setup_layout('Create SQLite Database', ob_get_clean());