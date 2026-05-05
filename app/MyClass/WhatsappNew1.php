<?php

/**
 *	4 April 2025 17.50
 *	Manfaat class ini
 *	-> Mengirim pesan chat whatsapp
 * 	-> Mengirim pesan gambar/media ke whatsapp
 * 
 *	@static json sendChat(array $data) untuk mengirim chat
 *	@static json sendMedia(array $data) untuk mengirim gambar/media
 * */


namespace App\MyClass;

use Illuminate\Support\Facades\File;

class Whatsapp
{

	const STATUS_PENDING	= 1;
	const STATUS_SENT		= 2;
	const STATUS_RECEIVED	= 3;
	const STATUS_READ 		= 4;
	const STATUS_CANCELED	= 5;

	// API Type
	const API_TYPE_V1 = 'API_TYPE_V1'; // type lama buatan mas vaizal
	const API_TYPE_V2 = 'API_TYPE_V2'; // type baru buatan pak bambang

	
	/**
	 * 	Edit method dibawah untuk menyesuaikan dengan aplikasi kamu
	 * */

	/**
	* Untuk mendapat default Api Server Whatsapp
	* @return String
	*/
	private static function defaultApiServer()
	{
		return 'http://103.242.105.85:60002';
		// return 'http://10.1.105.164:3000';
		// return setting('whatsapp_url_server', 'http://103.242.105.85:50000');
	}


	/**
	* Untuk mendapat default Api Type Whatsapp
	* @return String
	*/
	private static function defaultApiType()
	{
		return self::API_TYPE_V2;
	}


	/**
	* Untuk mendapat valid Api Type
	* @return String
	*/
	public static function getValidApiType($data)
	{
		if(array_key_exists('api_type', $data)) {
			if(in_array($data['api_type'], [ self::API_TYPE_V1, self::API_TYPE_V2 ])) {
				return $data['api_type'];
			}
		}
		return self::defaultApiType();
	}


	/**
	* Untuk mendapat valid Api Server
	* @return String
	*/
	public static function getValidApiServer($data)
	{
		$apiServer = self::defaultApiServer();
		if(array_key_exists('api_server', $data)) {
			$apiServer = $data['api_server'];
		}
		if(substr($apiServer, -1, 1) == "/") {
			$apiServer = substr($apiServer, 0, strlen($apiServer) - 1);
		}
		return $apiServer;
	}

	/**
	 * 	Untuk reformating nomor telepon
	 * 	@param string|int $phoneNumber
	 * 	@return string|int
	 * */
	private static function reformatPhoneNumber($phoneNumber)
	{
		// try {
		// 	$phoneNumber = \App\MyClass\Helper::idPhoneNumberFormat($phoneNumber);
		// } catch (\Exception $e) {}
		return $phoneNumber;
	}

	/**
	 * 	Cek ketersediaan file
	 * 	@param string $path
	 * 	@return bool
	 * */
	private static function isFileOrDirectoryExists($path)
	{
		return \File::exists($path);
	}

	/**
	 * 	Directory Media
	 * 	@return string
	 * */
	private static function mediaDirectory($filename = '')
	{
		return storage_path('app/public/whatsapp_media/'.$filename);
	}

	/**
	 * 	Directory Media
	 * 	@return string
	 * */
	private static function mediaLink($filename = '')
	{
		return url('storage/whatsapp_media/'.$filename);
	}

	/**
	 * 	Membuat direktori
	 * 	@return bool
	 * */
	private static function createDirectory($path)
	{
		return \File::makeDirectory($path);
	}

	/**
	 * 	Membuat file
	 * 	@return bool
	 * */
	private static function createFile($path, $content)
	{
		return \File::put($path, $content);
	}

