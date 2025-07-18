<?php
//admin panel path
define("ADMIN_DIR",basename(__DIR__));

if(file_exists("../config.php"))
	require_once('../config.php');



require_once(DIR_SYSTEM . "/bootstarp.php");

$session = new \Framework\Library\Session($config, $request, $log, 'admin_');
$registry->set('session', $session);
$session->start();

if(file_exists("router.php"))
    require_once('router.php');

$r = new Router($registry);
$r->Route((isset($_GET["url"])) ? $_GET['url'] : "dashboard");

?>