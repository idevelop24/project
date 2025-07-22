<?php

define('HTTP_SERVER', 'http://localhost/vanilla/');
define("MAGNET_DIR",str_replace('\\', '/', realpath(dirname(__FILE__) . '/')));
define("DIR_SYSTEM",MAGNET_DIR."/framework");
define('DIR_STORAGE', DIR_SYSTEM . "/storage/");
define('DIR_CONFIG', DIR_SYSTEM . "/config/");
define('DIR_BACKUP', DIR_STORAGE . 'backup/');
define('DIR_CACHE', DIR_STORAGE . 'cache/');
define('DIR_CACHE_IMAGES', DIR_CACHE . 'images/');
define('DIR_LOGS', DIR_STORAGE . 'logs/');
define('DIR_SESSION', DIR_STORAGE . 'session/');

//image Uploads Location
define('DIR_IMAGE', MAGNET_DIR."/uploads/");
//Posts Image Uploads Location
define('DIR_POSTS_IMAGE', DIR_IMAGE."posts/");
define('DIR_POSTS_CATEGORY_IMAGE', DIR_POSTS_IMAGE."category/");
define('DIR_POSTS_ITEM_IMAGE', DIR_POSTS_IMAGE."items/");

//Database Config
//use pdo 
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'vanilla');
define('DB_PORT', '3306');
define('DB_PREFIX', 'tbl_');
?>