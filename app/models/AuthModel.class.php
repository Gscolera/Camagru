<?php

namespace app\models;

class AuthModel extends \app\core\Model
{
	public static function createUserpic()
	{
		if (isset($_FILES['file']))
		{
			$image = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($_FILES['file']['tmp_name']));
			$_SESSION['userpic'] = $image;
			exit(json_encode(['image' => $image]));
		}
	}

	public static function signUp()
	{
		$userpic = isset($_SESSION['userpic']) ? $_SESSION['userpic'] : null;
		$login = self::validateLogin(trim($_POST['login']));
		$email = self::validateEmail(trim($_POST['email']));
		$password = self::validatePassword($_POST['password'], $_POST['passwordConfirm']);
		$token = hash('whirlpool', $login);
		$sql = 'INSERT INTO users (login, password, email, token, userpic) VALUES (:login, :password, :email, :token, :userpic)';
		$params = ['login' => $login, 'email' => $email, 'password' => $password, 'token' => $token, 'userpic' => $userpic];
		self::$db::execute($sql, $params);
		if (isset($_SESSION['userpic']))
			unset($_SESSION['userpic']);
		self::sendAutorizationMail($login, $email, $token);
		self::returnSuccess('Almost done, we have send you an activation link on your email. Follow it ti finish the registration!');
	}

	public static function signIn()
	{
		$login = trim($_POST['login']);
		$sql = 'SELECT authorized, password, email FROM users WHERE login = :login';
		$params = ['login' => $login];
		$response = self::$db::query($sql, $params);
		if (empty($response))
			self::returnError('Invalid login or password!', 0);
		if (!password_verify($_POST['password'], $response[0]['password']))
			self::returnError('Invalid login or password!', 0);
		if ($response[0]['authorized'] !== '1')
			self::returnError('You need to confirm your email first!', 3);
		$_SESSION['user'] = $login;
		$_SESSION['email'] = $response[0]['email'];
		self::returnSuccess();
	}

	public static function activateAccount()
	{
		if (!isset($_GET['login']) or !isset($_GET['token']))
			return(['error' => 'Invalid link!']);
		$login = $_GET['login'];
		$token = $_GET['token'];
		$sql = 'SELECT authorized, token FROM users WHERE login = :login';
		$params = ['login' => $login];
		$response = self::$db::query($sql, $params);
		if (empty($response))
			return(['error' => 'Invalid link!']);
		if ($response[0]['authorized'] === '1')
			return(['error' => 'Your account is already activated!']);
		if ($response[0]['token'] !== $token)
			return(['error' => 'Invalid link!']);
		$sql = 'UPDATE users SET authorized = 1, token = NULL WHERE login = :login';
		$params = ['login' => $login];
		self::$db::execute($sql, $params);
		return (['message' => 'Your account is activated! Now you may login!']);
	}

	public static function resetPassword()
	{
		$passwdReset = self::generatePasswd();
		$email = '';

		if (!empty($_POST['login'])) {
			$sql = 'SELECT email FROM users WHERE login = :login';
			$params = ['login' => $_POST['login']];
			$response = self::$db::query($sql, $params);
			if (isset($response[0]['email']))
				$email = $response[0]['email'];
			$login = $_POST['login'];
		}
		elseif (!empty($_POST['email'])) {
			$sql = 'SELECT login FROM users WHERE email = :email';
			$params = ['email' => $_POST['email']];
			$response = self::$db::query($sql, $params);
			if (isset($response[0]['login']))
				$email = $_POST['email'];
			$login = $response[0]['login'];
		}
		else
			self::returnError('You should fill at least one field!', 0);
		if ($email)
		{
			$sql = 'UPDATE users SET password = :password WHERE login = :login';
			$params = ['login' => $login, 'password' => password_hash($passwdReset, PASSWORD_DEFAULT)];
			self::$db::execute($sql, $params);
			self::sendNewPassword($email, $passwdReset, $login);
		}
		self::returnSuccess('We have send you a new password on your email!');
	}

	public static function validateLogin($login)
	{
		if (!preg_match('#^([a-z0-9]{3,30})$#i', $login))
			self::returnError('Login must contain from 3 to 30 characters! Only latin characters and nubmers are allowed!', 0);
		$sql = 'SELECT * FROM users WHERE login = :login';
		$params = ['login' => $login];
		if (self::$db::query($sql, $params))
			self::returnError('Login ' . $login . ' is already in use!', 0);
		return $login;
	}

	public static function validateEmail($email)
	{
		$email = filter_var($email, FILTER_VALIDATE_EMAIL);
		if (!$email)
			self::returnError('Invalid email format!', 1);
		$sql = 'SELECT * FROM users WHERE email = :email';
		$params = ['email' => $email];
		if (self::$db::query($sql, $params))
			self::returnError('Email ' . $email . ' is already in use!', 1);
		return $email;
	}

	public static function validatePassword($password, $passwordConfirm)
	{
		if (!preg_match('#^([a-z0-9]{6,30})$#i', $password))
			self::returnError('Password must contain from 6 to 30 characters! Only latin characters and numbers are allowed!', 2);
		if (!preg_match('#[a-z]+#', $password))
			self::returnError('Password must contain at least one letter!', 2);
		if (!preg_match('#[0-9]+#', $password))
			self::returnError('Password must contain at least one number!', 2);
		if ($password !== $passwordConfirm)
			self::returnError('Password do not match!', 3);
		return password_hash($password, PASSWORD_DEFAULT);
	}

	private static function sendAutorizationMail($login, $email, $token)
	{
		$to = $email;
		$subject = 'Camagru registration';
		$message =
			"<html>
					<head>
						  <title>Activation</title>
					</head>
					<body>
						Welcome $login!<br><br>
						Thank you for joining Camaguru.<br>
						To activate your account, please click the link below.<br>
						<a href=http://localhost/auth/activate?login=$login&token=$token><b>Activate</b></a>
				</body>
			</html>";
		$headers = 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= "From: <no-reply@camagru>\r\n";
		mail($to, $subject, $message, $headers);
	}

	private static function generatePasswd()
	{
		$chars = 'qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP';
		$size = strlen($chars) - 1;
		$passwd = '';
		for ($i = 10; $i > 0; $i--)
			$passwd .= $chars[rand(0, $size)];
		return $passwd;
	}

	private static function sendNewPassword($email, $password, $login)
	{
		$to = $email;
		$subject = 'Camagru Password Reset';
		$message =
			"<html>
					<head>
						  <title>Email Reset</title>
					</head>
					<body align='center'>
						Welcome $login!<br><br>
						Here is your new password! You can change it anytime in your personal account!<br><br>
						<b>$password</b>
				</body>
			</html>";
		$headers = 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= "From: <no-reply@camagru>\r\n";
		mail($to, $subject, $message, $headers);
	}

	private static function returnError($message, $field)
	{
		die(json_encode(['status' => 'error', 'error' => $message, 'field' => $field]));
	}

	private static function returnSuccess($message = '')
	{
		exit(json_encode(['status' => 'success', 'message' => $message]));
	}
}