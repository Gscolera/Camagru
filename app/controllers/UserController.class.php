<?php

namespace app\controllers;

class UserController extends \app\core\Controller
{
	public function __construct()
	{
		self::$model = new \app\models\UserModel;
	}

	public function logoutAction()
	{
		unset($_SESSION['user']);
		unset($_SESSION['email']);
		header('Location: /');
	}

	public function checkPreferencesAction()
	{
		self::$model::checkPreferences();
	}

	public function changePreferencesAction()
	{
		self::$model::changePreferences();
	}

	public function changePersonalInfoAction()
	{
		self::$model::changePersonalInfo();
	}

}