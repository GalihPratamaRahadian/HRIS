<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalarySlip extends Model
{
	protected $fillable = [ 'title', 'id_employee', 'month', 'month_name', 'year', 'total', 'filename' ];

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
	public static function createSalarySlipMultiple($request)
	{
		$title = $request->title;
		$year = $request->year;
		$month = $request->month;
		$idEmployees = $request->id_employee;
		$totals = $request->total;
		$files = $request->file;
		$index = 0;

		foreach($idEmployees as $employeeId) {
			\DB::beginTransaction();
			$salarySlip = self::create([
				'title'			=> $title,
				'id_employee'	=> $employeeId,
				'month'			=> $month,
				'month_name'	=> \App\MyClass\Date::monthName($month),
				'year'			=> $year,
				'total'			=> $totals[$index],
			]);
			$salarySlip->saveFile($request->file('file')[$index]);
			\DB::commit();
			$index++;
		}
	}

	public function updateSalarySlip($request)
	{
		$this->update([
			'title'			=> $request->title,
			'id_employee'	=> $request->id_employee,
			'month'			=> $request->month,
			'month_name'	=> \App\MyClass\Date::monthName($request->month),
			'year'			=> $request->year,
			'total'			=> $request->total,
		]);
		
		if(!empty($request->file)) {
			$this->saveFile($request->file('file'));
		}

		return $this;
	}

	public function deleteSalarySlip()
	{
		$this->removeFile();
		return $this->delete();
	}


	/**
	 * 	Helper methods
	 * */
	public function employeeName()
	{
		return $this->employee->employee_name ?? '-';
	}

	public function departmentName()
	{
		return $this->employee->department->department_name ?? '-';
	}

	public function saveFile($file)
	{
		if(!empty($file)) {
			$this->removeFile();
			$filename = date('Ymdhis_').$this->id_employee.'.'.$file->getClientOriginalExtension();
			$file->move(storage_path('app/public/salary_slip/'), $filename);
			$this->update([
				'filename' => $filename,
			]);
		}

		return $this;
	}

	public function filePath()
	{
		return storage_path('app/public/salary_slip/'.$this->filename);
	}

	public function fileLink()
	{
		return url('storage/salary_slip/'.$this->filename);
	}

	public function fileLinkHtml()
	{
		$html = '<a href="'.$this->fileLink().'" target="_blank"> Klik Disini </a>';
		return $html;
	}

	public function isHasFile()
	{
		if(empty($this->filename)) return false;
		return \File::exists($this->filePath());
	}

	public function removeFile()
	{
		if($this->isHasFile()) {
			\File::delete($this->filePath());
			$this->update([
				'filename' => null
			]);
		}

		return $this;
	}

	public function totalFormatted()
	{
		if(empty($this->total)) return 'Rp. -';
		return 'Rp. '.number_format($this->total);
	}


	/**
	 * 	Static methods
	 * */
	public static function dt()
	{
		$data = self::select([ 'salary_slips.*' ])
					->leftJoin('employees', 'salary_slips.id_employee', '=', 'employees.id')
					->leftJoin('departments', 'employees.id_department', '=', 'departments.id')
					->with([ 'employee.department', 'employee.position' ]);

		if(auth()->user()->isEmployee()) {
			$employee = employee();
			$data = $data->where('id_employee', $employee->id);
		}

		return \DataTables::eloquent($data)
			->editColumn('employees.employee_name', function($data){
				$text = $data->employeeName().'<br>';
				$text .= '<span class="text-primary">['.$data->departmentName().']</span>';
				return $text;
			})
			->editColumn('department_name', function($data){
				return $data->departmentName();
			})
			->editColumn('total', function($data){
				return $data->totalFormatted();
			})
			->editColumn('filename', function($data){
				return $data->fileLinkHtml();
			})
			->addColumn('action', function($data){
				$button = '
				<div class="dropdown">
					<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Aksi
					</button>
					<div class="dropdown-menu">';

				if(UserPermission::check('salary_slip', 'u')) {
					$button .= '
						<a class="dropdown-item" href="'.route('salary_slip.edit', $data->id).'" title="Edit Slip Gaji">
							<i class="mdi mdi-pencil"></i> Edit 
						</a>';
				}

				if(UserPermission::check('salary_slip', 'd')) {
					$button .= '
						<a class="dropdown-item delete" href="javascript:void(0);" data-href="'.route('salary_slip.destroy', $data->id).'" title="Hapus Slip Gaji">
							<i class="mdi mdi-trash-can"></i> Hapus
						</a>';
				}

				if(!UserPermission::check('salary_slip', 'u') && !UserPermission::check('salary_slip', 'd')) {
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
			->rawColumns([ 'employees.employee_name', 'total', 'filename', 'action' ])
			->make(true);
	}
}
