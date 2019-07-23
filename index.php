<?php

include 'app/debug.php';
include 'app/init.php';

session_start();

\app\core\Router::dispatch();


