<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('gd.jpeg_ignore_warning', 1);

function debug($var)
{
    echo '<pre>' . print_r($var, true) . '</pre><br>';
}