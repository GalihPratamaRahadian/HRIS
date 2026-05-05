<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeTraining extends Model
{
	protected $fillable = [ 'id_employee', 'training_name', 'date_start', 'date_end', 'provider', 'file', 'description', 'id_course_participant' ];


	/**
	 * 	Relationship
	 * */
	public function employee()
	{
		return $this->belongsTo('App\Models\Employee', 'id_employee');
	}

	public function employeeName()
	{
		return $this->employee ? $this->employee->employee_name : '-';
	}


	/**
	 * 	CRUD methods
	 * */
	public static function createEmployeeTraining($request, $employee)
	{
		\DB::beginTransaction();
		$data = $request->all();
		$data['id_employee'] = $employee->id;
		$training = self::create($data);
		\DB::commit();
		$training->saveFile($request);
		return $training;
	}

	public function updateEmployeeTraining($request, $employee)
	{
		\DB::beginTransaction();
		$data = $request->all();
		$data['id_employee'] = $employee->id;
		$this->update($data);
		\DB::commit();
		$this->saveFile($request);
		return $this;
	}

	public function deleteEmployeeTraining()
	{
		$this->removeFile();
		return $this->delete();
	}


	/**
	 * 	Helper methods
	 * */
	public function dateOfBirthText($format = 'd M Y')
	{
		return date($format, strtotime($this->date_of_birth));
	}

	public function saveFile($request)
	{
		if(!empty($request->file_training))
		{
			$this->removeFile();
			$file = $request->file('file_training');
			$filename = \Str::random(16).'.'.$file->getClientOriginalExtension();
			$file->move(storage_path('app/public/employee_training'), $filename);
			$this->update([
				'file'	=> $filename
			]);
		}

		return $this;
	}

	public function removeFile()
	{
		if($this->isHasFile()) {
			\File::delete($this->filePath());
			$this->update([
				'file' => null
			]);
		}

		return $this;
	}

	public function filePath()
	{
		return storage_path('app/public/employee_training/'.$this->file);
	}

	public function fileLink()
	{
		return url('storage/employee_training/'.$this->file);
	}

	public function isHasFile()
	{
		if(empty($this->file)) return false;
		return \File::exists($this->filePath());
	}


	/**
	 * 	Static methods
	 * */
	public static function dataTable($request, $employee)
	{
		$data = self::where('id_employee', $employee->id);

		return \DataTables::eloquent($data)
			->editColumn('description', function($data){
				return $data->description ?? '-';
			})
			->editColumn('file', function($data){
				$html = '<a href="'.$data->fileLink().'" target="_blank"> Klik Disini <a>';
				return $html;
			})
			->addColumn('action', function($data){
				$button = '
				<div class="dropdown">
					<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Aksi
					</button>
					<div class="dropdown-menu">
						<a class="dropdown-item" href="'.route('employee_training.edit', [$data->id_employee, $data->id]).'" title="Edit Pelatihan">
							<i class="mdi mdi-pencil"></i> Edit 
						</a>
						<a class="dropdown-item delete" href="javascript:void(0);" data-href="'.route('employee_training.destroy', [$data->id_employee, $data->id]).'" title="Hapus Pelatihan">
							<i class="mdi mdi-trash-can"></i> Hapus
						</a>
					</div>
				</div>';

				return $button;
			})
			->rawColumns([ 'file', 'action' ])
			->make(true);
	}
}
