<?php

namespace DuckyCMS\DB;

use PDO;
use PDOException;
use PDOStatement;

/**
 * Get a PDO database connection
 *
 * @param string|null $db_path Optional database path, uses default if not provided
 * @return PDO
 * @throws PDOException
 */
function get_db_connection(?string $db_path = null): PDO
{
  if ($db_path === null) {
    $db_files = glob(DUCKY_ROOT . '/db/*.sqlite');
    if (empty($db_files)) {
      throw new PDOException('No database file found');
    }
    $db_path = $db_files[0];
  }

  $pdo = new PDO('sqlite:' . $db_path);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  /*
   * Enable SQLite WAL (Write-Ahead Logging) mode and increase cache size
   * for improved concurrent access performance. WAL mode allows multiple
   * readers to access the database simultaneously while a writer is active,
   * which is essential for a CMS where admins might be editing content
   * while visitors browse the site.
   */
  $pdo->exec('PRAGMA journal_mode=WAL');
  $pdo->exec('PRAGMA cache_size=10000');

  return $pdo;
}

/**
 * Execute a prepared statement with parameters
 *
 * @param string $query SQL query
 * @param array $params Parameters for the query
 * @param string|null $db_path Optional database path
 * @return PDOStatement
 * @throws PDOException
 */
function execute_query(string $query, array $params = [], ?string $db_path = null): PDOStatement
{
  $pdo  = get_db_connection($db_path);
  $stmt = $pdo->prepare($query);
  $stmt->execute($params);

  return $stmt;
}

/**
 * Execute a query and return a single row
 *
 * @param string $query SQL query
 * @param array $params Parameters for the query
 * @param string|null $db_path Optional database path
 * @return array|false
 * @throws PDOException
 */
function fetch_single(string $query, array $params = [], ?string $db_path = null): array|false
{
  $stmt = execute_query($query, $params, $db_path);
  return $stmt->fetch(PDO::FETCH_ASSOC);
}

/*
 * =============================================================================
 * PAGE MANAGEMENT FUNCTIONS
 * =============================================================================
 */

/**
 * Get all pages
 *
 * @param string|null $db_path
 * @return array
 * @throws PDOException
 */
function dcms_get_all_pages(?string $db_path = null): array
{
  $query = "SELECT id, title, slug FROM pages ORDER BY id";
  $stmt  = execute_query($query, [], $db_path);
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get a page by ID
 *
 * @param int $id
 * @param string|null $db_path
 * @return array|false
 * @throws PDOException
 */
function get_page_by_id(int $id, ?string $db_path = null): array|false
{
  $query = "SELECT * FROM pages WHERE id = :id";
  return fetch_single($query, [':id' => $id], $db_path);
}

/**
 * Update a page by ID
 *
 * @param int $id
 * @param string $title
 * @param string $slug
 * @param string $content
 * @param string|null $db_path
 * @return bool|string
 * @throws PDOException
 */
function update_page(int $id, string $title, string $slug, string $content, ?string $db_path = null): bool|string
{
  /*
   * Prevent duplicate slug for other pages
   */
  $existing = fetch_single("SELECT id FROM pages WHERE slug = :slug AND id != :id", [':slug' => $slug, ':id' => $id], $db_path);
  if ($existing) {
    return 'Slug already exists';
  }

  $query = "UPDATE pages SET title = :title, slug = :slug, content = :content WHERE id = :id";
  $stmt  = execute_query($query, [
    ':title'   => $title,
    ':slug'    => $slug,
    ':content' => $content,
    ':id'      => $id
  ], $db_path);

  if ($stmt->rowCount() > 0) {
    return true;
  }

  return 'Failed to update page';
}

/**
 * Create a new page
 *
 * @param string $title
 * @param string $slug
 * @param string $content
 * @param string|null $db_path
 * @return int|string Returns new page ID on success, or error message string on failure
 * @throws PDOException
 */
function dcms_create_page(string $title, string $slug, string $content, ?string $db_path = null): int|string
{
  try {
    $existing = fetch_single("SELECT id FROM pages WHERE slug = :slug", [':slug' => $slug], $db_path);
    if ($existing) {
      return 'Slug already exists';
    }

    $pdo = get_db_connection($db_path);
    $query = "INSERT INTO pages (title, slug, content) VALUES (:title, :slug, :content)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
      ':title'   => $title,
      ':slug'    => $slug,
      ':content' => $content
    ]);

    if ($stmt->rowCount() > 0) {
      return (int)$pdo->lastInsertId();
    }

    return 'Failed to create page';
  } catch (PDOException $e) {
    return $e->getMessage();
  }
}

/*
 * =============================================================================
 * USER MANAGEMENT FUNCTIONS
 * =============================================================================
 */

/**
 * Get user by username
 *
 * @param string $username
 * @param string|null $db_path
 * @return array|false
 * @throws PDOException
 */
