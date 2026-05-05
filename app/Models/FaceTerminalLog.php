<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Image;
use Setting;
use DataTables;
use Illuminate\Database\Eloquent\Builder;

class FaceTerminalLog extends Model
{
	protected $fillable = [ 'date', 'device_name', 'auth_id', 'name', 'from', 'temperature', 'mask', 'photo' ];

	const NOT_USING_MASK	= 1;
	const USING_MASK		= 2;

	const INTERVAL_ALLOW_CREATE_LOG_SAME_AUTH = 5;


	public static function getStrangerName()
	{
		return Setting::getValue('stranger_name', 'Stranger');
	}


	public function faceTerminalUser()
	{
		return $this->belongsTo('App\Models\FaceTerminalUser', 'auth_id');
	}


	/**
	 * 	Helper methods
	 * */
	public function photoPath()
	{
		return storage_path('app/public/faceterminal_log/full/'.$this->photo);
	}

	public function photoLink()
	{
		if($this->isHasPhoto()) {
			return url('storage/faceterminal_log/full/'.$this->photo);
		}

		return url('images/no-image.jpg');
	}

	public function facePhotoPath()
	{
		return storage_path('app/public/faceterminal_log/face/'.$this->photo);
	}

	public function facePhotoLink()
	{
		if($this->isHasFacePhoto()) {
			return url('storage/faceterminal_log/face/'.$this->photo);
		}
		
		return url('images/no-image.jpg');
	}

	public function watermarkedPhotoPath()
	{
		return storage_path('app/public/faceterminal_log/with_watermark/'.$this->photo);
	}

	public function watermarkedPhotoLink()
	{
		if($this->isHasWatermarkedPhoto()) {
			return url('storage/faceterminal_log/with_watermark/'.$this->photo);
		}
		
		return url('images/no-image.jpg');
	}

	public function isHasPhoto()
	{
		if(empty($this->photo)) return false;
		return \File::exists($this->photoPath());
	}

	public function isHasFacePhoto()
	{
		if(empty($this->photo)) return false;
		return \File::exists($this->facePhotoPath());
	}

	public function isHasWatermarkedPhoto()
	{
		if(empty($this->photo)) return false;
		return \File::exists($this->watermarkedPhotoPath());
	}

	public function removePhoto()
	{
		if($this->isHasPhoto()) {
			\File::delete($this->photoPath());
		}

		if($this->isHasFacePhoto()) {
			\File::delete($this->facePhotoPath());
		}

		if($this->isHasWatermarkedPhoto()) {
			\File::delete($this->watermarkedPhotoPath());
		}

		$this->update([
			'photo'	=> null
		]);

		return $this;
	}


	public function temperatureText()
	{
		return $this->temperature."C";
	}


	public function dateText()
	{
		$tt = function($date) {
			return strtotime($date);
		};

		$dateText = \App\MyClass\Date::dayName(date('N', $tt($this->date)));
		$dateText .= ", ";
		$dateText .= date('d', $tt($this->date))." ";
		$dateText .= \App\MyClass\Date::monthName(date('m', $tt($this->date)))." ";
		$dateText .= date('Y', $tt($this->date))." ";
		$dateText .= date('H:i:s', $tt($this->date))." WIB";

		return $dateText;
	}


	public function createdAtTextSortable()
	{
		return date('Y-m-d H:i:s', strtotime($this->created_at));
	}


	public function isUsingMask()
	{
		if(empty($this->mask)) return false;

		return $this->mask == self::USING_MASK ? true : false;
	}


	public function isNormalTemperature()
	{
		return Setting::isNormalTemperature($this->temperature);
	}


	public function maskText()
	{
		if(empty($this->mask)) return '-';

		return $this->mask == self::USING_MASK ? 'Menggunakan Masker' : 'Tidak Menggunakan Masker';
	}


	public static function getLatestByAuthId($authId)
	{
		if(empty($authId)) return false;

		return self::where('auth_id', $authId)->first();
	}


	public static function getManyByAuthId($authId)
	{
		if(empty($authId)) return false;

		return self::where('auth_id', $authId)->get();
	}


	public static function createFaceTerminalLog($request)
	{
		if(!empty($request->authId))
		{
			$latestLog = self::getLatestByAuthId($request->authId);

			if($latestLog)
			{
				$date = strtotime(date('Y-m-d H:i:s'));
				$lastestDate = strtotime($latestLog);
				$interval = $date - $lastestDate;

				if($interval < self::INTERVAL_ALLOW_CREATE_LOG_SAME_AUTH)
				{
					return false;
				}
			}
		}

		return self::writeLog($request);
	}


