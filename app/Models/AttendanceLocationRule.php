<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\MyClass\Location;

class AttendanceLocationRule extends Model
{
	protected $fillable = [ 'location_name', 'latitude', 'longitude', 'radius_distance', 'radius_unit' ];

	const UNIT_METERS		= 'm';
	const UNIT_KILOMETERS	= 'km';
	const UNIT_MILES		= 'miles';


	public static function availableRadiusUnit()
	{
		return [
			self::UNIT_METERS		=> 'Meters',
			self::UNIT_KILOMETERS	=> 'Kilometers',
			self::UNIT_MILES		=> 'Miles',
		];
	}


	public static function createAttendanceLocationRule($request)
	{
		$locationRule = self::create([
			'location_name'		=> $request->location_name,
			'latitude'			=> $request->latitude,
			'longitude'			=> $request->longitude,
			'radius_distance'	=> $request->radius_distance,
			'radius_unit'		=> $request->radius_unit,
		]);

		return $locationRule;
	}


	public function updateAttendanceLocationRule($request)
	{
		$this->update([
			'location_name'		=> $request->location_name,
			'latitude'			=> $request->latitude,
			'longitude'			=> $request->longitude,
			'radius_distance'	=> $request->radius_distance,
			'radius_unit'		=> $request->radius_unit,
		]);

		return $this;
	}


	public function deleteAttendanceLocationRule()
	{
		return $this->delete();
	}


	public function gmapsLink()
	{
		return $this->makeLocation()->gmapsLink();
	}


	public function coordinatePoint()
	{
		return $this->latitude.",".$this->longitude;
	}


	public function radiusDistanceText()
	{
		return $this->radius_distance." ".$this->radiusUnitText();
	}


	public function radiusUnitText()
	{
		try {
			return self::availableRadiusUnit()[$this->radius_unit];
		} catch (\Exception $e) {
			return '-';
		}
	}


	public static function dt()
	{
		$data = self::select([ 'attendance_location_rules.*' ]);

		return \DataTables::eloquent($data)
			->addColumn('map', function($data){
				return "<a href='".$data->gmapsLink()."' target='_blank'> Lihat Peta </a>";
			})
			->addColumn('radius_distance', function($data){
				return $data->radius_distance;
			})
			->addColumn('radius_unit', function($data){
				return $data->radiusUnitText();
			})
			->addColumn('action', function($data){
				$button = '
				<div class="dropdown">
					<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Aksi
					</button>
					<div class="dropdown-menu">
						<a class="dropdown-item" href="'.route('attendance_location_rules.detail', $data->id).'" title="Detail Lokasi">
							<i class="mdi mdi-magnify"></i> Detail 
						</a>';

				if(UserPermission::check('attendance_location_rules', 'u')) {
					$button .= '
						<a class="dropdown-item" href="'.route('attendance_location_rules.edit', $data->id).'" title="Edit Lokasi">
							<i class="mdi mdi-pencil"></i> Edit 
						</a>';
				}

				if(UserPermission::check('attendance_location_rules', 'd')) {
					$button .= '
						<a class="dropdown-item delete" href="javascript:void(0);" data-href="'.route('attendance_location_rules.destroy', $data->id).'" title="Hapus Lokasi">
							<i class="mdi mdi-trash-can"></i> Hapus
						</a>';
				}

				$button .= '
					</div>
				</div>';

				return $button;
			})
			->rawColumns([ 'map', 'action' ])
			->make(true);
	}


	public function radiusUnitIsMeters()
	{
		return $this->radius_unit == self::UNIT_METERS; 
	}


	public function radiusUnitIsKilometers()
	{
		return $this->radius_unit == self::UNIT_KILOMETERS; 
	}


	public function radiusUnitIsMiles()
	{
		return $this->radius_unit == self::UNIT_MILES; 
	}


	public function makeLocation()
	{
		return Location::make($this->latitude, $this->longitude);
	}


	public function isInRadius($latitude, $longitude)
	{
		$fromCoordinate = $this->makeLocation();
		$toCoordinate = Location::make($latitude, $longitude);
		$distance = 0;
		
		if($this->radiusUnitIsMeters()) $distance = $fromCoordinate->distanceInMeters($toCoordinate);
		if($this->radiusUnitIsKilometers()) $distance = $fromCoordinate->distanceInKilometers($toCoordinate);
		if($this->radiusUnitIsMiles()) $distance = $fromCoordinate->distanceInMiles($toCoordinate);

		return (double) $this->radius_distance > (double) $distance;
	}
}
