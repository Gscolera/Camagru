<?php

namespace app\controllers;

class MainController extends \app\core\Controller
{

	public function __construct()
	{
		self::$model = new \app\models\MainModel;
	}

	public function indexAction()
	{
		self::render('main.php', 'Camagru');
	}

	public function uploadAction()
	{
		self::$model::uploadImage();
	}

	public function mergeAction()
	{
		self::$model::mergeImages();
	}
}