function get_user_by_username(string $username, ?string $db_path = null): array|false
{
  $query = 'SELECT * FROM users WHERE username = :username';
  return fetch_single($query, [':username' => $username], $db_path);
}

/**
 * Update user session token
 *
 * @param int $user_id
 * @param string $token
 * @param int $created_at
 * @param string|null $db_path
 * @return bool
 * @throws PDOException
 */
function update_user_session_token(int $user_id, string $token, int $created_at, ?string $db_path = null): bool
{
  $query = "UPDATE users SET session_token = :token, token_created_at = :created_at WHERE id = :id";
  $stmt  = execute_query($query, [
    ':token'      => $token,
    ':created_at' => $created_at,
    ':id'         => $user_id
  ], $db_path);

  return $stmt->rowCount() > 0;
}

/**
 * Get user session token
 *
 * @param int $user_id
 * @param string|null $db_path
 * @return string|null
 * @throws PDOException
 */
function get_user_session_token(int $user_id, ?string $db_path = null): ?string
{
  $query  = "SELECT session_token FROM users WHERE id = :id";
  $result = fetch_single($query, [':id' => $user_id], $db_path);

  return $result ? $result['session_token'] : null;
}

/**
 * Create a new user
 *
 * @param string $username
 * @param string $hashed_password
 * @param string|null $db_path
 * @return bool
 * @throws PDOException
 */
function create_user(string $username, string $hashed_password, ?string $db_path = null): bool
{
  $query = "INSERT INTO users (username, password) VALUES (:username, :password)";
  $stmt  = execute_query($query, [
    ':username' => $username,
    ':password' => $hashed_password
  ], $db_path);

  return $stmt->rowCount() > 0;
}

/*
 * =============================================================================
 * SETTINGS MANAGEMENT FUNCTIONS
 * =============================================================================
 */

/**
 * Get a setting value by key
 *
 * @param string $key
 * @param string|null $db_path
 * @return string|null
 * @throws PDOException
 */
function get_setting(string $key, ?string $db_path = null): ?string
{
  $query  = "SELECT value FROM settings WHERE key = :key LIMIT 1";
  $result = fetch_single($query, [':key' => $key], $db_path);

  return $result ? $result['value'] : null;
}

/**
 * Set a setting value (insert or update)
 *
 * @param string $key
 * @param string $value
 * @param string|null $db_path
 * @return bool
 * @throws PDOException
 */
function set_setting(string $key, string $value, ?string $db_path = null): bool
{
  $query = "INSERT INTO settings (key, value) VALUES (:key, :value)
              ON CONFLICT(key) DO UPDATE SET value = excluded.value";
  $stmt  = execute_query($query, [
    ':key'   => $key,
    ':value' => $value
  ], $db_path);

  return $stmt->rowCount() > 0;
}

/*
 * =============================================================================
 * SETUP/NONCE MANAGEMENT FUNCTIONS
 * =============================================================================
 */

/**
 * Get setup nonce by token
 *
 * @param string $token
 * @param string|null $db_path
 * @return array|false
 * @throws PDOException
 */
function get_setup_nonce(string $token, ?string $db_path = null): array|false
{
  $query = "SELECT token, created_at, used FROM setup_nonce WHERE token = :token LIMIT 1";
  return fetch_single($query, [':token' => $token], $db_path);
}

/**
 * Create a setup nonce
 *
 * @param string $token
 * @param int $created_at
 * @param int $used
 * @param string|null $db_path
 * @return bool
 * @throws PDOException
 */
function create_setup_nonce(string $token, int $created_at, int $used = 0, ?string $db_path = null): bool
{
  $query = "INSERT INTO setup_nonce (token, created_at, used) VALUES (:token, :created_at, :used)";
  $stmt  = execute_query($query, [
    ':token'      => $token,
    ':created_at' => $created_at,
    ':used'       => $used
  ], $db_path);

  return $stmt->rowCount() > 0;
}

/**
 * Mark setup nonce as used
 *
 * @param string $token
 * @param string|null $db_path
 * @return bool
 * @throws PDOException
 */
function mark_setup_nonce_used(string $token, ?string $db_path = null): bool
{
  $query = "UPDATE setup_nonce SET used = 1 WHERE token = :token";
  $stmt  = execute_query($query, [':token' => $token], $db_path);

  return $stmt->rowCount() > 0;
}

/*
 * =============================================================================
 * DATABASE INITIALIZATION FUNCTIONS
 * =============================================================================
 */

/**
 * Initialize database with schema
 *
 * @param string $schema_sql
 * @param string $db_path
 * @return bool
 * @throws PDOException
 */
function initialize_database(string $schema_sql, string $db_path): bool
{
  $pdo = new PDO("sqlite:$db_path");
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $pdo->exec($schema_sql);

  return true;
}