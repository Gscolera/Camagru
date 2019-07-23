<?php

namespace app\core;

class Model
{
	protected static $db;

	public function __construct()
	{
		self::$db = Db::instance();
	}
}