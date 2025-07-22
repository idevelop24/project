<?php


if(file_exists("config.php"))
	require_once('config.php');


require_once(DIR_SYSTEM . "/bootstarp.php");

if(file_exists("start.php"))
	require_once('start.php');

/*
echo $_GET["fake_url"] ."<br/>";
echo $_GET["token"] ."<br/>";
echo $_GET["utm"] ."<br/>";
*/




//$cache->deleteAll();
// Store data
$cache->set('user_profile_321', [1,2]);

// Check existence
if ($cache->has('user_profile_3211')) {
    echo 'ok<br>';
}
else
	echo 'Nokay';

// Delete cache
//$cache->delete('user_profile_123');