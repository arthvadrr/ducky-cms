<?php
/**
 * This file is step two of the setup. It inits the DB.
 */

namespace DuckyCMS\SetupLayout;

require_once dirname(__DIR__, 2) . '/bootstrap.php';

use PDO;
use PDOException;

/**
 * Include the layout for the html
 */
require_once '../../templates/layout.php';

/**
 * Make session available if it exists and make sure we have a db path from step 1.
 */
session_start();

if (!isset($_SESSION['db_path']) || !file_exists($_SESSION['db_path'])) {
  die('No valid database found. Please complete Step 1 first.');
}

/**
 * Handle the layout
 */
$page_title = 'DuckyCMS Create Admin User';
$message    = '';

/**
 * Handle adding user to db
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username']);
  $password = $_POST['password'];

  if ($username && $password) {
    $db_path = $_SESSION['db_path'];

    if (!file_exists($db_path)) {
      die('Database not found.');
    }

    $pdo = new PDO('sqlite:' . $db_path);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $user_insert = "
        INSERT INTO users (username, password) 
        VALUES (:username, :password)
    ";

    $stmt            = $pdo->prepare($user_insert);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
      $stmt->execute([':username' => $username, ':password' => $hashed_password]);
      $message = '<p>User created successfully! <a href="/auth/login.php">Go to login</a>.</p>';
    } catch (PDOException $error) {
      $message = '<p>Error creating user: ' . htmlspecialchars($error->getMessage()) . '</p>';
    }
  } else {
    $message = '<p>Username and password are both required.</p>';
  }
}

ob_start();
?>
  <section>
    <h2>Step 2: Create Admin User</h2>
    <p>Pick a username and password.</p>
    <form method="post">
      <label for="username">Username:</label>
      <input id="username" name="username" type="text" placeholder="ducky_admin" required>
      <label for="password">Password:</label>
      <input id="password" name="password" type="password" placeholder="Temp123" required>
      <button type="submit">Create User</button>
    </form>
    <?php if (!empty($message)) echo $message; ?>
  </section>
  <?php

render_layout($page_title, ob_get_clean());