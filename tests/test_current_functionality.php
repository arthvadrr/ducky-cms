<?php
/*
 * Test current bootstrap and functions
 */
require_once dirname(__DIR__) . '/bootstrap.php';

echo "Testing current functionality:\n";

/*
 * Test core functions
 */
echo "1. Testing dcms_get_base_url(): ";
try {
    $base_url = \DuckyCMS\dcms_get_base_url();
    echo "✓ Success: " . $base_url . "\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "2. Testing dcms_db_exists(): ";
try {
    $db_exists = \DuckyCMS\dcms_db_exists();
    echo "✓ Success: " . ($db_exists ? "DB exists" : "DB not found") . "\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "3. Testing dcms_get_site_url() with lazy loading: ";
try {
    \DuckyCMS\dcms_require_module('auth');
    $site_url = \DuckyCMS\dcms_get_site_url();
    echo "✓ Success: " . $site_url . "\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

/*
 * Test partials loading
 */
echo "4. Testing partials loading: ";
try {
    \DuckyCMS\dcms_require_module('partials');
    if (function_exists('DuckyCMS\render_ducky_logo')) {
        echo "✓ Success: render_ducky_logo function available\n";
    } else {
        echo "✗ Error: render_ducky_logo function not available\n";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\nCurrent functionality test completed.\n";