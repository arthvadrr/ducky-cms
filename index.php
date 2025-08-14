<?php
require_once __DIR__ . '/bootstrap.php';

use function DuckyCMS\dcms_db_exists;


function dcms_handle_root_redirect(): never {
  if (!dcms_db_exists()) {
    header('Location: setup/welcome/');
    exit;
  }

  if (isset($_COOKIE[session_name()])) {
    session_start();

    if (isset($_SESSION['user_id'], $_SESSION['session_token'])) {
      header('Location: admin/pages/');
      exit;
    }
  }

  header('Location: auth/login/');
  exit;
}

dcms_handle_root_redirect();