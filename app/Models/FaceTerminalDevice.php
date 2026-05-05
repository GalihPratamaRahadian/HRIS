<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use DataTables;

class FaceTerminalDevice extends Model
{
	protected $fillable = [ 'device_name', 'ip_address', 'port', 'username', 'password', 'type', 'meta', 'status' ];

	const TYPE_HIKVISION_MINMOE	= 1;
	const TYPE_CHINA_FT			= 2;


	const STATUS_ACTIVE		= 1;
	const STATUS_NOT_ACTIVE	= 2;


	public static function availableTypes()
	{
		return [
			[
				'type'	=> self::TYPE_HIKVISION_MINMOE,
				'label'	=> 'FT Hikvision Minmoe',
			],
			[
				'type'	=> self::TYPE_CHINA_FT,
				'label'	=> 'FT China',
			],
		];
	}


	public function isTypeHikvisionMinmoe()
	{
		return $this->type == self::TYPE_HIKVISION_MINMOE ? true : false;
	}


	public function isTypeChinaFT()
	{
		return $this->type == self::TYPE_CHINA_FT ? true : false;
	}


	public function typeText()
	{
		foreach(self::availableTypes() as $type) {
			if($type['type'] == $this->type) {
				return $type['label'];
			}
		}

		return null;
	}


	public static function createFaceTerminalDevice($request)
	{
		$device = self::create([
			'device_name'	=> $request->device_name,
			'ip_address'	=> $request->ip_address,
			'port'			=> $request->port,
			'username'		=> $request->username,
			'password'		=> $request->password,
			'type'			=> $request->type,
			'status'		=> $request->status,
		]);

		$device->setMetaData($request);
		// $device->pushAllUsers();

		return $device;
	}


	public function updateFaceTerminalDevice($request)
	{
		$this->update([
			'device_name'	=> $request->device_name,
			'ip_address'	=> $request->ip_address,
			'port'			=> $request->port,
			'username'		=> $request->username,
			'password'		=> $request->password,
			'type'			=> $request->type,
			'status'		=> $request->status,
		]);

		$this->setMetaData($request);
		// $this->pushAllUsers();

		return $this;
	}


	public function setMetaData($request)
	{
		if (!empty($request->geolocation)) {
			$geolocation = explode(";", $request->geolocation);

			if (count($geolocation) == 2) {
				if (is_numeric($geolocation[0]) && is_numeric($geolocation[1])) {
					$this->setMeta('latitude', $geolocation[0]);
					$this->setMeta('longitude', $geolocation[1]);
				}
			}
		}

		if($this->isTypeChinaFT())
		{
			if(!empty($request->device_id)) {
				$this->setMeta('device_id', $request->device_id);
			}
		}

		return $this;
	}


	public function setMeta($key, $value)
	{
		$meta = $this->getMetaData();

		if (array_key_exists($key, $meta)) {
			unset($meta[$key]);
		}

		$meta[$key] = $value;

		$this->update([
			'meta'	=> serialize($meta)
		]);

		return $this;
	}


	public function getMeta($key)
	{
		if (!$this->isMetaExists($key)) return null;

		return $this->getMetaData()[$key];
	}


	public function isMetaExists($key)
	{
		return array_key_exists($key, $this->getMetaData());
	}


	public function getMetaData()
	{
		if(empty($this->meta)) return [];

		return unserialize($this->meta);
	}


	public function deleteFaceTerminalDevice()
	{
		return $this->delete();
	}



