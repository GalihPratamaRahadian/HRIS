<?php 

namespace App\MyClass;

use Illuminate\Support\Facades\File;

class Photo
{

	public static function createFromBase64($data, $path)
	{
		$blob = Photo::getBlobFromBase64($data);
		Photo::save($blob, $path);
	}

	public static function createFromBlob($data, $path)
	{
		//
	}

	private static function getBlobFromBase64($data)
	{
		$explode 	= Photo::explodeBase64($data);
		$cek 		= count($explode);
		$blob 		= null;

		if ($cek == 1) {
			$blob = $explode[0];
		} else {
			$blob = $explode[1];
		}

		$blob = base64_decode($blob);

		return $blob;
	}

	private static function explodeBase64($data)
	{
		$explode = explode(',', $data);
		return $explode;
	}

	private static function save($data, $path)
	{
        File::put($path, $data);
        return true;
	}

	private static function fileName($fileName)
	{
		$explodeName 	= explode('.', $fileName);
		$count 			= count($explodeName);

		if ($count > 1) {
			return $fileName;
		} else {
			return $fileName.".jpg";
		}
	} 

	public static function createJPEGBase64($base64)
	{
		$output = "";
		$photo = base64_decode($base64);

        $img = imagecreatefromstring($photo);
        if($img !== false)
        {
            header('Content-Type: image/jpeg');
            $output = imagejpeg($img);
            imagedestroy($img);
        }

        return 'hai';
	}
}