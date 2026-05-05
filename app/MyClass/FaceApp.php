<?php

namespace App\MyClass;

class FaceApp
{
	const SERVER_URL = 'http://103.242.105.85:61788';
	const SERVER_USERNAME = 'admin';
	const SERVER_PASSWORD = 'admin';


	public static function compare($pathOne, $pathTwo)
	{
		$url = self::SERVER_URL;
		$username = self::SERVER_USERNAME;
		$password = self::SERVER_PASSWORD;

		try {
			if(!empty($url) && !empty($username) && !empty($password))
			{
				$b64 = function($path) {
					return "data:image/jpeg;base64,".base64_encode(\File::get($path));
				};

				$pictOne = $b64($pathOne);
				$pictTwo = $b64($pathTwo);

				$pushData = [
					"operator"	=> "GetPictureSimilarity",
					"picinfo1"	=> $pictOne,
					"picinfo2"	=> $pictTwo
				];

				$destination = $url;
				$destination .= "/action/GetPictureSimilarity";

				$response = \Http::withBasicAuth($username, $password)
							->contentType("text/plain")->send('POST', $destination, [
								'body' => str_replace("\/", "/", json_encode($pushData)),
							])->json();

				$similarity = $response['info']['Similarity'];

				return $similarity;
			}
		} catch (\Exception $e) {
			return 0;
		}
	}
}