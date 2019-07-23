<?php

namespace app\core;

class Db
{
	protected static $pdo;
	protected static $instance;

	private function __construct()
	{
		require_once ROOT . '/config/database.php';
		try
		{
			self::$pdo = new \PDO(DB_DSN, DB_USER, DB_PASSWD, DB_OPTIONS);
		}
		catch (\PDOException $e)
		{
			die( json_encode(['error' => 'Database error: ' . $e->getMessage()]));
		}
	}

	public static function instance()
	{
		if (self::$instance === null)
			self::$instance = new self;
		return self::$instance;
	}

	public static function query($sql, $params = [])
	{
		try
		{
			$stmt = self::$pdo->prepare($sql);
			$stmt->execute($params);
			return $stmt->fetchAll();
		}
		catch (\PDOException $e)
		{
			die( json_encode(['error' => 'Database error: ' . $e->getMessage()]));
		}
	}

	public static function execute($sql, $params)
	{
		try
		{
			$stmt = self::$pdo->prepare($sql);
			return $stmt->execute($params);
		}
		catch (\PDOException $e)
		{
			die( json_encode(['error' => 'Database error: ' . $e->getMessage()]));
		}
	}
}