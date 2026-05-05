<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
	use SoftDeletes;
	
	protected $fillable = [ 'store_name', 'phone_number', 'address', 'latitude', 'longitude', 'registered_by', 'handled_by', 'last_visited_at', 'partner_status' ];


	/**
	 * 	Relationship methods
	 * */
	public function registeredBy()
	{
		return $this->belongsTo('App\Models\Employee', 'registered_by')->withTrashed();
	}

	public function handledBy()
	{
		return $this->belongsTo('App\Models\Employee', 'handled_by')->withTrashed();
	}


	/**
	 * 	CRUD methods
	 * */
	public static function createStore($request)
	{
		$employeeId = !empty($request->id_employee) ? $request->id_employee : employee()->id; 
		$store = self::create([
			'store_name'	=> $request->store_name,
			'phone_number'	=> $request->phone_number,
			'address'		=> $request->address,
			'latitude'		=> $request->latitude,
			'longitude'		=> $request->longitude,
			'registered_by'	=> $employeeId,
			'handled_by'	=> $employeeId,
			'partner_status' => 'active',
		]);

		try {
			$store->load('handledBy');
			$store->handledBy->salesEmployee->countStoreHandle();
		} catch (\Exception $e) {}

		return $store;
	}

	public function deleteStore()
	{
		$handledBy = $this->handledBy;
		$delete = $this->delete();
		try {
			$handledBy->salesEmployee->countStoreHandle();
		} catch (\Exception $e) {}

		return $delete;
	}


	/**
	 * 	Helper methods
	 * */
	public function setLastVisitedAt()
	{
		$this->update([
			'last_visited_at'	=> now()
		]);

		return $this;
	}

	public function handledByName()
	{
		return $this->handledBy->employee_name ?? '-';
	}

	public function registeredByName()
	{
		return $this->registeredBy->employee_name ?? '-';
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
		$visit = StoreVisit::where('id_employee', $this->handled_by)
						   ->where('id_store', $this->id)
						   ->where('visited_at', '>=', now()->format('Y-m-d').' 00:00:00')
						   ->where('visited_at', '<=', now()->format('Y-m-d').' 23:59:59')
						   ->first();
		return !empty($visit);
	}


	public function isLocationValid($latitude, $longitude)
	{
		$distanceInMeters = $this->distanceInMeters($latitude, $longitude);
		return $distanceInMeters <= 40;
	}

	public function lastVisitedAtText($format = 'd M Y H:i')
	{
		if(empty($this->last_visited_at)) return '-';
		return date($format, strtotime($this->last_visited_at));
	}

	public function isPartnerStatusActive()
	{
		return $this->partner_status == 'active';
	}

	public function partnerStatusHtml()
	{
		if($this->isPartnerStatusActive()) {
			return '<span class="text text-success"> Aktif </span>';
		} else {
			return '<span class="text text-danger"> Nonaktif </span>';
		}
	}

	public function getLocation()
	{
		return \App\MyClass\Location::make($this->latitude, $this->longitude);
	}

	public function setActivePartnerStatus()
	{
		$this->update([
			'partner_status'	=> 'active',
		]);
		return $this;
	}

	public function setInactivePartnerStatus()
	{
		$this->update([
			'partner_status'	=> 'inactive',
		]);
		return $this;
	}

	public function fetchData()
	{
		return (object) [
			'id'			=> $this->id,
			'store_name'	=> $this->store_name,
			'address'		=> $this->address,
			'latitude'		=> $this->latitude,
			'longitude'		=> $this->longitude,
		];
	}


	public static function dt($request)
	{
		$data = self::select([ 'stores.*', 'employees.employee_name as handled_by_name' ])
					->with([ 'handledBy' ])
					->leftJoin('employees', 'stores.handled_by', '=', 'employees.id');

		if(!empty($request->handled_by) && $request->handled_by != 'all') {
			$data = $data->where('handled_by', $request->handled_by);
		}

		if(!empty($request->partner_status) && $request->partner_status != 'all') {
			$data = $data->where('partner_status', $request->partner_status);
		}

		return \DataTables::eloquent($data)
			->editColumn('handle_by.employee_name', function($data){
				return '<a href="'.route('employee.detail', $data->handled_by).'">'.$data->handledByName().'</a>';
			})
			->editColumn('last_visited_at', function($data){
				return $data->lastVisitedAtText();
			})
			->editColumn('partner_status', function($data){
				return $data->partnerStatusHtml();
			})
			->addColumn('action', function($data){

				$button = '
				<div class="dropdown">
					<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Aksi
					</button>
					<div class="dropdown-menu">';

				if($data->isPartnerStatusActive()) {
					$button .= '
						<a class="dropdown-item set-inactive" href="javascript:void(0);" data-href="'.route('store.set_inactive', $data->id).'" title="Nonaktifkan Mitra Toko">
							<i class="mdi mdi-close"></i> Nonaktifkan Mitra
						</a>';
				} else {
					$button .= '
						<a class="dropdown-item set-active" href="javascript:void(0);" data-href="'.route('store.set_active', $data->id).'" title="Aktifkan Mitra Toko">
							<i class="mdi mdi-check"></i> Aktifkan Mitra
						</a>';
				}

				$button .= '
						<a class="dropdown-item" href="'.route('store.detail', $data->id).'" title="Detail Toko">
							<i class="mdi mdi-magnify"></i> Detail
						</a>
						<a class="dropdown-item delete" href="javascript:void(0);" data-href="'.route('store.destroy', $data->id).'" title="Hapus Mitra Toko">
							<i class="mdi mdi-trash-can"></i> Hapus
						</a>
					</div>
				</div>';

				return $button;
			})
			->rawColumns([ 'handle_by.employee_name', 'partner_status', 'action' ])
			->make(true);
	}
}
