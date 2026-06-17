<?php
if (strpos($_SERVER['REQUEST_URI'], '/techshop') !== false) {
    define('BASE_URL', '/techshop');
    define('ASSETS_URL', '/techshop/assets');
} else {
    define('BASE_URL', '');
    define('ASSETS_URL', '/assets');
}
?>