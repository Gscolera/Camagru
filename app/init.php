<?php

define('ROOT', dirname(__DIR__));
define('APP', ROOT . '/app');
define('WWW', ROOT . '/public');
define('VIEWS', APP . '/views');
define('LAYOUT', VIEWS . '/layoutDefault.php');
define('TMP_FILE', '/tmp/camagru.jpg');

spl_autoload_register('classLoader');

function classLoader($class)
{
	$file = ROOT . '/' . str_replace('\\', '/', $class) . '.class.php';
	if (file_exists($file))
		include_once $file;
}