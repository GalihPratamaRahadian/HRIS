<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreVisit extends Model
{
	protected $fillable = [ 'id_employee', 'id_store', 'latitude', 'longitude', 'photo', 'visited_at', 'purchase' ];


	/**
	 * 	Relationship methods
	 * */
	public function employee()
	{
		return $this->belongsTo('App\Models\Employee', 'id_employee')->withTrashed();
	}

	public function store()
	{
		return $this->belongsTo('App\Models\Store', 'id_store')->withTrashed();
	}


	/**
	 * 	CRUD methods
	 * */
	public static function createStoreVisit($request)
	{
		$storeVisit = self::create([
			'id_employee'	=> employee()->id,
			'id_store'		=> $request->id_store,
			'latitude'		=> $request->latitude,
			'longitude'		=> $request->longitude,
			'visited_at'	=> now(),
			'purchase'		=> $request->purchase,
		]);

		$storeVisit->setPhoto($request);
		return $storeVisit;
	}


	/**
	 * 	Helper methods
	 * */
	public function setPhoto($request)
	{
		$explodeBase64 = explode(",", $request->photo);
		$photo = count($explodeBase64) > 1 ? $explodeBase64[1] : $explodeBase64[0];
		$photo = base64_decode($photo);
		$filename = date('YmdHis').".jpeg";
		$img = imagecreatefromstring($photo);
		
		if($img !== false) {
			header('Content-Type: image/jpeg');
			imagejpeg($img, storage_path('app/public/store_visit/'.$filename));

			$this->update([
				'photo'	=> $filename,
			]);
		}

		return $this;
	}

	public function employeeName()
	{
		return $this->employee->employee_name ?? '-';
	}

	public function storeName()
	{
		return $this->store->store_name ?? '-';
	}

	public function purchaseText()
	{
		return $this->purchase == 'yes'? 'Ya' : 'Tidak';
	}

	public function visitedAtText($format = 'd M Y H:i')
	{
		return date($format, strtotime($this->visited_at));
	}

	public function getLocation()
	{
		return \App\MyClass\Location::make($this->latitude, $this->longitude);
	}

	public function photoPath()
	{
		return storage_path('app/public/store_visit/'.$this->photo);
	}

	public function photoLink()
	{
		if($this->isHasPhoto()) {
			return url('storage/store_visit/'.$this->photo);
		}

		return url('images/no-image.jpg');
	}

	public function isHasPhoto()
	{
		if(empty($this->photo)) return false;
		return \File::exists($this->photoPath());
	}

	public function removePhoto()
	{
		if($this->isHasPhoto()) {
			\File::delete($this->photoPath());
			$this->update([
				'photo'	=> null,
			]);
		}

		return $this;
	}

	public function fetchData()
	{
		return (object) [
			'id'				=> $this->id,
			'store_name'		=> $this->storeName(),
			'employee_name'		=> $this->employeeName(),
			'visit_latitude'	=> $this->latitude,
			'visit_longitude'	=> $this->longitude,
			'store_latitude'	=> $this->store->latitude ?? null,
			'store_longitude'	=> $this->store->longitude ?? null,
			'photo_link'		=> $this->photoLink(),
			'purchase'			=> $this->purchaseText(),
			'visited_at'		=> $this->visitedAtText(),
		];
	}


	public static function dt($request)
	{
		$data = self::select([ 'store_visits.*' ])
					->with([ 'employee', 'store' ])
					->leftJoin('employees', 'store_visits.id_employee', '=', 'employees.id')
					->leftJoin('stores', 'store_visits.id_store', '=', 'stores.id');

		if(!empty($request->start_date)) {
			$data = $data->where('visited_at', '>=', $request->start_date.' 00:00:00');
		}

		if(!empty($request->end_date)) {
			$data = $data->where('visited_at', '<=', $request->end_date.' 23:59:59');
		}

		if(!empty($request->id_employee) && $request->id_employee != 'all') {
			$data = $data->where('id_employee', $request->id_employee);
		}

		if(!empty($request->id_store) && $request->id_store != 'all') {
			$data = $data->where('id_store', $request->id_store);
		}

		return \DataTables::eloquent($data)
			->editColumn('visited_at', function($data){
				return $data->visitedAtText();
			})
			->editColumn('employee.employee_name', function($data){
				return '<a href="'.route('employee.detail', $data->id_employee).'">'.$data->employeeName().'</a>';
			})
			->editColumn('store.store_name', function($data){
				return '<a href="'.route('store.detail', $data->id_employee).'">'.$data->storeName().'</a>';
			})
			->editColumn('purchase', function($data){
				return $data->purchaseText();
			})
			->addColumn('action', function($data){

				$button = '
				<a class="btn btn-primary py-2" href="'.route('store_visit.detail', $data->id).'" title="Detail Kunjungan">
					<i class="mdi mdi-magnify"></i> Detail
				</a>';

				return $button;
			})
			->rawColumns([ 'employee.employee_name', 'store.store_name', 'action' ])
			->make(true);
	}
}