	public static function apiDT()
	{
		$data = self::all();

		return DataTables::of($data)
			->editColumn('type', function($data){
				return $data->typeText();
			})
			->editColumn('status', function($data){
				return $data->statusHtml();
			})
			->addColumn('action', function($data){
				$button = '
				<div class="dropdown">
					<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Aksi
					</button>
					<div class="dropdown-menu">';

				if(UserPermission::check('face_terminal_device', 'u')) {
					$button .= '
						<a class="dropdown-item" href="'.route('face_terminal_device.edit', $data->id).'" title="Edit Face Terminal Device">
							<i class="mdi mdi-pencil"></i> Edit 
						</a>';
				}

				if(UserPermission::check('face_terminal_device', 'd')) {
					$button .= '
						<a class="dropdown-item delete" href="javascript:void(0);" data-href="'.route('face_terminal_device.destroy', $data->id).'" title="Hapus Face Terminal Device">
							<i class="mdi mdi-trash-can"></i> Hapus
						</a>';
				}

				if(!UserPermission::check('face_terminal_device', 'u') && !UserPermission::check('face_terminal_device', 'd')) {
					$button .= '
						<a class="dropdown-item" href="javascript:void(0);">
							Tidak Ada Aksi
						</a>';
				}

				$button .= '
					</div>
				</div>';

				return $button;
			})
			->rawColumns([ 'status', 'action' ])
			->make(true);
	}


	public function pushUser($data)
	{
		if ($this->isTypeHikvisionMinmoe()) {
			return $this->pushUserToTypeHikvisionMinmoe($data);
		} elseif ($this->isTypeChinaFT()) {
			return $this->pushUserToTypeChinaFT($data);
		}
	}


	public static function pushUserToAllDevices($data)
	{
		foreach(self::getActiveFaceTerminalDevices() as $device) 
		{
			$device->pushUser($data);
		}

		return true;
	}


	public static function eventSubscribe($request)
	{
		if(!$log = FaceTerminalLog::createFaceTerminalLog($request)) return false;

		// Cek auth ID kosong
		if(empty($request->authId)) return self::responseForStranger($log);

		$faceTerminalUser = FaceTerminalUser::find($request->authId);

		// Cek terdaftar sebagai user face terminal
		if(!$faceTerminalUser) return self::responseForStranger($log);

		$resultData = null;

		if($faceTerminalUser->isEmployee())
		{
			$employee = $faceTerminalUser->employee;
			if($employee) {
				Attendance::createAttendanceViaFaceTerminal($faceTerminalUser->id_reference, $log);

				$dataPersonal = [
					'type'      => 'karyawan',
					'nama'      => $employee->employee_name,
					'nik'       => $employee->employee_number,
					'departemen'=> $employee->departmentName(),
				];

				$jobStatusValidate = $employee->isJobStatusValid();
				$maskAndTemperatureValidate = $log->isUsingMask() && $log->isNormalTemperature();

				$resultData = [
					'valid'				=> true,
					'waktu'				=> date('d-m-Y H:i:s'),
					'tujuan'			=> null,
					'data'				=> $dataPersonal,
					'face'				=> base64_encode(\File::get($log->facePhotoPath())),
					'image'				=> base64_encode(\File::get($log->photoPath())),
					'normalTemperature' => $log->isNormalTemperature(),
					'temperature'		=> $log->temperature,
				];

				if($maskAndTemperatureValidate && $jobStatusValidate)
				{
					return response()->json(array_merge($resultData, [
						'akses'		=> true,
						'pesan'		=> "Anda diperbolehkan masuk",
						'alasan'	=> "YY",
					]));

					$this->openGate();
				}
				else
				{
					// Tidak boleh lewat gate
					if(!$log->isNormalTemperature())
					{
						return response()->json(array_merge($resultData, [
							'akses'     => false,
							'pesan'     => "Anda tidak diperbolehkan masuk",
							'alasan'    => "Suhu badan tidak normal",
						]));
					}
					elseif(!$log->isUsingMask())
					{
						return response()->json(array_merge($resultData, [
							'akses'		=> false,
							'pesan'		=> "Anda tidak diperbolehkan masuk",
							'alasan'	=> "Tidak menggunakan masker",
						]));
					}

					// Masa kerja habis
					if(!$jobStatusValidate)
					{
						return response()->json(array_merge($resultData, [
							'akses'     => false,
							'pesan'     => "Anda tidak diperbolehkan masuk",
							'alasan'    => "Tidak ada masa kerja yang berlaku",
						]));
					}
				}

			}
		}

		return false;
	}


