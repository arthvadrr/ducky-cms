<?php

require_once __DIR__ . '/../../../bootstrap.php';

use function DuckyCMS\dcms_require_login;
use function DuckyCMS\dcms_require_module;
use function DuckyCMS\DB\dcms_delete_page_forever;

dcms_require_module('auth');
dcms_require_module('db');
dcms_require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
  $id = (int)$_POST['id'];

  if ($id > 0) {
    dcms_delete_page_forever($id);
  }
}

header('Location: /admin/pages/');
exit();