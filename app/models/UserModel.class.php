<?php

namespace app\models;

class UserModel extends \app\core\Model
{
	public static function checkPreferences()
	{
		if (!isset($_SESSION['user']))
			exit(json_encode(['error' => 'not logged in']));
		$sql = 'SELECT notification, userpic FROM users WHERE  login = :login';
		$params = ['login' => $_SESSION['user']];
		$response = self::$db::query($sql, $params);
		$res = $response[0];
		$userpic = $response[0]['userpic'];
		if (!$userpic)
			$userpic = 'https://cdn.intra.42.fr/users/medium_default.png';
		exit (json_encode(['notification' => $res['notification'], 'userpic' => $userpic, 'login' => $_SESSION['user']]));
	}

	public static function changePreferences()
	{
		if (isset($_GET['notification']))
			self::changeNotificationStatus($_GET['notification'] === 'true' ? 1 : 0);
	}

	public static function changePersonalInfo()
	{
		$login = trim($_POST['login']);
		$email = trim($_POST['email']);
		$newPassword = $_POST['newPassword'];
		$newPasswordConfirm = $_POST['newPasswordConfirm'];
		$password = $_POST['password'];

		if (!$login and !$email and !$newPassword)
			self::returnError('You must choose at least one option to change!', 8);
		if ($login)
			self::changeLogin($login, $password);
		if ($email)
			self::changeEmail($email, $password);
		if ($newPassword)
			self::changePassword($newPassword, $newPasswordConfirm, $password);
		self::returnSuccess();
	}

	private static function changeLogin($login, $password)
	{
		$login = AuthModel::validateLogin($login);
		$sql = 'SELECT uid, password FROM users WHERE login = :login';
		$params = ['login' => $_SESSION['user']];
		$response = self::$db::query($sql, $params);
		if (!password_verify($password, $response[0]['password']))
			self::returnError('Invalid password!', 4);
		$sql = 'UPDATE users SET login = :login WHERE uid = :uid';
		$params = ['login' => $login, 'uid' => $response[0]['uid']];
		self::$db::execute($sql, $params);
		$_SESSION['user'] = $login;
	}

	private static function changeEmail($email, $password)
	{
		$email = AuthModel::validateEmail($email);
		$sql = 'SELECT uid, password FROM users WHERE email = :email';
		$params = ['email' => $_SESSION['email']];
		$response = self::$db::query($sql, $params);
		if (!password_verify($password, $response[0]['password']))
			self::returnError('Invalid password!', 4);
		$sql = 'UPDATE users SET email = :email WHERE uid = :uid';
		$params = ['email' => $email, 'uid' => $response[0]['uid']];
		self::$db::execute($sql, $params);
		$_SESSION['email'] = $email;
	}

	private static function changePassword($passwordNew, $passwordNewConfirm, $password)
	{
		$passwordNew = AuthModel::validatePassword($passwordNew, $passwordNewConfirm);
		$sql = 'SELECT uid, password FROM users WHERE login = :login';
		$params = ['login' => $_SESSION['user']];
		$response = self::$db::query($sql, $params);
		if (!password_verify($password, $response[0]['password']))
			self::returnError('Invalid password!', 4);
		$sql = 'UPDATE users SET password = :password WHERE uid = :uid';
		$params = ['password' => $passwordNew, 'uid' => $response[0]['uid']];
		self::$db::execute($sql, $params);
	}

	private static function changeNotificationStatus($status)
	{
		$sql = 'UPDATE users SET notification = :notification WHERE login = :login';
		$params = ['notification' => $status, 'login' => $_SESSION['user']];
		self::$db::execute($sql, $params);
	}

	private static function returnError($message, $field)
	{
		die(json_encode(['status' => 'error', 'error' => $message, 'field' => $field]));
	}

	private static function returnSuccess($message = '')
	{
		exit(json_encode(['status' => 'success', 'message' => $message, 'login' => $_SESSION['user']]));
	}
}