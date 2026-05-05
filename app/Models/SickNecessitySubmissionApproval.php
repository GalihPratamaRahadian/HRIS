<?php

namespace App\Models;

use App\MyClass\Helper;
use App\MyClass\Whatsapp;
use App\MyClass\WhatsappNew;
use Illuminate\Database\Eloquent\Model;

class SickNecessitySubmissionApproval extends Model
{
    protected $fillable = [ 'id_sick_necessity_submission', 'level', 'id_approver_position', 'status', 'id_user', 'approved_at', 'rejected_at' ];


    const STATUS_WAIT       = 'wait';
    const STATUS_APPROVED   = 'approved';
    const STATUS_REJECTED   = 'rejected';
    const STATUS_CANCELED   = 'canceled';
    const STATUS_SKIP       = 'skip';


    /**
     *  Relationships
     * */
    public function sickNecessitySubmission()
    {
        return $this->belongsTo('App\Models\SickNecessitySubmission', 'id_sick_necessity_submission');
    }

    public function approverPosition()
    {
        return $this->belongsTo('App\Models\Position', 'id_approver_position');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'id_user');
    }



    /**
     *  Helper methods
     * */
    public function isStatusWaiting()
    {
        return $this->status == self::STATUS_WAIT;
    }

    public function isStatusApproved()
    {
        return $this->status == self::STATUS_APPROVED;
    }

    public function isStatusRejected()
    {
        return $this->status == self::STATUS_REJECTED;
    }

    public function isStatusCanceled()
    {
        return $this->status == self::STATUS_CANCELED;
    }

    public function isStatusSkip()
    {
        return $this->status == self::STATUS_SKIP;
    }

    public function createdAtText($format = 'd M Y H:i')
    {
        return date($format, strtotime($this->created_at));
    }

    public function statusText()
    {
        if($this->isStatusWaiting()) return 'Menunggu';
        if($this->isStatusApproved()) return 'Disetujui';
        if($this->isStatusRejected()) return 'Ditolak';
        if($this->isStatusCanceled()) return 'Dibatalkan';
        if($this->isStatusSkip()) return 'Dilewat';
        return '-';
    }

    public function statusHtml()
    {
        $text = $this->statusText();
        if($this->isStatusWaiting()) return '<span class="text-primary">'.$text.'</span>';
        if($this->isStatusApproved()) return '<span class="text-success">'.$text.'</span>';
        if($this->isStatusRejected() || $this->isStatusCanceled()) return '<span class="text-danger">'.$text.'</span>';
        if($this->isStatusSkip()) return '<span class="text-primary">'.$text.'</span>';
        return '-';
    }

    public function approverPositionName()
    {
        return $this->approverPosition ? $this->approverPosition->position_name : '-';
    }

    public function approverDepartmentName()
    {
        return $this->approverPosition ? $this->approverPosition->departmentName() : '-';
    }

    public function employeeName()
    {
        try {
            return $this->sickNecessitySubmission->employee->employee_name;
        } catch (\Exception $e) {
            return '-';
        }
    }

    public function positionName()
    {
        try {
            return $this->sickNecessitySubmission->employee->position->position_name;
        } catch (\Exception $e) {
            return '-';
        }
    }

    public function departmentName()
    {
        try {
            return $this->sickNecessitySubmission->employee->department->department_name;
        } catch (\Exception $e) {
            return '-';
        }
    }

    public function reasonText()
    {
        try {
            return $this->sickNecessitySubmission->reason;
        } catch (\Exception $e) {
            return '-';
        }
    }

    public function userName()
    {
        return $this->user ? $this->user->name : '-';
    }


    /**
     *  Static methods
     * */

    public static function dataTable($request)
    {
        $data = self::select([ 'sick_necessity_submission_approvals.*' ])
                    ->has('sickNecessitySubmission')
                    ->with([ 'sickNecessitySubmission.employee.position' ])
                    ->leftJoin('sick_necessity_submissions', 'sick_necessity_submission_approvals.id_sick_necessity_submission', '=', 'sick_necessity_submissions.id')
                    ->leftJoin('employees', 'sick_necessity_submissions.id_employee', '=', 'employees.id')
                    ->leftJoin('positions', 'employees.id_position', '=', 'positions.id');

        if(user()->isEmployee()) {
            $data = $data->where('sick_necessity_submission_approvals.id_approver_position', employee()->id_position)
                         ->where(function($q1){
                            $q1->where(function($q2){
                                $q2->where('sick_necessity_submission_approvals.level', 1)
                                   ->where('sick_necessity_submissions.approval_progress_level', 1);
                            })->orWhere(function($q2){
                                $q2->where('sick_necessity_submission_approvals.level', 2)
                                   ->where('sick_necessity_submissions.approval_progress_level', 2);
                            });
                         });
        } elseif (user()->isAdmin()) {
            $data = $data->where('sick_necessity_submission_approvals.position_level', '0');
        }

        return \DataTables::eloquent($data)
            ->editColumn('created_at', function($data){
                return $data->createdAtText();
            })
            ->editColumn('employee_name', function($data){
                $html = $data->employeeName();
                if($departmentName = $data->departmentName()) {
                    $html .= "<br><span class='text-primary'>[". $departmentName ."]</span>";
                }

                return $html;
            })
            ->editColumn('sick_necessity_submission.employee.position.position_name', function($data){
                return $data->positionName();
            })
            ->editColumn('sick_necessity_submission.reason', function($data){
                return $data->reasonText();
            })
            ->editColumn('status', function($data){
                return $data->statusHtml();
            })
            ->editColumn('sick_necessity_submission.status', function($data){
                return $data->sickNecessitySubmission->statusHtml();

                return '-';
            })
            ->editColumn('start_date', function($data){
                $sickNecessitySub = $data->sickNecessitySubmission;
                if($sickNecessitySub->start_date == $sickNecessitySub->end_date) {
                    return $sickNecessitySub->startDateText('d M Y');
                } else {
                    return $sickNecessitySub->startDateText('d M Y').' - <br>'.$sickNecessitySub->endDateText('d M Y');
                }
            })
            ->addColumn('action', function($data){
                if ($data->isStatusWaiting()) {
                    $button = '
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Aksi
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="'.route('employee.sick_necessity_approval.detail', $data->id).'" title="Detail Pengajuan Izin/Sakit">
                                <i class="mdi mdi-magnify"></i> Detail
                            </a>
                            <a class="dropdown-item approve" href= "javascript:void(0)" data-href="'.route('employee.sick_necessity_approval.approve', $data->id).'" title="Setujui Pengajuan Izin/Sakit">
								<i class="mdi mdi-check"></i> Setuju
							</a>
							<a class="dropdown-item reject" href= "javascript:void(0)" data-href="'.route('employee.sick_necessity_approval.reject', $data->id).'" title="Tolak Pengajuan Izin/Sakit">
								<i class="mdi mdi-close"></i> Tolak
							</a>
                        </div>
                    </div>';

                    return $button;
                }elseif($data->isStatusApproved()){
                    $button = '
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Aksi
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="'.route('employee.sick_necessity_approval.detail', $data->id).'" title="Detail Pengajuan Izin/Sakit">
                                <i class="mdi mdi-magnify"></i> Detail
                            </a>
                        </div>
                    </div>';

                    return $button;
                }
            })
            ->rawColumns([ 'status', 'employee_name', 'start_date', 'action', 'sick_necessity_submission.status' ])
            ->make(true);
    }


    public function approve()
    {
        $this->update([
            'approved_at'   => now(),
            'id_user'       => user()->id,
            'status'        => self::STATUS_APPROVED
        ]);
        $this->sickNecessitySubmission->checkingApproval();

        return $this;
    }

    public function reject()
    {
        $this->update([
            'rejected_at'   => now(),
            'id_user'       => user()->id,
            'status'        => self::STATUS_REJECTED
        ]);
        $this->sickNecessitySubmission->checkingApproval();

        return $this;
    }

    public function cancel()
    {
        $this->update([
            'status'    => self::STATUS_CANCELED
        ]);

        return $this;
    }

    public function sendNotification()
    {
        $this->load('sickNecessitySubmission.employee');
        $message = "*HRIS System*";
        $message .= "\n\nKaryawan atas nama *".$this->sickNecessitySubmission->employeeName()."* telah mengajukan ".strtolower($this->sickNecessitySubmission->reason).", diharapkan untuk segera memproses penyetujuan/penolakan.";
        $message .= "\nKlik link berikut untuk lihat detail pengajuan ".route('employee.sick_necessity_approval.detail', $this->id);
        foreach($this->approverPosition->employees as $employee) {
            // \App\MyClass\Whatsapp::sendChat([
            //     'to'    => \App\MyClass\Helper::idPhoneNumberFormat($employee->phone_number),
            //     'text'  => $message
            // ]);

            $EndPointWa = WhatsappNew::END_POINT_WA;
            if($EndPointWa == 'WA Baru'){
                // wa Baru
                $res = Helper::sendNotificationWhatsapp($phoneNumber =\App\MyClass\Helper::idPhoneNumberFormat($employee->phone_number), $message);
            }else{
                $res = Whatsapp::sendChat([
                    'to'    => \App\MyClass\Helper::idPhoneNumberFormat($employee->phone_number),
                    'text'  => $message,
                ]);
            }
        }

        return $this;
    }

     public function getEmployeePosition()
    {
        return Employee::where('status', Employee::STATUS_ACTIVE)
            ->when(!empty($this->id_approver_position), function ($query) {
                $query->where('id_position', $this->id_approver_position);
            })->get();
    }

    public function resendBroadcastToApproval()
    {
       $this->loadMissing([
            'sickNecessitySubmission.employee',
            'sickNecessitySubmission.sickReason',
            'approverPosition.employees',
        ]);

        if (!$this->sickNecessitySubmission) {
            return \Res::invalid([
                'message' => 'Pengajuan izin/sakit tidak ditemukan.',
            ]);
        }

        $employeeName = $this->sickNecessitySubmission->employeeName();
        $sickNecessityId = $this->sickNecessitySubmission->id;
        $reason = strtolower($this->sickNecessitySubmission->reason);

        $message = "*HRIS System*";
        $message .= "\n\nKaryawan atas nama *{$employeeName}* telah mengajukan {$reason}, diharapkan untuk segera memproses penyetujuan/penolakan.";
        $message .= "\nKlik link berikut untuk lihat detail pengajuan: ";
        $message .= route('employee.sick_necessity_approval.detail', ['sickNecessitySubmissionApproval' => $sickNecessityId]);


        foreach ($this->getEmployeePosition() as $employee) {
            if (!$employee->phone_number) {
                continue;
            }

            $phoneNumber = \App\MyClass\Helper::idPhoneNumberFormat($employee->phone_number);
            $endPointWa = \App\MyClass\WhatsappNew::END_POINT_WA;

            if ($endPointWa === 'WA Baru') {
                \App\MyClass\Helper::sendNotificationWhatsapp($phoneNumber, $message);
            } else {
                \App\MyClass\Whatsapp::sendChat([
                    'to'   => $phoneNumber,
                    'text' => $message,
                ]);
            }
        }

        return $this;
    }

}
