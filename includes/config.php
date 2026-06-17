<?php
if (!defined('BASE_URL')) {
    if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] === 'localhost') {
        define('BASE_URL', '/techshop');
        define('ASSETS_URL', '/techshop/assets');
    } else {
        define('BASE_URL', '');
        define('ASSETS_URL', '/assets');
    }
}
?>