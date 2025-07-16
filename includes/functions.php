<?php

namespace DuckyCMS;

/**
 * Dynamically define the BASE_URL to support installs in different directories or environments.
 * This constructs the URL using the current protocol, host, and path — minus the script name —
 * so links and redirects work reliably without needing to hardcode a base path.
 *
 * TODO fix get_base_url with either config or base
 */
function dcms_get_base_url(): string {
  $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
  $host = $_SERVER['HTTP_HOST'];

  return "$protocol://$host/";
}

function dcms_db_exists(): bool {
  return !empty( glob(DUCKY_ROOT . 'db/*.sqlite') );
}