	/**
	 * 	Mime to Ext
	 * 	@param string $mime
	 * 	@return string
	 * */
	private static function mimeToExtension($mimeType)
	{
		$mimeMap = [
			'video/3gpp2'															=> '3g2',
			'video/3gp'																=> '3gp',
			'video/3gpp'															=> '3gp',
			'application/x-compressed'												=> '7zip',
			'audio/x-acc'															=> 'aac',
			'audio/ac3'																=> 'ac3',
			'application/postscript'												=> 'ai',
			'audio/x-aiff'															=> 'aif',
			'audio/aiff'															=> 'aif',
			'audio/x-au'															=> 'au',
			'video/x-msvideo'														=> 'avi',
			'video/msvideo'															=> 'avi',
			'video/avi'																=> 'avi',
			'application/x-troff-msvideo'											=> 'avi',
			'application/macbinary'													=> 'bin',
			'application/mac-binary'												=> 'bin',
			'application/x-binary'													=> 'bin',
			'application/x-macbinary'												=> 'bin',
			'image/bmp'																=> 'bmp',
			'image/x-bmp'															   => 'bmp',
			'image/x-bitmap'															=> 'bmp',
			'image/x-xbitmap'														   => 'bmp',
			'image/x-win-bitmap'														=> 'bmp',
			'image/x-windows-bmp'													   => 'bmp',
			'image/ms-bmp'															  => 'bmp',
			'image/x-ms-bmp'															=> 'bmp',
			'application/bmp'														   => 'bmp',
			'application/x-bmp'														 => 'bmp',
			'application/x-win-bitmap'												  => 'bmp',
			'application/cdr'														   => 'cdr',
			'application/coreldraw'													 => 'cdr',
			'application/x-cdr'														 => 'cdr',
			'application/x-coreldraw'												   => 'cdr',
			'image/cdr'																 => 'cdr',
			'image/x-cdr'															   => 'cdr',
			'zz-application/zz-winassoc-cdr'											=> 'cdr',
			'application/mac-compactpro'												=> 'cpt',
			'application/pkix-crl'													  => 'crl',
			'application/pkcs-crl'													  => 'crl',
			'application/x-x509-ca-cert'												=> 'crt',
			'application/pkix-cert'													 => 'crt',
			'text/css'																  => 'css',
			'text/x-comma-separated-values'											 => 'csv',
			'text/comma-separated-values'											   => 'csv',
			'application/vnd.msexcel'												   => 'csv',
			'application/x-director'													=> 'dcr',
			'application/vnd.openxmlformats-officedocument.wordprocessingml.document'   => 'docx',
			'application/x-dvi'														 => 'dvi',
			'message/rfc822'															=> 'eml',
			'application/x-msdownload'												  => 'exe',
			'video/x-f4v'															   => 'f4v',
			'audio/x-flac'															  => 'flac',
			'video/x-flv'															   => 'flv',
			'image/gif'																 => 'gif',
			'application/gpg-keys'													  => 'gpg',
			'application/x-gtar'														=> 'gtar',
			'application/x-gzip'														=> 'gzip',
			'application/mac-binhex40'												  => 'hqx',
			'application/mac-binhex'													=> 'hqx',
			'application/x-binhex40'													=> 'hqx',
			'application/x-mac-binhex40'												=> 'hqx',
			'text/html'																 => 'html',
			'image/x-icon'															  => 'ico',
			'image/x-ico'															   => 'ico',
			'image/vnd.microsoft.icon'												  => 'ico',
			'text/calendar'															 => 'ics',
			'application/java-archive'												  => 'jar',
			'application/x-java-application'											=> 'jar',
			'application/x-jar'														 => 'jar',
			'image/jp2'																 => 'jp2',
			'video/mj2'																 => 'jp2',
			'image/jpx'																 => 'jp2',
			'image/jpm'																 => 'jp2',
			'image/jpeg'																=> 'jpeg',
			'image/pjpeg'															   => 'jpeg',
			'application/x-javascript'												  => 'js',
			'application/json'														  => 'json',
			'text/json'																 => 'json',
			'application/vnd.google-earth.kml+xml'									  => 'kml',
			'application/vnd.google-earth.kmz'										  => 'kmz',
			'text/x-log'																=> 'log',
			'audio/x-m4a'															   => 'm4a',
			'audio/mp4'																 => 'm4a',
			'application/vnd.mpegurl'												   => 'm4u',
			'audio/midi'																=> 'mid',
			'application/vnd.mif'													   => 'mif',
			'video/quicktime'														   => 'mov',
			'video/x-sgi-movie'														 => 'movie',
			'audio/mpeg'																=> 'mp3',
			'audio/mpg'																 => 'mp3',
			'audio/mpeg3'															   => 'mp3',
			'audio/mp3'																 => 'mp3',
			'video/mp4'																 => 'mp4',
			'video/mpeg'																=> 'mpeg',
			'application/oda'														   => 'oda',
			'audio/ogg'																 => 'ogg',
			'video/ogg'																 => 'ogg',
			'application/ogg'														   => 'ogg',
			'font/otf'																  => 'otf',
			'application/x-pkcs10'													  => 'p10',
			'application/pkcs10'														=> 'p10',
			'application/x-pkcs12'													  => 'p12',
			'application/x-pkcs7-signature'											 => 'p7a',
			'application/pkcs7-mime'													=> 'p7c',
			'application/x-pkcs7-mime'												  => 'p7c',
			'application/x-pkcs7-certreqresp'										   => 'p7r',
			'application/pkcs7-signature'											   => 'p7s',
			'application/pdf'														   => 'pdf',
			'application/octet-stream'												  => 'pdf',
			'application/x-x509-user-cert'											  => 'pem',
			'application/x-pem-file'													=> 'pem',
			'application/pgp'														   => 'pgp',
			'application/x-httpd-php'												   => 'php',
			'application/php'														   => 'php',
			'application/x-php'														 => 'php',
			'text/php'																  => 'php',
			'text/x-php'																=> 'php',
			'application/x-httpd-php-source'											=> 'php',
			'image/png'																 => 'png',
			'image/x-png'															   => 'png',
			'application/powerpoint'													=> 'ppt',
			'application/vnd.ms-powerpoint'											 => 'ppt',
			'application/vnd.ms-office'												 => 'ppt',
			'application/msword'														=> 'doc',
			'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
			'application/x-photoshop'												   => 'psd',
			'image/vnd.adobe.photoshop'												 => 'psd',
			'audio/x-realaudio'														 => 'ra',
			'audio/x-pn-realaudio'													  => 'ram',
			'application/x-rar'														 => 'rar',
			'application/rar'														   => 'rar',
			'application/x-rar-compressed'											  => 'rar',
			'audio/x-pn-realaudio-plugin'											   => 'rpm',
			'application/x-pkcs7'													   => 'rsa',
			'text/rtf'																  => 'rtf',
			'text/richtext'															 => 'rtx',
			'video/vnd.rn-realvideo'													=> 'rv',
			'application/x-stuffit'													 => 'sit',
			'application/smil'														  => 'smil',
			'text/srt'																  => 'srt',
			'image/svg+xml'															 => 'svg',
			'application/x-shockwave-flash'											 => 'swf',
			'application/x-tar'														 => 'tar',
			'application/x-gzip-compressed'											 => 'tgz',
			'image/tiff'																=> 'tiff',
			'font/ttf'																  => 'ttf',
			'text/plain'																=> 'txt',
			'text/x-vcard'															  => 'vcf',
			'application/videolan'													  => 'vlc',
			'text/vtt'																  => 'vtt',
			'audio/x-wav'															   => 'wav',
			'audio/wave'																=> 'wav',
			'audio/wav'																 => 'wav',
			'application/wbxml'														 => 'wbxml',
			'video/webm'																=> 'webm',
			'image/webp'																=> 'webp',
			'audio/x-ms-wma'															=> 'wma',
			'application/wmlc'														  => 'wmlc',
			'video/x-ms-wmv'															=> 'wmv',
			'video/x-ms-asf'															=> 'wmv',
			'font/woff'																 => 'woff',
			'font/woff2'																=> 'woff2',
			'application/xhtml+xml'													 => 'xhtml',
			'application/excel'														 => 'xl',
			'application/msexcel'													   => 'xls',
			'application/x-msexcel'													 => 'xls',
			'application/x-ms-excel'													=> 'xls',
			'application/x-excel'													   => 'xls',
			'application/x-dos_ms_excel'												=> 'xls',
			'application/xls'														   => 'xls',
			'application/x-xls'														 => 'xls',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'		 => 'xlsx',
			'application/vnd.ms-excel'												  => 'xlsx',
			'application/xml'														   => 'xml',
			'text/xml'																  => 'xml',
			'text/xsl'																  => 'xsl',
			'application/xspf+xml'													  => 'xspf',
			'application/x-compress'													=> 'z',
			'application/x-zip'														 => 'zip',
			'application/zip'														   => 'zip',
			'application/x-zip-compressed'											  => 'zip',
			'application/s-compressed'												  => 'zip',
			'multipart/x-zip'														   => 'zip',
			'text/x-scriptzsh'														  => 'zsh',
		];

		if(array_key_exists($mimeType, $mimeMap)) return $mimeMap[$mimeType];
		return false;
	}


