<?php
//admin panel path
define("ADMIN_DIR",basename(__DIR__));

if(file_exists("../config.php"))
	require_once('../config.php');



require_once(DIR_SYSTEM . "/bootstarp.php");

$registry->set('session', $session);

if(file_exists("router.php"))
    require_once('router.php');

$r = new Router($registry);
$r->Route((isset($_GET["url"])) ? $_GET['url'] : "dashboard");

?>