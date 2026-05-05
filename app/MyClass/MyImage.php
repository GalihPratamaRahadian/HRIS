<?php 

namespace App\MyClass;

use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;

class MyImage
{
	public static function setWidth($data, $ratio = true)
	{
		if(MyImage::validateRequest($data, ['path', 'width']))
		{
			if(File::exists($data['path']))
			{
				$img = Image::make($data['path']);
				$img->resize(null, $data['width'], function($cons){
					$cons->aspectRatio();
				});
				MyImage::save($img, $data);
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}

	public static function setHeight($data, $ratio = true)
	{
		if(MyImage::validateRequest($data, ['path', 'height']))
		{
			if(File::exists($data['path']))
			{
				$img = Image::make($data['path']);
				$img->resize($data['height'], null, function($cons){
					$cons->aspectRatio();
				});
				MyImage::save($img, $data);
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}


	public static function setSize($data)
	{
		if(MyImage::validateRequest($data, ['path', 'width', 'height']))
		{
			if(File::exists($data['path']))
			{
				$img = Image::make($data['path']);
				$img->resize($data['width'], $data['width']);
				MyImage::save($img, $data);
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}

	private static function save($image, $data)
	{
		if(MyImage::validateRequest($data, ['result']))
		{
			$image->save($data['result']);
		}
		else
		{
			$image->save();
		}
	}

	private static function validateRequest($request, $mustInput)
	{
		$notExists = 0;
		foreach($mustInput as $must)
		{
			if(!array_key_exists($must, $request))
			{
				$notExists += 1;
			}
		}
		if($notExists > 0)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
}