	/**
	 * 	Put temps
	 * */
	private static function putTemps($filename, $content)
	{
		try {
			self::createFile(\Setting::temps($filename), json_encode($content));
		} catch (\Exception $e) {}
	}

	/**
	 * 	End
	 * */


	/**
	 * 	Parse data
	 * 	@param array|string $data
	 * 	@param string $type. hanya mendukung 'text' atau 'media'
	 * 	@return array
	 * */
	private static function parseData($data, $type = 'text')
	{
		$apiType = self::getValidApiType($data);

		if($apiType == self::API_TYPE_V1) {
			$result = [];

			if(!in_array($type, [ 'text', 'media' ])) {
				throw new \Exception("Type tidak valid. Hanya menerima type 'text' atau 'media'");
			}

			if(!array_key_exists('to', $data) && !array_key_exists('phone', $data)) {
				throw new \Exception("Harap masukan nomor telepon dengan key 'to' atau 'phone'");
			}

			$result['phone'] = $data['to'] ?? $data['phone'];
			$result['phone'] = self::reformatPhoneNumber($result['phone']);

			if($type == 'text') {
				if(!array_key_exists('text', $data) && !array_key_exists('message', $data)) {
					throw new \Exception("Harap masukan pesan dengan key 'text' atau 'message'");
				} else {
					$result['message'] = $data['text'] ?? $data['message'];
				}
			} elseif($type == 'media') {
				if(!array_key_exists('path', $data)) {
					throw new \Exception("Harap masukkan path file dengan key 'path'");
				}
				if(!self::isFileOrDirectoryExists($data['path'])) throw new \Exception("File tidak ditemukan");
				
				$result['file'] = new \CURLFile($data['path'], mime_content_type($data['path']));
			}

			return $result;
		} elseif ($apiType == self::API_TYPE_V2) {
			$result = [];

			if(!in_array($type, [ 'text', 'media' ])) {
				throw new \Exception("Type tidak valid. Hanya menerima type 'text' atau 'media'");
			}

			if(!array_key_exists('to', $data) && !array_key_exists('phone', $data)) {
				throw new \Exception("Harap masukan nomor telepon dengan key 'to' atau 'phone'");
			}

			$result['recipientId'] = $data['to'] ?? $data['phone'];
			$result['recipientId'] = self::reformatPhoneNumber($result['recipientId']);
			$result['recipient'] = $result['recipientId'];

			if($type == 'text') {
				if(!array_key_exists('text', $data) && !array_key_exists('message', $data)) {
					throw new \Exception("Harap masukan pesan dengan key 'text' atau 'message'");
				} else {
					$result['message'] = $data['text'] ?? $data['message'];
				}

				return $result;
			} elseif($type == 'media') {
				$result['file_type'] = '';
				if(array_key_exists('text', $data)) {
					$result['message'] = $data['text'];
				}
				if(array_key_exists('message', $data)) {
					$result['message'] = $data['message'];
				}
				if(array_key_exists('caption', $data)) {
					$result['caption'] = $data['caption'];
				}
				if(!array_key_exists('path', $data)) {
					throw new \Exception("Harap masukkan path file dengan key 'path'");
				}
				$path = $data['path'];
				if(!self::isFileOrDirectoryExists($path)) throw new \Exception("File tidak ditemukan");

				if(pathinfo($path)['extension'] == 'pdf') {
					$result['pdfFile'] = new \CURLFile($path, mime_content_type($path), basename($path)); 
					$result['file_type'] = 'pdf';
				} elseif(explode('/', mime_content_type($path))[0] == 'image') {
					$result['gambar'] = new \CURLFile($path, mime_content_type($path), basename($path)); 
					$result['file_type'] = 'image';
				} else {
					throw new \Exception("Only support image or pdf file");
				}

				return $result;
			}
		}
	}



