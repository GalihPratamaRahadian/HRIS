<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebAttendancePermission extends Model
{
	protected $fillable = [ 'id_employee', 'valid_until', 'locations' ];


	public function employee()
	{
		return $this->belongsTo('App\Models\Employee', 'id_employee');
	}


	public function isValid()
	{
		return date('Y-m-d') <= date('Y-m-d', strtotime($this->valid_until));
	}


	public function employeeName()
	{
		return $this->employee ? $this->employee->employee_name : '-';
	}


	public function getLocationIDs()
	{
		try {
			$locationIDs = unserialize($this->locations);

			if(!is_array($locationIDs)) return [];

			return $locationIDs;
		} catch (\Exception $e) {
			return [];
		}
	}


	public function addLocationID($id)
	{
		$location = AttendanceLocationRule::find($id);

		if($location) {
			$locationIDs = $this->getLocationIDs();
			$locationIDs[] = $id;
			$this->update([
				'locations'	=> serialize($locationIDs)
			]);
		}

		return $this;
	}


	public function getLocations()
	{
		$locations = [];

		foreach($this->getLocationIDs() as $id) {
			$location = AttendanceLocationRule::find($id);
			if($location) {
				$locations[] = $location;
			}
		}

		return $locations;
	}


	public function getLocationsHtml()
	{
		if(empty($this->locations)) return '-';
		if(count($this->getLocations()) == 0) return '-';

		$html = '';
		$iteration = 0;

		foreach($this->getLocations() as $location)
		{
			if($iteration > 0) $html .= ", ";
			$html .= '<a href="'.$location->gmapsLink().'" target="_blank">'.$location->location_name.'</a>';
			$iteration++;
		}

		return $html;
	}


	public function clearLocations()
	{
		$this->update([
			'locations'	=> serialize([])
		]);

		return $this;
	}


	public function isHasLocations()
	{
		if(empty($this->locations)) return false;
		if(count($this->getLocations()) == 0) return false;

		return true;
	}


	public static function apiDT()
	{
		$data = self::all();

		return \DataTables::of($data)
			->editColumn('employees.employee_name', function($data){
				return $data->employeeName();
			})
			->editColumn('locations', function($data){
				return $data->getLocationsHtml();
			})
			->addColumn('action', function($data){
				$button = '
				<div class="dropdown">
					<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Aksi
					</button>
					<div class="dropdown-menu">';

				if(UserPermission::check('web_attendance_permissions', 'u')) {
					$button .= '
						<a class="dropdown-item" href="'.route('web_attendance_permissions.edit', $data->id).'" title="Edit Setting">
							<i class="mdi mdi-pencil"></i> Edit 
						</a>';
				}

				if(UserPermission::check('web_attendance_permissions', 'd')) {
					$button .= '
						<a class="dropdown-item delete" href="javascript:void(0);" data-href="'.route('web_attendance_permissions.destroy', $data->id).'" title="Hapus Setting">
							<i class="mdi mdi-trash-can"></i> Hapus
						</a>';
				}

				if(!UserPermission::check('web_attendance_permissions', 'u') && !UserPermission::check('web_attendance_permissions', 'd')) {
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
			->rawColumns([ 'locations', 'action' ])
			->make(true);
	}


	public static function createWebAttendancePermissions($request)
	{
		$idEmployees = $request->id_employees;
		$idLocations = $request->id_locations;
		$permissions = [];

		foreach($idEmployees as $idEmployee)
		{
			$permissions[] = self::createOrUpdateWebAttendancePermission([
				'id_employee'	=> $idEmployee,
				'valid_until'	=> $request->valid_until,
				'locations'		=> $idLocations,
			]);
		}

		return $permissions;
	}


	public static function createOrUpdateWebAttendancePermission($data)
	{
		$permission = self::where('id_employee', $data['id_employee'])->first();

		if($permission) 
		{
			$permission->update([
				'valid_until'	=> $data['valid_until'],
			]);
			$permission->clearLocations();
		}
		else
		{
			$permission = self::create([
				'id_employee'	=> $data['id_employee'],
				'valid_until'	=> $data['valid_until']
			]);
		}

		if(!empty($data['locations']))
		{
			foreach($data['locations'] as $location)
			{
				$permission->addLocationID($location);
			}
		}

		return $permission;
	}


	public function deleteWebAttendancePermission()
	{
		return $this->delete();
	}
}
