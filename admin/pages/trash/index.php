<?php
namespace DuckyCMS\Admin\Pages\Trash;

require_once __DIR__ . '/../../../bootstrap.php';

use function DuckyCMS\DB\dcms_move_page_to_trash;
use function DuckyCMS\dcms_get_base_url;
use function DuckyCMS\dcms_require_login;
use function DuckyCMS\dcms_require_module;

/**
 * Require auth and db
 */
dcms_require_module('auth');
dcms_require_module('db');
dcms_require_login();

/**
 * Handle POST move to trash
 */
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
  $id = (int)($_POST['id'] ?? 0);
  if ($id > 0) {
    dcms_move_page_to_trash($id);
  }
}

/**
 * Redirect back to the listing, prefer referrer
 */
$redirect = $_SERVER['HTTP_REFERER'] ?? (dcms_get_base_url() . 'admin/pages/?status=published');
header('Location: ' . $redirect);
exit;