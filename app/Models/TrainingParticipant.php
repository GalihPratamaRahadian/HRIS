<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use function GuzzleHttp\Promise\all;

class TrainingParticipant extends Model
{
	protected $fillable = [ 'id_training', 'id_employee', 'photo', 'status' ];


	/**
	 * 	Relationship methods
	 * */
	public function training()
	{
		return $this->belongsTo('App\Models\Training', 'id_training');
	}

	public function employee()
	{
		return $this->belongsTo('App\Models\Employee', 'id_employee');
	}

    public function trainingMaterial()
    {
        return $this->belongsTo('App\Models\TrainingMaterial', 'id_training');
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
        return $this->employee && $this->employee->department ? $this->employee->department->department_name : '';
    }

    public function employeeGroupName()
    {
         return $this->employee && $this->employee->employeGroup ? $this->employee->employeeGroup->group_name : '';
    }

    public function trainingMaterialName()
    {
        return $this->trainingMaterial ? $this->trainingMaterial->title : '-';
    }

    public function photoDateText($format = 'd/m/Y')
    {
        if (!$this->participantIsHasPhoto() || empty($this->photo_date)) {
            return '';
        }

        return date($format, strtotime($this->photo_date));
    }

    public function photoClockText($format = 'H:i:s')
    {
        if (!$this->participantIsHasPhoto() || empty($this->photo_clock)) {
            return '';
        }

        return date($format, strtotime($this->photo_clock));
    }

    public function startDateTrainingText($format = 'd/m/Y')
    {
        return $this->training && $this->training->start_date
            ? \Carbon\Carbon::parse($this->training->start_date)->format($format)
            : null;
    }


    public function endDateTrainingText($format = 'd/m/Y')
    {
        return $this->training && $this->training->end_date
            ? \Carbon\Carbon::parse($this->training->end_date)->format($format)
            : null;
    }

    public function trainerName()
    {
        return $this->training ? $this->training->trainer_name : '-';
    }

    public function photoPath()
	{
		return storage_path('app/public/training_participant/'.$this->photo);
	}

	public function photoLink()
	{
		return url('storage/training_participant/'.$this->photo);
	}

    public function trainingParticipantPhotoHtml()
    {
        if($this->participantIsHasPhoto()) {
            return '<img src="'.$this->photoLink().'?'.rand(100000000,999999999).'" width="100px" class="img-fluid">';
        }

        return $this;
    }

    public function participantIsHasPhoto()
    {
        return !empty($this->photo) && \File::exists($this->photoPath());
    }


    public function createPhotoTrainingParticipant($request)
    {
        $photoBlob = base64_decode(explode(',', $request->blobImage)[1]);
		$tempFilename = date('Ymd_His_').rand(100,999).'.jpeg';
		$tempPath = \Setting::temps($tempFilename);
        // dd($tempPath);
		\File::put($tempPath, $photoBlob);
		$employee = employee();

		\File::copy($tempPath, storage_path('app/public/training_participant/'.$tempFilename));

		\DB::beginTransaction();

       // Ambil data peserta berdasarkan employee_id dan training_id
        $participant = \App\Models\TrainingParticipant::where('id_employee', $employee->id)
            ->where('id_training', $request->id_training)
            ->first();

        if (!$participant) {
            \DB::rollBack();
            \File::delete($tempPath);

            return \Res::invalid([
                'message' => 'Data peserta training tidak ditemukan.',
            ]);
        }
        $participant->photo = $tempFilename;
        $participant->photo_clock = date('H:i:s');
        $participant->photo_date = date('Y-m-d');
        $participant->save();
		\DB::commit();

		\File::delete($tempPath);

		return \Res::success([
			'message'	=> 'Berhasil melakukan foto training',
		]);
    }

    public static function exportToExcel($request)
    {
        $trainings = self::select('training_participants.*')
            ->with([
                'employee.department',
                'employee.position',
                'employee.employeeGroup',
                'training',
                'trainingMaterial'
            ])
            ->leftJoin('employees', 'training_participants.id_employee', '=', 'employees.id')
            ->leftJoin('trainings', 'training_participants.id_training', '=', 'trainings.id')
            ->leftJoin('departments', 'employees.id_department', '=', 'departments.id')
            ->leftJoin('positions', 'employees.id_position', '=', 'positions.id')
            ->leftJoin('employee_groups', 'employees.id_employee_group', '=', 'employee_groups.id');

        // Filter Training
        if (!empty($request->id_training)) {
            $trainings->where('training_participants.id_training', $request->id_training);
        }


        // Filter Departemen
        if (!empty($request->id_department)) {
            $departmentID = $request->id_department;

            if ($departmentID === 'no') {
                $trainings->whereNull('employees.id_department');
            } else {
                $trainings->where('employees.id_department', $departmentID);
            }
        }

        // Filter Grup Karyawan
        if (!empty($request->id_employee_group)) {
            $groupID = $request->id_employee_group;

            if ($groupID !== 'all') {
                if ($groupID === 'no') {
                    $trainings->whereNull('employees.id_employee_group');
                } else {
                    $trainings->where('employees.id_employee_group', $groupID);
                }
            }
        }

        $trainings = $trainings->get();

        $headStyle = [
            'font-style'   => 'bold',
            'halign'       => 'center',
            'border'       => 'left,right,top,bottom',
            'border-color' => '#000',
            'border-style' => 'thin',
            'wrap_text'    => true,
            'valign'       => 'top'
        ];

        $bodyStyle = [
            'border'       => 'left,right,top,bottom',
            'border-color' => '#000',
            'border-style' => 'thin',
            'valign'       => 'top'
        ];

        $writer = new \App\MyClass\XLSXWriter();

        // Judul
        $writer->writeSheetHeader('Sheet1', [
            'Data Training Karyawan' => 'string',
            '' => 'string',
            '' => 'string'
        ], [
            'font-style' => 'bold',
            'halign'     => 'center',
            'font-size'  => 16,
            'widths'     => [5, 25, 25, 25, 20, 15, 15, 15, 15, 20, 15]
        ]);

        $writer->markMergedCell('Sheet1', 0, 0, 0, 10);
        $writer->writeSheetRow('Sheet1', ['']); // Spasi
        $writer->writeSheetRow('Sheet1', [
            'No',
            'Nama Karyawan',
            'Departemen',
            'Jabatan',
            'Grup Karyawan',
            'Tanggal Mulai Training',
            'Tanggal Selesai Training',
            'Tanggal Foto',
            'Waktu Foto',
            'Materi Training',
            'Mentor',
        ], $headStyle);

        $i = 1;
        foreach ($trainings as $training) {
            $writer->writeSheetRow('Sheet1', [
                $i++,
                $training->employeeName(),
                $training->departmentName(),
                optional($training->employee)->positionName() ?? '-',
                $training->employeeGroupName(),
                $training->startDateTrainingText('d/m/Y') ?? '',
                $training->endDateTrainingText('d/m/Y') ?? '',
                $training->photoDateText('d/m/Y') ?? '',
                $training->photoClockText('H:i:s') ?? '',
                optional($training)->trainingMaterialName() ?? '-',
                optional($training)->trainerName() ?? '-',
            ], $bodyStyle);
        }

        $filename = now()->format('YmdHis') . '_Rekap_Training_Karyawan.xlsx';
        $path = \Setting::temps($filename);
        $writer->writeToFile($path);

        return $path;
    }

}
