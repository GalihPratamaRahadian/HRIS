<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Training;
use App\Models\TrainingParticipant;

class TrainingController extends Controller
{
	public function index(Request $request)
	{
		if($request->ajax()) {
			return Training::dataTable($request);
		}

		return view('employee.training.index', [
			'title'			=> 'Program Training',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Program Training',
					'link'	=> route('employee.training')
				],
			]
		]);
	}

	public function learn(Training $training)
	{
		if(!$training->isEmployeeAllorForTraining(employee())) abort(404);
		if(!$training->isPublished()) abort(404);

		$participant = TrainingParticipant::where('id_training', $training->id)
										  ->where('id_employee', employee()->id)
										  ->first();

		if(!$participant) {
			$participant = TrainingParticipant::create([
				'id_training'	=> $training->id,
				'id_employee'	=> employee()->id
			]);
		}

		return view('employee.training.learn', [
			'title'			=> 'Pelajari '.$training->title,
			'training'		=> $training,
			'breadcrumbs'	=> [
				[
					'title'	=> 'Program Training',
					'link'	=> route('employee.training')
				],
				[
					'title'	=> 'Pelajari '.$training->title,
					'link'	=> route('employee.training.learn', $training->id)
				],
			]
		]);
	}

    public function takePhoto(TrainingParticipant $trainingParticipant)
    {
        return view('employee.training.take_photo', [
            'title'			            => 'Ambil Foto',
            'trainingParticipant'		=> $trainingParticipant,
            'breadcrumbs'	            => [
                [
                    'title'	            => 'Program Training',
                    'link'	            => route('employee.training')
                ],
                [
                    'title'	            => 'Ambil Foto',
                    'link'	            => route('employee.training.take_photo', $trainingParticipant->id)
                ],
            ]
        ]);
    }

    public function takePhotoSave(Request $request, TrainingParticipant $trainingParticipant)
    {
        $request->validate([
            'blobImage' => 'required',
        ]);

        try {

            $trainingParticipant->createPhotoTrainingParticipant($request);

            return \Res::success();
        } catch (\Exception $e) {
            return \Res::error($e);
        }
    }
}
