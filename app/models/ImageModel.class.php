<?php

namespace app\models;

class ImageModel extends \app\core\Model
{
	protected static $images = [];

	public static function getSticker()
	{
		$file = WWW . '/img/stickers/' . $_GET['sticker'] . '.png';
		if (file_exists($file))
		{
			$image = imagecreatefrompng($file);
			imagesavealpha($image, true);
			header('Content: image/png');
			imagepng($image);
			imagedestroy($image);
		}
	}

	public static function fillGallery()
	{
		$row = intval($_GET['last']);
		$limit = intval($_GET['limit']);
		$sql = "SELECT pid, image FROM gallery ORDER BY pid DESC LIMIT $row, $limit";
		$response = self::$db::query($sql);
		foreach ($response as $row)
			self::$images[$row['pid']] = 'data:image/jpeg;base64,' . base64_encode($row['image']);
		exit(json_encode(self::$images));
	}

	public static function getImageInfo()
	{
		$sql = 'SELECT login, date FROM gallery WHERE pid = :pid';
		$params = ['pid' => $_GET['pid']];
		$response = self::$db::query($sql, $params);

		$image['owner'] = $response[0]['login'];
		$image['date'] = $response[0]['date'];
		if (isset($_SESSION['user']))
			$image['logged'] = $image['owner'] === $_SESSION['user'] ? true : false;
		else
			$image['logged'] = false;
		$sql = "SELECT COUNT(id) FROM likes WHERE pid = :pid";
		$params = ['pid' => $_GET['pid']];
		$response = self::$db::query($sql, $params);

		$image['likesCount'] = $response[0]['COUNT(id)'];
		$image['authorizedUser'] = isset($_SESSION['user']) ? true : false;

		$sql = 'SELECT * FROM comments WHERE pid = :pid';
		$params = ['pid' => $_GET['pid']];
		$response = self::$db::query($sql, $params);
		foreach ($response as $comment)
		{
			$header = 'Commented by ' . $comment['login'] . ' on ' . $comment['date'];
			$image['comment'][$header] = $comment['comment'];
		}
		exit(json_encode($image));
	}

	public static function toggleLike()
	{
		if (!isset($_SESSION['user']))
			exit(json_encode(['like' => 'notLogged']));
		$login = $_SESSION['user'];
		$pid = $_GET['pid'];

		$sql = "SELECT id FROM likes WHERE login = :login AND pid = :pid";
		$params = ['login' => $login, 'pid' => $pid];
		$res = self::$db::query($sql, $params);
		if (!$res)
		{
			$sql = 'INSERT INTO likes (pid, login) VALUES (:pid, :login)';
			$params = ['pid' => $pid, 'login' => $login];
			$response = 'set';
		}
		else
		{
			$sql = 'DELETE FROM likes WHERE login = :login AND pid = :pid';
			$params = ['login' => $login, 'pid' => $pid];
			$response = 'unset';
		}
		self::$db::execute($sql, $params);
		exit(json_encode(['like' => $response]));
	}

	public static function addComment()
	{
		if (!isset($_SESSION['user']))
			exit(json_encode(['error' => 'not logged in']));
		$comment = htmlentities(trim($_POST['comment']));
		$pid = $_POST['pid'];
		$login = $_SESSION['user'];
		$date = date("Y-m-d H:i:s");
		$sql = 'INSERT INTO comments (pid, login, date, comment) VALUES (:pid, :login, :date, :comment)';
		$params = ['pid' => $pid, 'login' => $login, 'date' => $date, 'comment' => $comment];
		self::$db::execute($sql, $params);
		self::sendNotification($pid, $login);
		$header = 'Commented by ' . $login . ' on ' . $date;
		exit(json_encode(['header' => $header, 'comment' => $comment]));

	}

	public static function deleteImage()
	{
		$pid = $_GET['pid'];
		$sql = 'SELECT login FROM gallery WHERE pid = :pid';
		$params = ['pid' => $pid];
		$res = self::$db::query($sql, $params);
		if ($res[0]['login'] === $_SESSION['user'])
		{
			$sql = 'DELETE FROM gallery WHERE pid = :pid';
			self::$db::execute($sql, $params);
			$sql = 'DELETE FROM likes WHERE pid = :pid';
			self::$db::execute($sql, $params);
			$sql = 'DELETE FROM comments WHERE pid = :pid';
			self::$db::execute($sql, $params);
		}

	}

	private static function sendNotification($pid, $login)
	{
		$sql = 'SELECT login FROM gallery WHERE pid = :pid';
		$params = ['pid' => $pid];
		$response = self::$db::query($sql, $params);
		$picOwner = $response[0]['login'];
		$sql = 'SELECT email, notification FROM users WHERE login = :login';
		$params = ['login' => $picOwner];
		$response = self::$db::query($sql, $params);
		if ($response[0]['notification'] == 1)
		{
			$to = $response[0]['email'];
			$subject = 'Camagru notification';
			$message =
				"<html>
					<head>
						  <title>Notification</title>
					</head>
					<body>
						Hello $picOwner!<br><br>
						You have a new comment below your photo from $login.<br>
						Check it on Camagru.<br>
				</body>
			</html>";
			$headers = 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$headers .= "From: <no-reply@camagru>\r\n";
			mail($to, $subject, $message, $headers);
		}
	}
}