<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrackingLocation extends Model
{
	protected $fillable = [ 'location_name', 'latitude', 'longitude', 'photo', 'description', 'address' ];


	/**
	 * 	CRUD methods
	 * */
	public static function createTrackingLocation($request)
	{
		$track = self::create($request->all());
		$track->setPhoto($request);
		return $track;
	}

	public function updateTrackingLocation($request)
	{
		$this->update($request->all());
		$this->setPhoto($request);
		return $this;
	}

	public function deleteTrackingLocation()
	{
		$this->removePhoto();
		return $this->delete();
	}


	/**
	 * 	Helper methods
	 * */
	public function setPhoto($request)
	{
		if(!empty($request->file_photo))
		{
			$this->removePhoto();
			$file = $request->file('file_photo');
			$filename = $this->id.date('_Ymdhis').'.'.$file->getClientOriginalExtension();
			$file->move(storage_path('app/public/track_location'), $filename);
			$this->update([
				'photo' => $filename
			]);
		}

		return $this;
	}

	public function removePhoto()
	{
		if($this->isHasPhoto())
		{
			\File::delete($this->photoPath());
			$this->update([
				'photo' => null
			]);
		}

		return $this;
	}

	public function photoLink()
	{
		if($this->isHasPhoto()) {
			return url('storage/track_location/'.$this->photo);
		} else {
			return url('images/no-image.jpg');
		}
	}

	public function photoPath()
	{
		return storage_path('app/public/track_location/'.$this->photo);
	}

	public function isHasPhoto()
	{
		if(empty($this->photo)) return false;
		return \File::exists($this->photoPath());
	}

	public function getLocation()
	{
		return \App\MyClass\Location::make($this->latitude, $this->longitude);
	}

	public function distanceInMeters($latitude, $longitude)
	{
		$storeLocation = \App\MyClass\Location::make($this->latitude, $this->longitude);
		$currentLocation = \App\MyClass\Location::make($latitude, $longitude);
		return $storeLocation->distanceInMeters($currentLocation);
	}

	public function distanceText($latitude, $longitude)
	{
		$distanceInMeters = $this->distanceInMeters($latitude, $longitude);
		if($distanceInMeters >= 1000) {
			return round(($distanceInMeters / 1000), 1).' Kilometer';
		} else {
			return round($distanceInMeters).' Meter';
		}
	}

	public function isVisitedToday()
	{
		$tracking = Tracking::where('id_employee', employee()->id)
					->where('id_tracking_location', $this->id)
					->where('check_in_at', '>=', now()->format('Y-m-d').' 00:00:00')
					->where('check_in_at', '<=', now()->format('Y-m-d').' 23:59:59')
					->first();
		return !empty($tracking);
	}

	public function isCheckedInToday()
	{
		$tracking = Tracking::where('id_employee', employee()->id)
					->where('id_tracking_location', $this->id)
					->where('check_in_at', '>=', now()->format('Y-m-d').' 00:00:00')
					->where('check_in_at', '<=', now()->format('Y-m-d').' 23:59:59')
					->first();
		return !empty($tracking);
	}

	public function isCheckedDayToday()
	{
		$tracking = Tracking::where('id_employee', employee()->id)
					->where('id_tracking_location', $this->id)
					->where('check_day_at', '>=', now()->format('Y-m-d').' 00:00:00')
					->where('check_day_at', '<=', now()->format('Y-m-d').' 23:59:59')
					->first();
		return !empty($tracking);
	}

	public function isCheckedOutToday()
	{
		$tracking = Tracking::where('id_employee', employee()->id)
					->where('id_tracking_location', $this->id)
					->where('check_out_at', '>=', now()->format('Y-m-d').' 00:00:00')
					->where('check_out_at', '<=', now()->format('Y-m-d').' 23:59:59')
					->first();
		return !empty($tracking);
	}


	/**
	 * 	Static methods
	 * */
	public static function dataTable($request)
	{
		$data = self::select([ 'tracking_locations.*' ]);

		return \DataTables::eloquent($data)
			->addColumn('photo_image', function($data){
				return "<img src='".$data->photoLink()."' />";
			})
			->addColumn('map', function($data){
				return "<a href='".$data->getLocation()->gmapsLink()."' target='_blank'> Lihat Peta </a>";
			})
			->addColumn('action', function($data){
				$button = '
				<div class="dropdown">
					<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Aksi
					</button>
					<div class="dropdown-menu">';

				if(UserPermission::check('tracking_location', 'u')) {
					$button .= '
						<a class="dropdown-item" href="'.route('admin.tracking_location.edit', $data->id).'" title="Edit Lokasi">
							<i class="mdi mdi-pencil"></i> Edit 
						</a>';
				}

				if(UserPermission::check('tracking_location', 'd')) {
					$button .= '
						<a class="dropdown-item delete" href="javascript:void(0);" data-href="'.route('admin.tracking_location.destroy', $data->id).'" title="Hapus Lokasi">
							<i class="mdi mdi-trash-can"></i> Hapus
						</a>';
				}

				if(!UserPermission::check('tracking_location', 'u') && !UserPermission::check('tracking_location', 'd')) {
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
			->rawColumns([ 'photo_image', 'map', 'action' ])
			->make(true);
	}


	public static function importFromExcel($request)
	{
		$amount = 0;

		if(!empty($request->file))
		{
			$file = $request->file('file');
			$extension = $file->getClientOriginalExtension();
			$filename = date('Ymdhis').'_Lokasi_Tracking.'.$extension;
			$tempPath = \Helper::tempsPath();
			$filepath = $tempPath.'/'.$filename;
			$file->move($tempPath, $filename);
			$parseData = \App\MyClass\SimpleXLSX::parse($filepath);

			if($parseData)
			{
				$iter = 0;
				foreach($parseData->rows() as $row)
				{
					$iter++;
					if($iter == 1) continue;

					// \DB::beginTransaction();
					// try {
						$locationName = $row[0];
						$description = $row[1] ?? null;
						$address = $row[2];
						$mapsLink = $row[3];
						$coordinate = \App\MyClass\Location::getCoordinateFromMapsLink($mapsLink);

						if(empty($coordinate->latitude) || empty($coordinate->longitude)) {
							continue;
						}

						$trackingLocation = self::where('location_name', $locationName)
												->where('address', $address)
												->first();
						if($trackingLocation) {
							$trackingLocation->update([
								'latitude'		=> $coordinate->latitude,
								'longitude'		=> $coordinate->longitude,
								'description'	=> $description,
							]);
						} else {
							self::create([
								'location_name'	=> $locationName,
								'address'		=> $address,
								'latitude'		=> $coordinate->latitude,
								'longitude'		=> $coordinate->longitude,
								'description'	=> $description,
							]);
						}
					// 	\DB::commit();
					// } catch (\Exception $e) {
					// 	\DB::rollback();
					// }
				}
			}

			\File::delete($filepath);
		}

		return $amount;
	}
}