	private static function responseForStranger($log)
	{
		$resultData = [
			'valid'				=> true,
			'waktu'				=> date('d-m-Y H:i:s'),
			'tujuan'			=> null,
			'data'				=> [
				'type'      => 'karyawan',
				'nama'      => "- ".Setting::getValue('stranger_name', 'Stranger')." -",
				'nik'       => "-",
				'departemen'=> "-",
			],
			'face'				=> base64_encode(\File::get($log->facePhotoPath())),
			'image'				=> base64_encode(\File::get($log->photoPath())),
			'normalTemperature' => $log->isNormalTemperature(),
			'temperature'		=> $log->temperature,
			'akses'     		=> false,
			'pesan'     		=> "Anda tidak diperbolehkan masuk",
			'alasan'    		=> "Anda tidak terdaftar",
		];

		return response()->json($resultData);
	}


	private function pushUserToTypeHikvisionMinmoe($data)
	{
		$route = setting('relay_face_terminal_url', 'http://localhost:2000')."/create";
		$data = $this->toHikvisionMinmoeValidData($data);
		unset($data['path']);

		$dataPost = http_build_query($data).'&'.http_build_query([
			'device'	=> $this->device_name,
			'ip'		=> $this->ip_address,
			'port'		=> $this->port,
			'username'	=> $this->username,
			'password'	=> $this->password
		]);

		// $dataPost = array_merge($data, [
		// 	'device'	=> $this->device_name,
		// 	'ip'		=> $this->ip_address,
		// 	'port'		=> $this->port,
		// 	'username'	=> $this->username,
		// 	'password'	=> $this->password
		// ]);

		# Log
		$logContent = now()." ROUTE : ".$route."\n";
		// $logContent .= now()." DATA : ".http_build_query($dataPost)."\n\n";
		\File::append(storage_path('app/public/logs/ft_push_log.txt'), $logContent);

		try {
			$curl = curl_init(); 
			curl_setopt($curl, CURLOPT_URL, $route);
			curl_setopt ($curl, CURLOPT_POST, TRUE);
			curl_setopt ($curl, CURLOPT_POSTFIELDS, $dataPost); 

			curl_setopt($curl, CURLOPT_USERAGENT, 'api');

			curl_setopt($curl, CURLOPT_TIMEOUT, 1); 
			curl_setopt($curl, CURLOPT_HEADER, 0);
			curl_setopt($curl,  CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 1);
			curl_setopt($curl, CURLOPT_DNS_CACHE_TIMEOUT, 10); 

			curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
			$data = curl_exec($curl);
			$http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE); 
			curl_close($curl);

			// $response = \Http::asForm()->post($route, $dataPost);
			// $ch = curl_init();

			// curl_setopt($ch, CURLOPT_URL, $route);
			// curl_setopt($ch, CURLOPT_POST, 1);
			// curl_setopt($ch, CURLOPT_POSTFIELDS, $dataPost);
			// curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));


			// // receive server response ...
			// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);		
			// $data = curl_exec($ch);
			// $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
			// curl_close($ch);	
			// $ch = curl_init();

			# Log
			$logContent = now()." RESULT : \n\n";
			\File::append(storage_path('app/public/logs/ft_push_log.txt'), $logContent);

			return true;
		} catch (\Exception $e) {

			# Log
			$logContent = now()." ERROR : ".$e->getFile()." : ".$e->getLine()." - ".$e->getMessage()."\n\n";
			\File::append(storage_path('app/public/logs/ft_push_log.txt'), $logContent);
		}
	}


	private function pushUserToTypeChinaFT($data)
	{
		$meta = $this->getMetaData();

		$base64Photo = base64_encode(\File::get($data['path']));
		$picinfo = "data:image/jpeg;base64,".$base64Photo;

		// Debug
		\File::put(Setting::temps('foto.txt'), $picinfo);

		$pushData = [
			"operator"	=> "AddPerson",
			"info"		=> [
				"DeviceID" 		=> $meta['device_id'],
				"IdType"		=> 0,
				"CustomizeID"	=> $data['id'],
				"PersonType"	=> 0,
				"Name"			=> $data['name'],
				"CardType"		=> 0,
				"IdCard"		=> $data['card'],
				"ValidBegin"	=> $data['validStart'], 
				"ValidEnd"		=> $data['validEnd'],
			],
			"picinfo"	=> $picinfo
		];

		// Debug
		\File::put(Setting::temps('data.json'), json_encode($pushData));

		$destination = "http://{$this->ip_address}:{$this->port}";
		$destination .= "/action/AddPerson";

		$res = \Http::withBasicAuth($this->username, $this->password)
					->contentType("text/plain")->send('POST', $destination, [
						'body' => str_replace("\/", "/", json_encode($pushData)),
					])->json();

		// Debug
		\File::put(Setting::temps('res.txt'), json_encode($res));
	}


	public static function removeUserFromAllDevices($data)
	{
		foreach(self::getActiveFaceTerminalDevices() as $device) 
		{
			$device->removeUser($data);
		}

		return true;
	}


	public function removeUser($data)
	{
		if ($this->isTypeHikvisionMinmoe()) {
			return $this->removeUserFromTypeHikvisionMinmoeAlternate($data);
		} elseif ($this->isTypeChinaFT()) {
			return $this->removeUserFromTypeChinaFT($data);
		}
	}


	public function removeUserFromTypeChinaFT($data)
	{
		$meta = $this->getMetaData();

		$pushData = [
			"operator"	=> "DeletePerson",
			"info"		=> [
				"DeviceID" 		=> $meta['device_id'],
				"TotalNum"		=> 1,
				"IdType"		=> 0,
				"CustomizeID"	=> [
					$data['id']
				],
			],
		];

		$destination = "http://{$this->ip_address}:{$this->port}";
		$destination .= "/action/DeletePerson";

		$res = \Http::withBasicAuth($this->username, $this->password)
					->contentType("text/plain")->send('POST', $destination, [
						'body' => str_replace("\/", "/", json_encode($pushData)),
					])->json();

		// Debug
		\File::put(Setting::temps('res.txt'), json_encode($res));
	}


	private function removeUserFromTypeHikvisionMinmoe($data)
	{
		$route = setting('relay_face_terminal_url', 'http://localhost:2000')."/delete";

		$dataPost = http_build_query($data).'&'.http_build_query([
			'device'	=> $this->device_name,
			'ip'		=> $this->ip_address,
			'port'		=> $this->port,
			'username'	=> $this->username,
			'password'	=> $this->password
		]);

		# Log
		$logContent = now()." DATA DELETE : ".$dataPost."\n\n";
		\File::append(storage_path('app/public/logs/ft_push_log.txt'), $logContent);

		try {
			$curl = curl_init(); 
			curl_setopt($curl, CURLOPT_URL, $route);
			curl_setopt ($curl, CURLOPT_POST, TRUE);
			curl_setopt ($curl, CURLOPT_POSTFIELDS, $dataPost); 

			curl_setopt($curl, CURLOPT_USERAGENT, 'api');

			curl_setopt($curl, CURLOPT_TIMEOUT, 1); 
			curl_setopt($curl, CURLOPT_HEADER, 0);
			curl_setopt($curl,  CURLOPT_RETURNTRANSFER, false);
			curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 1);
			curl_setopt($curl, CURLOPT_DNS_CACHE_TIMEOUT, 10); 

			curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
			$data = curl_exec($curl);   
			curl_close($curl);			

			# Log
			$logContent = now()." RESULT DELETE : ".$data."\n\n";
			\File::append(storage_path('app/public/logs/ft_push_log.txt'), $logContent);

			return true;
		} catch (\Exception $e) {

			# Log
			$logContent = now()." ERROR DELETE : ".$e->getFile()." : ".$e->getLine()." - ".$e->getMessage()."\n\n";
			\File::append(storage_path('app/public/logs/ft_push_log.txt'), $logContent);
		}
	}


	private function removeUserFromTypeHikvisionMinmoeAlternate($data)
	{
		$route = setting('relay_face_terminal_url', 'http://localhost:2000')."/create";

		$data['card'] = '';
		$data['name'] = '';
		$data['validStart'] = date('Y-m-d 00:00:00');
		$data['validEnd'] = date('Y-m-d 00:00:00');
		$data['fp'] = '';

		$dataPost = http_build_query($data).'&'.http_build_query([
			'device'	=> $this->device_name,
			'ip'		=> $this->ip_address,
			'port'		=> $this->port,
			'username'	=> $this->username,
			'password'	=> $this->password
		]);

		# Log
		$logContent = now()." DATA DELETE ALTERNATE : ".$dataPost."\n\n";
		\File::append(storage_path('app/public/logs/ft_push_log.txt'), $logContent);

		try {
			$curl = curl_init(); 
			curl_setopt($curl, CURLOPT_URL, $route);
			curl_setopt ($curl, CURLOPT_POST, TRUE);
			curl_setopt ($curl, CURLOPT_POSTFIELDS, $dataPost); 

			curl_setopt($curl, CURLOPT_USERAGENT, 'api');

			curl_setopt($curl, CURLOPT_TIMEOUT, 1); 
			curl_setopt($curl, CURLOPT_HEADER, 0);
			curl_setopt($curl,  CURLOPT_RETURNTRANSFER, false);
			curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 1);
			curl_setopt($curl, CURLOPT_DNS_CACHE_TIMEOUT, 10); 

			curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
			$data = curl_exec($curl);   
			curl_close($curl);			

			# Log
			$logContent = now()." RESULT DELETE ALTERNATE : ".$data."\n\n";
			\File::append(storage_path('app/public/logs/ft_push_log.txt'), $logContent);

			return true;
		} catch (\Exception $e) {

			# Log
			$logContent = now()." ERROR DELETE ALTERNATE : ".$e->getFile()." : ".$e->getLine()." - ".$e->getMessage()."\n\n";
			\File::append(storage_path('app/public/logs/ft_push_log.txt'), $logContent);
		}
	}


	private function toHikvisionMinmoeValidData($data)
	{
		if(!array_key_exists('validStart', $data)) {
			$data['validStart'] = date('Y-M-d H:i:s');
		}

		return $data;
	}


	public static function faceCompare($pathOne, $pathTwo)
	{
		$serverURL = appconfig('face_compare_url');
		$serverUsername = appconfig('face_compare_username');
		$serverPassword = appconfig('face_compare_password');

		try {
			if(!empty($serverURL) && !empty($serverUsername) && !empty($serverPassword))
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

				$destination = $serverURL;
				$destination .= "/action/GetPictureSimilarity";

				$response = \Http::withBasicAuth($serverUsername, $serverPassword)
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


	public function pushAllUsers()
	{
		if(!$this->isActive()) return $this;

		foreach(Employee::getActiveEmployees() as $employee)
		{
			$this->pushUser($employee->fetchEmployeeForFaceTerminalDevice());
		}

		return $this;
	}


	public function getLocation()
	{
		if(!$this->isMetaExists('latitude') || !$this->isMetaExists('longitude')) return false;

		return (object) [
			'latitude'	=> $this->getMeta('latitude'),
			'longitude'	=> $this->getMeta('longitude'),
		];
	}


	public static function getActiveFaceTerminalDevices()
	{
		return self::where('status', self::STATUS_ACTIVE)->get();
	}


	public function isActive()
	{
		return $this->status == self::STATUS_ACTIVE;
	}


	public function statusText()
	{
		return $this->isActive() ? 'Aktif' : 'Tidak Aktif';
	}


	public function statusHtml()
	{
		$text = $this->statusText();
		$class = $this->isActive() ? 'text-success' : 'text-danger';

		return "<span class='{$class}'> {$text} </span>";
	}


	public static function isDeviceReachLimit()
	{
		$limit = Setting::getValue('device_limit', 0);

		if($limit == 0) {
			return false;
		} else {
			return self::count() >= $limit;
		}
	}
}
