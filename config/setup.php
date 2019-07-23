<?php

require_once 'database.php';

try {
	$pdo = new \PDO(DB_DSN_SETUP, DB_USER, DB_PASSWD, DB_OPTIONS);
	$pdo->exec('CREATE DATABASE IF NOT EXISTS ' . DB_NAME);
	$pdo->exec('USE ' . DB_NAME);
	$pdo->exec('CREATE TABLE IF NOT EXISTS users(
	`uid` INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
	`login` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`email` VARCHAR(60) NOT NULL,
	`password` VARCHAR(255) NOT NULL,
	`token` VARCHAR(255),
	`userpic` LONGBLOB,
	`authorized` BOOLEAN DEFAULT 0 NOT NULL,
	`notification` BOOLEAN DEFAULT 1 NOT NULL)');
	$pdo->exec('CREATE TABLE IF NOT EXISTS `gallery` (
  	`pid` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  	`login` VARCHAR(30) NOT NULL,
  	`date` DATETIME NOT NULL,
  	`image` LONGBLOB NOT NULL)');
	$pdo->exec('CREATE TABLE IF NOT EXISTS `comments` (
  	`id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  	`pid` INT UNSIGNED NOT NULL,
  	`login` VARCHAR(30) NOT NULL,
  	`date` DATETIME NOT NULL,
  	`comment` TEXT NOT NULL)');
	$pdo->exec('CREATE TABLE IF NOT EXISTS `likes` (
  	`id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  	`pid` INT UNSIGNED NOT NULL,
  	`login` VARCHAR(30) NOT NULL)');
} catch (PDOException $e) {
	die ('Database error: ' . $e->getMessage());
}
