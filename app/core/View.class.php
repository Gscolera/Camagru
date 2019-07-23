<?php

namespace app\core;

class View
{
	public static function render($content, $title, $data)
	{
		$content = VIEWS . '/' . $content;
		include_once LAYOUT;
	}

}