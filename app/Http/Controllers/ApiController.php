<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Position;

use App\Device;
use App\Models\Employee;
use App\GateAuth;
use App\MyClass\Photo;
use App\MyClass\FaceTerminal;
use App\MyClass\Helper;
use App\MyClass\WhatsappNew;
use DB;

class ApiController extends Controller
{
	/**
	 * 	Device API
	 * 	@return json
	 * */
	public function getDevices()
	{
		return \Res::success([
			'devices'	=> \App\Models\FaceTerminalDevice::getActiveFaceTerminalDevices(),
		]);
	}


	// Department
	public function getDepartment()
	{
		return response()->json(Department::all());
	}

	public function findDepartment(Department $department)
	{
		return response()->json($department);
	}


	// Position
	public function getPosition()
	{
		return response()->json(Position::all());
	}

	public function findPosition(Position $position)
	{
		return response()->json($position);
	}




	public function allDevice()
	{
		return Response::json(Device::all(), 200);
	}

	public function createEmployee(Request $request)
	{
		DB::beginTransaction();

		try {
			Employee::createEmployeeFromRegister($request);
			DB::commit();

			\File::put(\Setting::temps('sukses.txt'), '');
			return true;
		} catch (\Exception $e) {
			DB::rollback();

			\File::put(\Setting::temps('error.txt'), \Setting::errorMessage($e));
			return \Setting::errorMessage($e);
		}
	}


	public function faceTerminalEvent(Request $request)
	{
		try {
			$res1 = \App\Models\FaceTerminalDevice::eventSubscribe($request);
			$res2 = \App\Models\FaceTerminalDevice::eventSubscribe($request);

			if($res1) {
				return $res1;
			} else {
				return $res2;
			}
		} catch (\Exception $e) {
			\File::put(\Setting::temps('error.txt'), \Setting::errorMessage($e));
		}
	}


	public function eventSecond(Request $request)
	{
		$content	= $request->getContent();
		$randNumber = rand(1, 999);

		$data 	= json_decode($content, true);
		
		$toBlob = function($picture) {
			return base64_decode(explode(",", $picture)[1]);
		};

		\File::put(storage_path('event_logs/'.date('Ymd_His_').$randNumber.'.json'), $content);
		\File::put(storage_path("photo_logs/{$randNumber}.jpeg"), $toBlob($data['SanpPic']));

		$info   = $data['info'];
		$url    = env('EXPRESS_ROUTER_URL', 'http://localhost:2000');

		// \Http::withOptions([
		// 	'timeout' 		=> 30,
		// 	'synchronous'	=> false,
		// ])->post("{$url}/event", [
		// 	'device'		=> $info['DeviceID'],
		// 	'date'			=> $info['CreateTime'],
		// 	'authId'		=> $info['IdCard'],
		// 	'temperature'	=> $info['Temperature'],
		// 	'mask'			=> $info['isNoMask'] ? false : true,
		// 	'picture'		=> $data['SanpPic'],
		// ]);

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, "{$url}/event");
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_TIMEOUT, 1); 
		curl_setopt($curl, CURLOPT_POST, TRUE);
		curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 1);
		curl_setopt($curl, CURLOPT_DNS_CACHE_TIMEOUT, 10); 
		curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query([
			'device'		=> $info['DeviceID'],
			'date'			=> $info['CreateTime'],
			'authId'		=> $info['IdCard'],
			'temperature'	=> $info['Temperature'],
			'mask'			=> $info['isNoMask'] ? false : true,
			'picture'		=> $data['SanpPic'],
		]));
		curl_exec($curl);
		curl_close($curl);

		return true;
	}


	public function strangerEvent(Request $request)
	{
		$content	= $request->getContent();
		$randNumber = rand(1, 999);

		$data 	= json_decode($content, true);
		
		$toBlob = function($picture) {
			return base64_decode(explode(",", $picture)[1]);
		};

		\File::put(storage_path('event_logs/'.date('Ymd_His_').$randNumber.'.json'), $content);
		\File::put(storage_path("photo_logs/{$randNumber}.jpeg"), $toBlob($data['SanpPic']));

		$info   = $data['info'];
		$url    = env('EXPRESS_ROUTER_URL', 'http://localhost:2000');

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, "{$url}/event");
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_TIMEOUT, 1); 
		curl_setopt($curl, CURLOPT_POST, TRUE);
		curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 1);
		curl_setopt($curl, CURLOPT_DNS_CACHE_TIMEOUT, 10); 
	    curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query([
			'device'		=> $info['DeviceID'],
			'date'			=> $info['CreateTime'],
			'authId'		=> null,
			'temperature'	=> $info['Temperature'],
			'mask'			=> $info['isNoMask'] ? false : true,
			'picture'		=> $data['SanpPic'],
		]));
		curl_exec($curl);
		curl_close($curl);

		return true;
	}


	public function whatsapp(Request $request)
	{
		set_time_limit(0);
		ini_set('memory_limit', '2048M');
		$whatsappResult = \App\MyClass\Whatsapp::receive($request);

		if($whatsappResult['is_message']) {
			if($whatsappResult['is_has_file']) {
				$path = $whatsappResult['file_path'];
				$employees = \App\Models\Employee::getActiveEmployees();
				$similarity = 0;
				$employeeData = null;
				foreach($employees as $employee) {
					$resultCompare = \App\Models\FaceTerminalDevice::faceCompare($employee->photoPath('face'), $path);
					if($resultCompare > $similarity) {
						$similarity = $resultCompare;
						$employeeData = $employee;
					}
				}

				$receivedMessage = $whatsappResult['message'];

				if(strtolower($receivedMessage) == "hadir" && !empty($employeeData))
				{
					\App\Models\Attendance::clockInOrClockOutViaWhatsapp($employeeData, $whatsappResult);
				}
				else
				{
					$message = "Hai";

					if($similarity > 0) {
						$message .= "\n\nAnda terindentifikasi sebagai *". $employeeData->employee_name .'* dengan kemiripan *'.$similarity.'%*';
					} else {
						$message .= "\n\nAnda tidak dikenali";
					}

					$message .= "\n\n*Attendance System*";
					// \App\MyClass\Whatsapp::sendChat([
					// 	'text'	=> $message,
					// 	'to'	=> $whatsappResult['phone'],
					// ]);

					$EndPointWa = WhatsappNew::END_POINT_WA;
					if($EndPointWa == 'WA Baru'){
						// wa Baru
						$res = Helper::sendNotificationWhatsapp($whatsappResult['phone'], $message);
					}else{
						$res = \App\MyClass\Whatsapp::sendChat([
							'text'	=> $message,
							'to'	=> $whatsappResult['phone'],
						]);
					}
				}

			}
		}
	}

}
