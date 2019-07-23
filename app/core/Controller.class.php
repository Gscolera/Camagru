<?php

namespace app\core;

class Controller
{
	protected static $model;

	public static function render($content, $title = 'Camagru', $data = [])
	{
		View::render($content, $title, $data);
	}
}