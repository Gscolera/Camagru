<?php

namespace app\controllers;

class AuthController extends \app\core\Controller
{
	public function __construct()
	{
		self::$model = new \app\models\AuthModel;
	}

	public function indexAction()
	{
		$this->loginAction();
	}

	public function loginAction()
	{
		self::render('login.php', 'Login Camagru');
	}

	public function createUserpicAction()
	{
		self::$model::createUserpic();
	}

	public function signUpAction()
	{
		self::$model::signUp();
	}

	public function activateAction()
	{
		$data = self::$model::activateAccount();
		self::render('login.php', 'Login Camagru', $data);
	}

	public function signInAction()
	{
		self::$model::signIn();
	}

	public function forgotAction()
	{
		self::$model::resetPassword();
	}

}