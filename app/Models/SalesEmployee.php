<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesEmployee extends Model
{
	protected $fillable = [ 'id_employee', 'amount_of_stores' ];


	/**
	 * 	Relationship methods
	 * */
	public function employee()
	{
		return $this->belongsTo('App\Models\Employee', 'id_employee')->withTrashed();
	}


	/**
	 * 	CRUD methods
	 * */
	public static function createSalesEmployee($request)
	{
		$employeeIds = $request->id_employees;

		foreach($employeeIds as $employeeId) {
			$salesEmployee = self::where('id_employee', $employeeId)->first();
			if(!$salesEmployee) {
				self::create([
					'id_employee' => $employeeId
				]);
			}
		}

		return true;
	}

	public function deleteSalesEmployee()
	{
		$this->removeStoreHandle();
		return $this->delete();
	}


	/**
	 * 	Helper methods
	 * */
	public function employeeName()
	{
		return $this->employee->employee_name ?? '-';
	}
	
	public function countStoreHandle()
	{
		$this->update([
			'amount_of_stores' => Store::where('handled_by', $this->id_employee)->count(),
		]);

		return $this;
	}

	public function removeStoreHandle()
	{
		Store::where('handled_by', $this->id_employee)
		->update([
			'handled_by'	=> null,
		]);

		$this->countStoreHandle();

		return $this;
	}


	public static function dt($request)
	{
		$data = self::select([ 'sales_employees.*' ])
					->has('employee')
					->where('status', Employee::STATUS_ACTIVE)
					->with([ 'employee.department', 'employee.position' ])
					->leftJoin('employees', 'sales_employees.id_employee', '=', 'employees.id')
					->leftJoin('departments', 'employees.id_department', '=', 'departments.id')
					->leftJoin('positions', 'employees.id_position', '=', 'positions.id');

		
		return \DataTables::eloquent($data)
			->editColumn('employee.employee_number', function($data){
				try {
					return $data->employee->employee_number ?? '-';
				} catch (\Exception $e) {
					return '-';
				}
			})
			->editColumn('employee.department.department_name', function($data){
				try {
					return $data->employee->departmentName().'<br> ['.$data->employee->positionName().']';
				} catch (\Exception $e) {
					return '-';
				}
			})
			->addColumn('action', function($data){

				$button = '
				<div class="dropdown">
					<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Aksi
					</button>
					<div class="dropdown-menu">
						<a class="dropdown-item" href="'.route('sales_visit.detail', $data->id).'" title="Edit Login Pengguna">
							<i class="mdi mdi-map-marker-multiple"></i> Lihat Kunjungan Hari Ini
						</a>
						<a class="dropdown-item delete" href="javascript:void(0);"  data-href="'.route('sales_employee.destroy', $data->id).'" title="Hapus Sales">
							<i class="mdi mdi-trash-can"></i> Hapus
						</a>
					</div>
				</div>';

				return $button;
			})
			->rawColumns([ 'employee.department.department_name', 'action' ])
			->make(true);
	}


	public static function salesVisitDt($request)
	{
		$data = self::select([ 'sales_employees.*' ])
					->has('employee')
					->with([ 'employee' ])
					->where('employees.status', Employee::STATUS_ACTIVE)
					->leftJoin('employees', 'sales_employees.id_employee', '=', 'employees.id');

		$date = $request->date;
		if(empty($date)) $date = date('Y-m-d');

		
		return \DataTables::eloquent($data)
			->editColumn('employee.employee_number', function($data){
				try {
					return $data->employee->employee_number ?? '-';
				} catch (\Exception $e) {
					return '-';
				}
			})
			->addColumn('amount_of_visited_stores', function($data) use($date){
				$amount = StoreVisit::where('id_employee', $data->id_employee)
									->where('visited_at', '>=', $date.' 00:00:00')
									->where('visited_at', '<=', $date.' 23:59:59')
									->count();
				return $amount;
			})
			->addColumn('action', function($data) use ($date){

				$button = '
				<div class="dropdown">
					<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Aksi
					</button>
					<div class="dropdown-menu">
						<a class="dropdown-item" href="'.route('sales_visit.detail', $data->id).'?date='.$date.'" title="Lihat Kunjungan">
							<i class="mdi mdi-map-marker-multiple"></i> Lihat Kunjungan
						</a>
					</div>
				</div>';

				return $button;
			})
			->rawColumns([ 'action' ])
			->make(true);
	}

}