	/**
	* 	Untuk kirim chat WhatsApp
	* 	@param array $data
	* 	@example ada dibawah
	* 	@return json
	*/
	public static function sendChat($data)
	{
		$apiServer = self::getValidApiServer($data);
		$apiType = self::getValidApiType($data);
		$data = self::parseData($data, 'text');
		return self::send($apiServer, $apiType, $data, 'text');
	}

	/**
	* 	Untuk kirim media WhatsApp
	* 	@param array $data
	*	@example ada dibawah
	* 	@return json
	*/
	public static function sendMedia($data)
	{
		$apiServer = self::getValidApiServer($data);
		$apiType = self::getValidApiType($data);
		$data = self::parseData($data, 'media');
		return self::send($apiServer, $apiType, $data, 'media');
	}


	/**
	* 	Untuk eksekusi pengiriman pesan/media
	* 	@param array $sendData
	* 	@return json
	*/
	private static function send($apiServer, $apiType, $sendData, $type)
	{
		if($apiType == self::API_TYPE_V1) {
			$endpoint = $apiServer . '/send';
			$ch = curl_init($endpoint);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $sendData);
			$res = curl_exec($ch);
			curl_close($ch);
			return $res;
		} elseif($apiType == self::API_TYPE_V2) {
			if($type == 'text') {
				$endpoint = $apiServer . '/kirim-pesan-gambar-caption';
				$ch = curl_init($endpoint);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($sendData));
				$res = curl_exec($ch);
				curl_close($ch);
				return $res;
			} elseif ($type == 'media') {
				if($sendData['file_type'] == 'pdf') {
					$endpoint = $apiServer . '/send-pdf';
				} else {
					$endpoint = $apiServer . '/kirim-pesan-gambar-caption';
				}
				$ch = curl_init($endpoint);
				curl_setopt($ch, CURLOPT_HEADER, true);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_HTTPHEADER, [ "Content-Type:multipart/form-data" ]);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $sendData);
				$res = curl_exec($ch);
				curl_close($ch);
				return $res;
			}
		}
	}


	/**
	* 	Untuk menerima data respon dari api
	* 	@param \Illuminate\Http\Request $request
	* 	@return json
	*/
	public static function receive($request)
	{
		$phone 	= explode('@', $request->phone)[0];
		$type 	= $request->type;

		if($type == 'ack')
		{
			$status = $request->message;
			$statusCode = 0;

			if ($status == 'SERVER') {
				$status 	= 'sent';
				$statusCode	= self::STATUS_SENT;
			} elseif ($status == 'DEVICE') {
				$status 	= 'received';
				$statusCode	= self::STATUS_RECEIVED;
			} elseif ($status == 'READ') {
				$status 	= 'read';
				$statusCode	= self::STATUS_READ;
			}

			$result = [
				'type'				=> $type,
				'is_acknowledge'	=> true,
				'is_message'		=> false,
				'phone'				=> $phone,
				'status'			=> $status,
				'status_code'		=> $statusCode,
			];

			self::putTemps('whatsapp_acknowledge.txt', $result);
			return $result;
		}
		elseif ($type == 'reply')
		{
			$hasFile 	= false;
			$fileData 	= [];

			if($request->is_file === true)
			{
				self::createMediaDirectoryIfNotExists();
				$hasFile = true;
				$ext = '';
				$fileData['file_mime'] 	= $request->file_mime;
				try {
					$ext = self::mimeToExtension($request->file_mime);
				} catch (\Exception $e) {}
				$fileData['file_extension'] = $ext;

				$filename = 'file_'.date('Ymd_His').'.'.$ext;
				$filepath = self::mediaDirectory($filename);
				self::createFile($filepath, base64_decode($request->file_data));
				$fileData['file_path']	= $filepath;
				$fileData['file_link']	= self::mediaLink($filename);
				$fileData['file_data']	= $request->file_data;
			}

			$returnData = [
				'type'				=> $type,
				'is_acknowledge'	=> false,
				'is_message'		=> true,
				'phone'				=> $phone,
				'message'			=> $request->message,
				'is_has_file'		=> $hasFile,
			];

			$result = array_merge($returnData, $fileData);
			self::putTemps('whatsapp.txt', $result);

			return $result;
		}
	}


	private static function createMediaDirectoryIfNotExists()
	{
		$path = self::mediaDirectory();

		if(!self::isFileOrDirectoryExists($path)) {
			self::createDirectory($path);
		}
	}


}

