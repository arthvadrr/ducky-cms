<?php
namespace DuckyCMS\Admin\Pages\Restore;

require_once __DIR__ . '/../../../bootstrap.php';

use function DuckyCMS\dcms_require_login;
use function DuckyCMS\dcms_require_module;
use function DuckyCMS\dcms_get_base_url;
use function DuckyCMS\DB\dcms_restore_page;

/**
 * Require auth and db
 */
dcms_require_module('auth');
dcms_require_module('db');
dcms_require_login();

/**
 * Handle POST restore -> draft
 */
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
  $id = (int)($_POST['id'] ?? 0);
  if ($id > 0) {
    dcms_restore_page($id);
  }
}

/**
 * Redirect back to the listing, prefer referrer
 */
$redirect = $_SERVER['HTTP_REFERER'] ?? (dcms_get_base_url() . 'admin/pages/?status=trash');
header('Location: ' . $redirect);
exit;