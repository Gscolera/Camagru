<?php

const DRIVER = 'mysql';
const HOST = 'localhost';
const DB_NAME = 'camagruDB';
const DB_USER = 'gscolera';
const DB_PASSWD = '';
const DB_DSN_SETUP = DRIVER . ':host=' . HOST . ';charset=utf8';
const DB_DSN = DRIVER . ':host=' . HOST . ';dbname=' . DB_NAME . ';charset=utf8';
const DB_OPTIONS = [ PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
										PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC ];