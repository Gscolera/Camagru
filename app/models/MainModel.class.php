<?php

namespace app\models;

class MainModel extends \app\core\Model
{
	private static $stickers = [];
	private static $image;

	public static function uploadImage()
	{
		$width = $_POST['width'];
		$height = $_POST['height'];

		$image = imagecreatefromstring(file_get_contents($_FILES['file']['tmp_name']));
		$size = getimagesize($_FILES['file']['tmp_name']);
		$resized = imagecreatetruecolor($width, $height);
		imagecopyresampled($resized, $image, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
		imagejpeg($resized, TMP_FILE);
		header('Content: image/jpeg');
		imagejpeg($resized);
		imagedestroy($image);
		imagedestroy($resized);
	}

	public static function mergeImages()
	{
		if (isset($_POST['sticker']))
			self::decodeStickerInfo($_POST['sticker']);
		self::getUserImage();
		if (self::$stickers)
			self::applyStickers();
		if ($_POST['filter'] !== 'none')
			self::applyFilter($_POST['filter']);
		ob_start();
		imagejpeg(self::$image);
		$blob = ob_get_clean();
		imagedestroy(self::$image);
		self::saveImage($blob);
		$blob = 'data:image/jpeg;base64,' . base64_encode($blob);
		exit(json_encode(['status' => 'success', 'image' => $blob]));
	}

	private static function saveImage($blob)
	{
		date_default_timezone_set('Europe/Moscow');
		$date = date("Y-m-d H:i:s");
		$sql = 'INSERT INTO gallery (login, date, image) VALUES (:login, :date, :image)';
		$params = ['login' => $_SESSION['user'], 'date' => $date, 'image' => $blob];
		self::$db::execute($sql, $params);
	}

	private static function applyFilter($filter)
	{
		switch ($filter)
		{
			case 'invert(100%)':
				imagefilter(self::$image, IMG_FILTER_NEGATE);
				break;
			case 'grayscale(100%)':
				imagefilter(self::$image, IMG_FILTER_GRAYSCALE);
				break;
			case 'sepia(100%)':
				imagefilter(self::$image, IMG_FILTER_COLORIZE,100,50,0);
				break;
			case 'contrast(200%)':
				imagefilter(self::$image, IMG_FILTER_CONTRAST, -50);
				break;
		}
	}


	private static function decodeStickerInfo(array $stickers)
	{
		foreach ($stickers as $sticker) {
			self::$stickers[] = json_decode($sticker, true);
		}
	}

	private static function getUserImage()
	{
		if (isset($_POST['image']) and $_POST['image'] === 'uploaded')
		{
			if (!file_exists(TMP_FILE))
				die (json_encode(['error' => 'No image uploaded!']));
			self::$image = imagecreatefromstring(file_get_contents(TMP_FILE));
		}
		else
		{
			self::$image = imagecreatefromstring(file_get_contents($_FILES['image']['tmp_name']));
		}
	}

	private static function applyStickers()
	{
		foreach (self::$stickers as $sticker)
		{
			$file = WWW . '/img/stickers/' . $sticker['name'] . '.png';
			if (!file_exists($file))
				break;
			$stickerImg = imagecreatefromstring(file_get_contents($file));
			$stickerScaled = imagescale($stickerImg, $sticker['width'], $sticker['height']);
			imagesavealpha($stickerScaled, true);
			$dst_x = $sticker['x'] - $_POST['x'];
			$dst_y = $sticker['y'] - $_POST['y'];
			self::imagecopymerge_alpha(self::$image, $stickerScaled, $dst_x, $dst_y, 0, 0, $sticker['width'],
								$sticker['height'], 100);
			imagedestroy($stickerImg);
			imagedestroy($stickerScaled);
		}
	}

	private static function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct)
	{
		$cut = imagecreatetruecolor($src_w, $src_h);
		imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h);
		imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h);
		imagecopymerge($dst_im, $cut, $dst_x, $dst_y, 0, 0, $src_w, $src_h, $pct);
	}
}