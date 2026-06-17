<?php
$requestUri = $_SERVER['REQUEST_URI'];
$scriptName = $_SERVER['SCRIPT_NAME'];

// Detect if running under /techshop/ subfolder
if (strpos($scriptName, '/techshop/') !== false) {
    define('BASE_URL', '/techshop');
    define('ASSETS_URL', '/techshop/assets');
} else {
    define('BASE_URL', '');
    define('ASSETS_URL', '/assets');
}
?>