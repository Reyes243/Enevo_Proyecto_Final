<?php
if ($_SERVER['HTTP_HOST'] === 'localhost' || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false) {
    define('BASE_PATH', '/Enevo_Proyecto_Final/');
} else {
    define('BASE_PATH', '/');
}

define('VIEWS_PATH', BASE_PATH . 'views/');
define('ASSETS_PATH', BASE_PATH . 'assets/');
?>