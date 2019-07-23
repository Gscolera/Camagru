<?php

namespace app\core;

class Router
{
	private static $request;
	private static $controller;
	private static $action;
	private static $publicControllers = [];
	private static $privateControllers = [];

	public static function dispatch()
	{
		self::init();

		if (self::matchRoute(self::$request))
		{
			if (!isset($_SESSION['user']))
				self::checkAccessRights();
			elseif (isset($_SESSION['user']) and self::$controller === 'AuthController')
				self::$controller = 'MainController';
			$controller = '\app\controllers\\' . self::$controller;
			if (!class_exists($controller))
				self::return404();
			$controller = new $controller;
			$action = method_exists($controller, self::$action) ? self::$action : 'indexAction';
			if (!method_exists($controller, $action))
				self::return404();
			$controller->$action();
		}
		else
		{
			self::return404();
		}
	}

	public static function return404()
	{
		http_response_code(404);
		include (VIEWS . '/notFound.html');
		die;
	}

	private static function init()
	{
		self::$request = rtrim(explode('&', $_SERVER['QUERY_STRING'], 2)[0], '/');

		self::$publicControllers = ['AuthController', 'GalleryController'];
		self::$privateControllers = ['MainController'];

	}

	private static function matchRoute($request)
	{
		if (preg_match('#^$#', $request))
		{
			self::$controller = 'MainController';
			self::$action = 'indexAction';
			return true;
		}
		if (preg_match('#^(?P<controller>[a-z-]+)/?(?P<action>[a-z-]+)?$#i', $request, $matches))
		{
			self::$controller = self::getControllerName($matches['controller']) . 'Controller';
			self::$action = (isset($matches['action'])) ? self::getActionName($matches['action']) . 'Action' : 'indexAction';
			return true;
		}
		return false;
	}

	private static function getControllerName($controller)
	{
		$controller = str_replace('-', ' ', $controller) ;
		$controller = ucwords($controller);
		return str_replace(' ', '', $controller);

	}

	private static function getActionName($action)
	{
		return lcfirst(self::getControllerName($action));
	}

	private static function checkAccessRights()
	{
		foreach (self::$privateControllers as $pc)
		{
			if (self::$controller === $pc)
			{
				self::$controller = 'AuthController';
				self::$action = 'indexAction';
				break;
			}
		}
	}

}