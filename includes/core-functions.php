<?php

namespace DuckyCMS;

/**
 * Dynamically define the BASE_URL to support installs in different directories or environments.
 * This constructs the URL using the current protocol, host, and path — minus the script name —
 * so links and redirects work reliably without needing to hardcode a base path.
 *
 * @return string The base URL for the application
 */
function dcms_get_base_url(): string {
  $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
  $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

  return "$protocol://$host/";
}

/**
 * Check if a database file exists in the db directory.
 * Looks for any .sqlite files in the DUCKY_ROOT/db/ directory.
 *
 * @return bool True if database exists, false otherwise
 */
function dcms_db_exists(): bool {
  return !empty( glob(DUCKY_ROOT . '/db/*.sqlite') );
}

/**
 * Lazy loading function for DuckyCMS modules.
 * Uses a configuration-driven approach for better maintainability and extensibility.
 *
 * @param string $module The module to load (auth, db, templates, partials)
 * @return bool True if module loaded successfully, false otherwise
 */
function dcms_require_module(string $module): bool {
  static $loaded_modules = [];
  static $module_config = null;
  
  /*
   * Return early if module already loaded
   */
  if (isset($loaded_modules[$module])) {
    return true;
  }
  
  /*
   * Initialize module configuration on first use
   */
  if ($module_config === null) {
    $module_config = [
      'auth' => [
        'files' => [DUCKY_ROOT . '/includes/auth-functions.php']
      ],
      'db' => [
        'files' => [DUCKY_ROOT . '/db/interface.php']
      ],
      'templates' => [
        'files' => [DUCKY_ROOT . '/templates/setup-layout.php']
      ],
      'admin' => [
        'files' => [DUCKY_ROOT . '/templates/admin-layout.php']
      ],
      'partials' => [
        'files' => [
          DUCKY_ROOT . '/partials/alert.php',
          DUCKY_ROOT . '/partials/ducky-cms-logo.php',
          DUCKY_ROOT . '/partials/menu-item.php'
        ]
      ]
    ];
  }
  
  /*
   * Check if module exists in configuration
   */
  if (!isset($module_config[$module])) {
    return false;
  }
  
  $config = $module_config[$module];
  $all_files_loaded = true;
  
  /*
   * Load all files for this module
   */
  foreach ($config['files'] as $file_path) {
    if (!file_exists($file_path)) {
      $all_files_loaded = false;
      break;
    }
    require_once $file_path;
  }
  
  if ($all_files_loaded) {
    $loaded_modules[$module] = true;
    return true;
  }
  
  return false;
}