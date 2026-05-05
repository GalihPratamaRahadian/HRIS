<?php 

namespace App\MyClass;

use Intervention\Image\Facades\Image;
use File;

class Resizer
{

	public static function createFaceSize($path, $filename, $result)
	{
		$img = Image::make($path);
		$img->resize(352, null, function($cons){
			$cons->aspectRatio();
		});

		if($img->height() < 432)
		{
			$img->resize(null, 432, function($cons){
				$cons->aspectRatio();
			});
		}

		$marginLeft = 0;
		if($img->width() > 352)
		{
			$marginLeft = ($img->width() - 352)/2;
		}

		$marginTop = 0;
		if($img->height() > 432)
		{
			$marginTop = ($img->height() - 432)/2;
		}

		$img->crop(352, 432, (int) $marginLeft, (int) $marginTop);
		$img->save($result);
	}


	public static function createThumbSize($path, $filename, $result)
	{
		$img = Image::make($path);
        $img->resize(null, 50, function($cons){
            $cons->aspectRatio();
        });
        $img->save($result);
	}
}