	private static function writeLog($request)
	{
		$authId = null;
		$name   = self::getStrangerName();
		$from   = '-';

		if(!empty($request->authId))
		{
			$faceTerminalUser = FaceTerminalUser::find($request->authId);

			if($faceTerminalUser)
			{
				$authId = $faceTerminalUser->id;

				if($faceTerminalUser->isEmployee()) {
					if($employee = $faceTerminalUser->employee) {
						$name = $employee->employee_name;
						if($department = $employee->department) {
							$from = $department->department_name;
						}
					}
				}

				if($faceTerminalUser->isVisitor()) {
					if($visitor = $faceTerminalUser->visitor) {
						$name   = $visitor->nama_visitor;
						$from   = $visitor->perusahaan;
					}
				}
			}
		}

		$mask = $request->mask == true? self::USING_MASK : self::NOT_USING_MASK;

		try {
			$date = explode('T', $request->date)[0].' '.explode('T', $request->date)[1];
		} catch (\Exception $e) {
			$date = now()->format('Y-m-d H:i:s');
		}

		$log = self::create([
			'date'			=> $date,
			'device_name'	=> $request->device,
			'auth_id'		=> $authId,
			'name'			=> $name,
			'from'			=> $from,
			'temperature'	=> !empty($request->temperature) ? $request->temperature : null,
			'mask'			=> $mask,
		]);

		$log->setPhotoLog($request->picture);

		try {
			$log->setFaceLog();
		} catch (\Exception $e) {}

		try {
			$log->setWatermarkedPhoto();
		} catch (Exception $e) {}

		return $log;
	}


	public function setPhotoLog($base64Data)
	{

		$photo = base64_decode(self::createValidBase64($base64Data));
		$filename = date('YmdHis').".jpeg";
		$img = imagecreatefromstring($photo);
		
		if($img !== false) {
			header('Content-Type: image/jpeg');
			imagejpeg($img, storage_path('app/public/faceterminal_log/full/'.$filename));

			$this->update([
				'photo'	=> $filename,
			]);
		}

		return $this;
	}

	public function setFaceLog()
	{
		ini_set('memory_limit', '-1');
		if(!\File::exists($this->photoPath())) {
			return $this;
		}

		$face = Image::make($this->photoPath());

		if($face->width() > $face->height())
		{
			$face->resize(null, 432, function($const){
				$const->aspectRatio();
			});
		}
		else
		{
			$face->resize(352, null, function($const){
				$const->aspectRatio();
			});
		}

		$face->crop(352, 432);
		$face->save(storage_path('app/public/faceterminal_log/face/'.$this->photo));

		return $this;
	}


	public function setWatermarkedPhoto()
	{
		ini_set('memory_limit', '-1');
		$img = Image::make($this->photoPath()); 

		$img->text($this->name, $img->width() - 25, $img->height() - 85, function($font) {  
			$font->file(storage_path('fonts/Calibri.ttf'));  
			$font->size(15);  
			$font->color('#ffffff');  
			$font->align('right');  
			$font->valign('top');
		});

		$img->text($this->temperatureText(), $img->width() - 25, $img->height() - 65, function($font) {  
			$font->file(storage_path('fonts/Calibri.ttf'));  
			$font->size(15);  
			$font->color('#ffffff');  
			$font->align('right');  
			$font->valign('top');
		});

		$img->text($this->maskText(), $img->width() - 25, $img->height() - 45, function($font) {  
			$font->file(storage_path('fonts/Calibri.ttf'));  
			$font->size(15);  
			$font->color('#ffffff');  
			$font->align('right');  
			$font->valign('top');
		});

		$img->text($this->dateText(), $img->width() - 25, $img->height() - 25, function($font) {  
			$font->file(storage_path('fonts/Calibri.ttf'));  
			$font->size(15);  
			$font->color('#ffffff');  
			$font->align('right');  
			$font->valign('top');
		});

		$img->save(storage_path('app/public/faceterminal_log/with_watermark/'.$this->photo));

		return $this;
	}