/**
* @see Send Chat Tutorial
parameter array
# string api_type 		=> (Opsional) Type API, Pilihannya : API_TYPE_V1 (default), API_TYPE_V2
API_TYPE_V1 // type lama buatan mas vaizal, default
API_TYPE_V2 // type baru buatan pak bambang

# string api_server		=> (Opsional) Jika tidak di setting maka akan ambil dari settingan
Untuk ambil default dari setting bisa di edit di bagian method defaultApiServer()

# string to|phone 		=> Nomor Telepon
# string text|message 	=> Isi Pesan
example :
Whatsapp::sendChat([
	'api_type'	=> Whatsapp::API_TYPE_V2, // Opsional
	'api_server'=> 'http://10.1.105.164:3000', // Opsional
	'to'		=> "6282316425264",
	'text'		=> "Text Pesan"
]);
*/


/**
* @see Send Media Tutorial
parameter array
# string api_type 		=> (Opsional) Type API, Pilihannya : API_TYPE_V1 (default), API_TYPE_V2
API_TYPE_V1 // type lama buatan mas vaizal, default
API_TYPE_V2 // type baru buatan pak bambang

# string api_server		=> (Opsional) Jika tidak di setting maka akan ambil dari settingan
Untuk ambil default dari setting bisa di edit di bagian method defaultApiServer()
# string to|phone	=> Nomor Telepon
# string path 		=> Path file
# string caption	=> Caption (opsional)
# string message	=> Pesan tambahan (opsional)
example : 
Whatsapp::sendMedia([
	'api_type'	=> Whatsapp::API_TYPE_V2, // Opsional
	'api_server'=> 'http://10.1.105.164:3000', // Opsional
	'to'		=> "6282316425264",
	'path'		=> "invoice/INV0001.pdf",
	'caption'	=> 'Test caption'
	'message'	=> 'Test pesan'
]);
*/