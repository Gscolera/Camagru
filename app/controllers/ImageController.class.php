<?php

namespace app\controllers;

class ImageController extends \app\core\Controller
{
	public function __construct()
	{
		self::$model = new \app\models\ImageModel;
	}

	public function getStickerAction()
	{
		self::$model::getSticker();
	}

	public static function indexAction()
	{
		self::render('gallery.php', 'Gallery');
	}
	public static function fillGalleryAction()
	{
		self::$model::fillGallery();
	}

	public static function getInfoAction()
	{
		self::$model::getImageInfo();
	}

	public function likeAction()
	{
		self::$model::toggleLike();
	}

	public function commentAction()
	{
		self::$model::addComment();
	}

	public function deleteAction()
	{
		self::$model::deleteImage();
	}
}