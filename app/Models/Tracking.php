<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tracking extends Model
{
	protected $fillable = [ 'id_employee', 'id_tracking_location', 'check_in_photo', 'check_day_photo', 'check_out_photo', 'latitude', 'longitude', 'file_good_receipt', 'check_in_at', 'check_day_at', 'check_out_at' ];


	/**
	 * 	Relationship methods
	 * */
	public function employee()
	{
		return $this->belongsTo('App\Models\Employee', 'id_employee')->withTrashed();
	}

	public function trackingLocation()
	{
		return $this->belongsTo('App\Models\TrackingLocation', 'id_tracking_location');
	}


	/**
	 * 	CRUD methods
	 * */
	public static function createTracking($request)
	{
		$tracking = self::create(array_merge($request->all(), [
			'id_employee' => employee()->id
		]));
		$tracking->saveCheckInPhoto($request);
		$tracking->saveFileGoodReceipt($request);
		return $tracking;
	}


	/**
	 * 	Helper methods
	 * */
	public function saveCheckInPhoto($request)
	{
		if(!empty($request->check_in_photo_base64))
		{
			$photoBlob = base64_decode(explode(',', $request->check_in_photo_base64)[1]);
			$tempFilename = date('Ymd_His_').rand(100,999).'.jpeg';
			$tempPath = \Setting::temps($tempFilename);
			\File::put(storage_path('app/public/tracking_photo/'.$tempFilename), $photoBlob);
			$this->update([
				'check_in_photo' 	=> $tempFilename,
				'check_in_at'		=> now(),
			]);
		}

		return $this;
	}

	public function saveCheckDayPhoto($request)
	{
		if(!empty($request->check_day_photo_base64))
		{
			$photoBlob = base64_decode(explode(',', $request->check_day_photo_base64)[1]);
			$tempFilename = date('Ymd_His_').rand(100,999).'.jpeg';
			$tempPath = \Setting::temps($tempFilename);
			\File::put(storage_path('app/public/tracking_photo/'.$tempFilename), $photoBlob);
			$this->update([
				'check_day_photo' 	=> $tempFilename,
				'check_day_at'		=> now(),
			]);
		}

		return $this;
	}

	public function saveCheckOutPhoto($request)
	{
		if(!empty($request->check_out_photo_base64))
		{
			$photoBlob = base64_decode(explode(',', $request->check_out_photo_base64)[1]);
			$tempFilename = date('Ymd_His_').rand(100,999).'.jpeg';
			$tempPath = \Setting::temps($tempFilename);
			\File::put(storage_path('app/public/tracking_photo/'.$tempFilename), $photoBlob);
			$this->update([
				'check_out_photo' 	=> $tempFilename,
				'check_out_at'		=> now(),
			]);
		}

		return $this;
	}

	public function saveFileGoodReceipt($request)
	{
		if(!empty($request->file_upload_good_receipt))
		{
			$file = $request->file('file_upload_good_receipt');
			$filename = date('Ymd_His_').rand(100,999).'.'.$file->getClientOriginalExtension();
			$file->move(storage_path('app/public/tracking_good_receipt'), $filename);
			$this->update([
				'file_good_receipt' => $filename
			]);
		}

		return $this;
	}

	public function checkInPhotoLink()
	{
		return url('storage/tracking_photo/'.$this->check_in_photo);
	}

	public function checkDayPhotoLink()
	{
		return url('storage/tracking_photo/'.$this->check_day_photo);
	}

	public function checkOutPhotoLink()
	{
		return url('storage/tracking_photo/'.$this->check_out_photo);
	}

	public function trackingLocationName()
	{
		return $this->trackingLocation->location_name ?? '-';
	}

	public function employeeName()
	{
		return $this->employee->employee_name ?? '-';
	}

	public function checkInAtFormatted($format = 'd M Y')
	{
		return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->check_in_at)->format($format);
	}

	public function checkDayAtFormatted($format = 'd M Y')
	{
		if(!empty($this->check_day_at)) {
			return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->check_day_at)->format($format);
		} else {
			return '-';
		}
	}

	public function checkOutAtFormatted($format = 'd M Y')
	{
		if(!empty($this->check_out_at)) {
			return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->check_out_at)->format($format);
		} else {
			return '-';
		}
	}

	public function fileGoodReceiptLink()
	{
		return url('storage/tracking_good_receipt/'.$this->file_good_receipt);
	}

	public function getLocation()
	{
		return \App\MyClass\Location::make($this->latitude, $this->longitude);
	}

	public function isCheckedDay()
	{
		return !empty($this->check_day_at);
	}

	public function isCheckedOut()
	{
		return !empty($this->check_out_at);
	}



	/**
	 * 	Static methods
	 * */
	public static function dataTable($request)
	{
		$data = self::select([ 'trackings.*' ])
					->leftJoin('tracking_locations', 'tracking_locations.id', '=', 'trackings.id_tracking_location')
					->leftJoin('employees', 'employees.id', '=', 'trackings.id_employee')
					->with([ 'trackingLocation', 'employee' ]);

		if(!empty($request->id_tracking_location)) {
			if($request->id_tracking_location != 'all') {
				$data = $data->where('id_tracking_location', $request->id_tracking_location);
			}
		}

		if(!empty($request->id_employee)) {
			if($request->id_employee != 'all') {
				$data = $data->where('id_employee', $request->id_employee);
			}
		}

		if(!empty($request->start_date)) {
			$data = $data->where('check_in_at', '>=', $request->start_date.' 00:00:00');
		}

		if(!empty($request->end_date)) {
			$data = $data->where('check_in_at', '<=', $request->end_date.' 23:59:59');
		}

		return \DataTables::eloquent($data)
			->editColumn('tracking_locations.location_name', function($data){
				return $data->trackingLocationName();
			})
			->editColumn('employees.employee_name', function($data){
				return $data->employeeName();
			})
			->addColumn('photo', function($data){
				$photo = '<img src="'.$data->checkInPhotoLink().'">';

				return $photo;
			})
			->addColumn('file_good_receipt', function($data){
				$link = '<a href="'.$data->fileGoodReceiptLink().'" target="_blank"> Lihat File Penerimaan Barang </a>';

				return $link;
			})
			->editColumn('check_in_at', function($data){
				return $data->checkInAtFormatted('d M Y H:i');
			})
			->addColumn('action', function($data){
				$button = '
				<div class="dropdown">
					<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Aksi
					</button>
					<div class="dropdown-menu">
						<a class="dropdown-item" href="'.route('admin.tracking.detail', $data->id).'" title="Detail Tracking">
							<i class="mdi mdi-magnify"></i> Detail 
						</a>
					</div>
				</div>';

				return $button;
			})
			->rawColumns([ 'photo', 'file_good_receipt', 'action' ])
			->make(true);
	}
}