	public function fetchData()
	{
		return [
			'id'				=> $this->id,
			'name'				=> $this->name,
			'from'				=> $this->from,
			'facePhoto'			=> $this->facePhotoLink(),
			'photo'				=> $this->photoLink(),
			'temperature'		=> $this->temperature,
			'temperatureText'	=> $this->temperatureText(),
			'mask'				=> $this->mask == self::USING_MASK ? true : false,
			'date'				=> date('Y-m-d H:i:s', strtotime($this->date))
		];
	}


	public static function fetchedDataForRecentPanel($start = null, $end = null)
	{
		$tt = function($date) { return strtotime($date); };

		$start = !empty($start) ? date('Y-m-d 00:00:00', $tt($start)) : today();
		$end = !empty($end) ? date('Y-m-d 23:59:59', $tt($end)) :  date('Y-m-d 23:59:59');

		$results = [];
		$logs = self::where('date', '>=', $start)
					->where('date', '<=', $end)
					->orderBy('date', 'desc')
					->get();

		foreach($logs as $log)
		{
			$results[] = $log->fetchData();
		}

		return $results;

	}


	private static function createValidBase64($base64)
	{
		$explodeBase64 = explode(",", $base64);

		if(count($explodeBase64) > 1) {
			return $explodeBase64[1];
		} else {
			return $explodeBase64[0];
		}
	}


	public static function amountOfLogsToday()
	{
		return self::where('date', 'like', "%".date('Y-m-d')."%")->count();
	}


	public static function apiDT($request)
	{
		$data = self::where('created_at', '!=', null);

		if(!empty($request->start_date) && !empty($request->end_date)) {
			$data = $data->whereDate('date', '>=', $request->start_date)
						 ->whereDate('date', '<=', $request->end_date);
		}

		if(!empty($request->people_type)) {
			$peopleType = $request->people_type;
			if($peopleType == 'employee') {
				$data = $data->whereHas('faceTerminalUser', function(Builder $query){
					$query->where('type', FaceTerminalUser::TYPE_EMPLOYEE);
				});
			} elseif ($peopleType == 'stranger') {
				$data = $data->where('name', self::getStrangerName());
			}
		}

		if(!empty($request->using_mask)) {
			$mask = $request->using_mask;
			if ($mask == 'yes') {
				$data = $data->where('mask', self::USING_MASK);
			} elseif ($mask == 'no') {
				$data = $data->where('mask', self::NOT_USING_MASK);
			}
		}

		if(!empty($request->temperature_status)) {
			$temperatureStatus = $request->temperature_status;
			if ($temperatureStatus == 'normal') {
				$data = $data->where('temperature', '>=', Setting::getMinTemperature())
							 ->where('temperature', '<=', Setting::getMaxTemperature());
			} elseif ($temperatureStatus == 'not_normal') {
				$data = $data->where('temperature', '<', Setting::getMinTemperature())
							 ->orWhere('temperature', '>', Setting::getMaxTemperature());
			}
		}

		return DataTables::eloquent($data)
			->editColumn('created_at', function($data){
				return $data->createdAtTextSortable();
			})
			->editColumn('name', function($data){
				if(empty($data->auth_id)) return '<span class="text-danger"> Stranger </span>';

				return $data->name;
			})
			->editColumn('photo', function($data){
				return '<img data-id="'.$data->id.'" src="'.$data->facePhotoLink().'" style="max-width: 150px; max-height: 100px; border-radius: 0px; width: unset; height: unset;" />';
			})
			->editColumn('temperature', function($data){
				return $data->temperatureText();
			})
			->editColumn('mask', function($data){
				return $data->isUsingMask() ? 'Ya' : 'Tidak';
			})
			->addColumn('action', function(){

			})
			->rawColumns([ 'name', 'photo', 'action' ])
			->make(true);
	}


	public function getFaceTerminalDevice()
	{
		$deviceNames = explode('-', $this->device_name);
		$id = $deviceNames[0];

		if(count($deviceNames) > 1) {
			if($device = FaceTerminalDevice::find($id)) {
				return $device;
			}
		}

		foreach(FaceTerminalDevice::all() as $device)
		{
			if($device->isMetaExists('device_id')) {
				$deviceID = $device->getMeta('device_id');
				if($deviceID == $id) {
					return $device;
					break;
				}
			}
		}

		$device = FaceTerminalDevice::where('device_name', $id)->first();

		return $device;
	}


	public function getLocation()
	{
		if($device = $this->getFaceTerminalDevice()) return $device->getLocation();

		return false;
	}
}
