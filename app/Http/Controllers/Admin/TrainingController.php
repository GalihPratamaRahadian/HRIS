<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\Training;
use App\Models\TrainingParticipant;
use App\MyClass\Validations;
use Carbon\Carbon;
use DB;

class TrainingController extends Controller
{
	public function index(Request $request)
	{
		if($request->ajax()) {

			return Training::dataTable($request);
		}

		return view('admin.training.index', [
			'title'			=> 'Program Training',
			'breadcrumbs'	=> [
				[
					'title' => 'Program Training',
					'link'	=> route('admin.training')
				],
			]
		]);
	}


	public function create()
	{
		return view('admin.training.create', [
			'title'			=> 'Buat Program Training',
			'breadcrumbs'	=> [
				[
					'title' => 'Program Training',
					'link'	=> route('admin.training')
				],
				[
					'title' => 'Buat',
					'link'	=> route('admin.training.create')
				],
			]
		]);
	}


	public function store(Request $request)
    {
        Validations::validateTraining($request);

        $employeeIds = [];

        if ($request->target == 'selected') {
            $employeeIds = $request->id_employees;
        } else {
            $employees = Employee::getActiveEmployees();

            if ($request->id_department && $request->id_department != 'all') {
                $employees = $employees->where('id_department', $request->id_department);
            }
            if ($request->id_position && $request->id_position != 'all') {
                $employees = $employees->where('id_position', $request->id_position);
            }
            if ($request->id_employee_group && $request->id_employee_group != 'all') {
                $employees = $employees->where('id_employee_group', $request->id_employee_group);
            }

            $employeeIds = $employees->pluck('id')->toArray();
        }

        if (empty($employeeIds)) {
            return \Res::invalid([
                'message' => 'Tidak ada karyawan yang dipilih.',
                'errors' => [
                    'id_employees' => 'Pilih minimal satu karyawan.',
                ]
            ]);
        }

        DB::beginTransaction();

        try {
            $training = Training::create([
                'title'             => $request->title,
                'trainer_name'      => $request->trainer_name,
                'start_date'        => $request->start_date,
                'end_date'          => $request->end_date,
                'is_published'      => $request->is_published,
                'id_department'     => $request->id_department != 'all' ? $request->id_department : null,
                'id_position'       => $request->id_position != 'all' ? $request->id_position : null,
                'id_employee_group' => $request->id_employee_group != 'all' ? $request->id_employee_group : null,
            ]);

           $training->saveTrainingMaterials($request, $employeeIds);

            foreach ($employeeIds as $employeeId) {
                TrainingParticipant::create([
                    'id_training' => $training->id,
                    'id_employee' => $employeeId,
                ]);
            }

            DB::commit();
            return \Res::save();

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }



	public function detail(Training $training)
	{
		$training->load('trainingMaterials');
		$training->load('trainingParticipants.employee.department');

		return view('admin.training.detail', [
			'title'			=> 'Detail Program Training',
			'training'		=> $training,
			'breadcrumbs'	=> [
				[
					'title' => 'Program Training',
					'link'	=> route('admin.training')
				],
				[
					'title' => 'Detail',
					'link'	=> route('admin.training.detail', $training->id)
				],
			]
		]);
	}


	public function edit(Training $training)
	{
		return view('admin.training.edit', [
			'title'			=> 'Edit Program Training',
			'training'		=> $training,
			'breadcrumbs'	=> [
				[
					'title' => 'Program Training',
					'link'	=> route('admin.training')
				],
				[
					'title' => 'Edit',
					'link'	=> route('admin.training.edit', $training->id)
				],
			]
		]);
	}


	public function update(Request $request, Training $training)
	{
		Validations::validateTraining($request);
		DB::beginTransaction();

		try {
			$training->updateTraining($request);
			DB::commit();

			return \Res::update();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	public function destroy(Training $training)
	{
		DB::beginTransaction();

		try {
			$training->deleteTraining();
			DB::commit();

			return \Res::delete();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

    public function trainingParticipantExport(Request $request)
	{
        try{
            $path = \App\Models\TrainingParticipant::exportToExcel($request);

			return response()->download($path)->deleteFileAfterSend();
        }catch (\Exception $e) {
            return \Res::error($e);
        }
	}
}
