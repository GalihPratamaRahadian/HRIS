<?php

namespace App\MyClass;

class Template
{
	public static function required()
	{
		return "<span class='text-danger'> * </span>";
	}

	public static function requiredBanner()
	{
		$text = "Kolom bertanda ".self::required()." wajib diisi.";
		return self::alertBanner($text);
	}

	public static function titleBanner($title)
	{
		return "<div class='title-banner mb-3'>
					".$title."
				</div>";
	}

	public static function alertBanner($text = '')
	{
		return "<div class='alert alert-info'>
					". $text ."
				</div>";
	}

	public static function dangerBanner($text = '')
	{
		return "<div class='alert alert-danger border-0'>
					<i class='mdi mdi-alert mr-1'></i> ". $text ."
				</div>";
	}

	public static function infoBanner($text = '')
	{
		$text = "<i class='mdi mdi-information mr-1'></i>". $text;
		return self::alertBanner($text);
	}
}