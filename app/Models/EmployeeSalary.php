<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeSalary extends Model
{
	protected $fillable = [ 'id_employee', 'basic_salary', 'overtime_pay', 'daily_meal_allowance', 'daily_transportation_allowance', 'total_allowance', 'total_cut' ];


	public function employee()
	{
		return $this->belongsTo('App\Models\Employee', 'id_employee');
	}


	public function employeeSalaryAllowances()
	{
		return $this->hasMany('App\Models\EmployeeSalaryAllowance', 'id_employee_salary');
	}


	public function employeeSalaryCuts()
	{
		return $this->hasMany('App\Models\EmployeeSalaryCut', 'id_employee_salary');
	}


	public static function createEmployeeSalary($request)
	{
		$employeeSalary = self::create([
			'id_employee'	=> $request->id_employee,
			'basic_salary'	=> $request->basic_salary,
			'overtime_pay'	=> $request->overtime_pay,
			'daily_meal_allowance'	=> $request->daily_meal_allowance ?? 0,
			'daily_transportation_allowance'	=> $request->daily_transportation_allowance ?? 0,
		]);

		$employeeSalary->createAllowances($request);
		$employeeSalary->createCuts($request);

		return $employeeSalary;
	}


	public function updateEmployeeSalary($request)
	{
		$this->update([
			'basic_salary'	=> $request->basic_salary,
			'overtime_pay'	=> $request->overtime_pay,
			'daily_meal_allowance'	=> $request->daily_meal_allowance ?? 0,
			'daily_transportation_allowance'	=> $request->daily_transportation_allowance ?? 0,
		]);

		$this->emptyingAllowances();
		$this->emptyingCuts();
		$this->createAllowances($request);
		$this->createCuts($request);

		return $this;
	}


	public function createAllowances($request)
	{
		$iteration 			= 0;
		$total 				= 0;

		if(!empty($request->allowance_name) && !empty($request->allowance_nominal))
		{
			$allowanceNames 	= $request->allowance_name;
			$allowanceNominals 	= $request->allowance_nominal;
			
			foreach($allowanceNames as $allowanceName) {
				EmployeeSalaryAllowance::create([
					'id_employee_salary'	=> $this->id,
					'allowance_name'		=> $allowanceName,
					'allowance_nominal'		=> $allowanceNominals[$iteration],
				]);

				$total += (int) $allowanceNominals[$iteration];

				$iteration++;
			}
		}

		$this->update([
			'total_allowance'	=> $total,
		]);

		return $this;
	}


	public function createCuts($request)
	{
		$iteration 		= 0;
		$total			= 0;

		if(!empty($request->cut_name) && !empty($request->cut_nominal))
		{
			$cutNames 		= $request->cut_name;
			$cutNominals 	= $request->cut_nominal;

			foreach($cutNames as $cutName) {
				EmployeeSalaryCut::create([
					'id_employee_salary'	=> $this->id,
					'cut_name'				=> $cutName,
					'cut_nominal'			=> $cutNominals[$iteration],
				]);

				$total += (int) $cutNominals[$iteration];

				$iteration++;
			}
		}

		$this->update([
			'total_cut'	=> $total,
		]);

		return $this;
	}


	public static function dt()
	{
		$data = self::select([ 'employee_salaries.*' ])
					->has('employee')
					->with('employee')
					->join('employees', 'employee_salaries.id_employee', '=', 'employees.id');

		return \DataTables::eloquent($data)
			->addColumn('employee.employee_name', function($data){
				return '<a href="'. route('employee.detail', $data->id_employee) .'">'. $data->employeeName() .'</a>';
			})
			->editColumn('basic_salary', function($data){
				return $data->basicSalaryText();
			})
			->editColumn('overtime_pay', function($data){
				return $data->overtimePayText();
			})
			->editColumn('total_allowance', function($data){
				return $data->totalAllowanceText();
			})
			->editColumn('total_cut', function($data){
				return $data->totalCutText();
			})
			->addColumn('action', function($data){
				$button = '
				<div class="dropdown">
					<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Aksi
					</button>
					<div class="dropdown-menu">
						<a class="dropdown-item" href="'.route('employee_salary.detail', $data->id).'" title="Detail Gaji">
							<i class="mdi mdi-magnify"></i> Detail 
						</a>';

				if(UserPermission::check('employee_salary', 'u')) {
					$button .= '
						<a class="dropdown-item" href="'.route('employee_salary.edit', $data->id).'" title="Edit Gaji">
							<i class="mdi mdi-pencil"></i> Edit 
						</a>';
				}

				if(UserPermission::check('employee_salary', 'd')) {
					$button .= '
						<a class="dropdown-item delete" href="javascript:void(0);" data-href="'.route('employee_salary.destroy', $data->id).'" title="Hapus Gaji">
							<i class="mdi mdi-trash-can"></i> Hapus
						</a>';
				}

				$button .= '
					</div>
				</div>';

				return $button;
			})
			->rawColumns([ 'employee.employee_name', 'action' ])
			->make(true);
	}


	public function basicSalaryText()
	{
		return 'Rp. '.number_format($this->basic_salary);
	}

	public function overtimePayText()
	{
		return 'Rp. '.number_format($this->overtime_pay);
	}

	public function dailyMealAllowanceText()
	{
		return 'Rp. '.number_format($this->daily_meal_allowance);
	}

	public function dailyTransportationAllowanceText()
	{
		return 'Rp. '.number_format($this->daily_transportation_allowance);
	}

	public function totalAllowanceText()
	{
		return 'Rp. '.number_format($this->total_allowance);
	}

	public function totalCutText()
	{
		return 'Rp. '.number_format($this->total_cut);
	}


	public function emptyingAllowances()
	{
		foreach($this->employeeSalaryAllowances as $allowance) {
			$allowance->delete();
		}

		$this->update([
			'total_allowance'	=> 0,
		]);

		return $this;
	}


	public function emptyingCuts()
	{
		foreach($this->employeeSalaryCuts as $cut) {
			$cut->delete();
		}

		$this->update([
			'total_cut'	=> 0,
		]);

		return $this;
	}


	public function employeeName()
	{
		return !empty($this->employee) ? $this->employee->employee_name : '-';
	}


	public function isHasAllowances()
	{
		return count($this->employeeSalaryAllowances) > 0 ? true : false;
	}


	public function isHasCuts()
	{
		return count($this->employeeSalaryCuts) > 0 ? true : false;
	}


	public function deleteEmployeeSalary()
	{
		if($this->isHasAllowances()) {
			foreach($this->employeeSalaryAllowances as $allowance) {
				$allowance->delete();
			}
		}

		if($this->isHasCuts()) {
			foreach($this->employeeSalaryCuts as $cut) {
				$cut->delete();
			}
		}

		return $this->delete();
	}
}
