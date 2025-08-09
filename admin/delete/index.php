<?php

require_once __DIR__ . '/../../bootstrap.php';

use function DuckyCMS\dcms_require_login;
use function DuckyCMS\DB\execute_query;

dcms_require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
  $id = (int)$_POST['id'];

  if ($id > 0) {
    execute_query("DELETE FROM pages WHERE id = :id", [':id' => $id]);
  }
}

header('Location: /admin/pages');
exit();