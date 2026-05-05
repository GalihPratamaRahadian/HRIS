<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AttendancePermissionSubmission;
use Carbon\Carbon;
use DB;

class AttendancePermissionSubmissionController extends Controller
{

    public function index(Request $request)
    {
        if($request->ajax()) {
            return AttendancePermissionSubmission::dt($request);
        }

        return view('employee.attendance_permission_submission.index', [
            'title'         => 'Izin Terlambat / Pulang Cepat',
            'breadcrumbs'   => [
                [
                    'title' => 'Izin Terlambat / Pulang Cepat',
                    'link'  => route('employee.attendance_permission_submission')
                ],
            ]
        ]);
    }

    public function create()
    {
        return view('employee.attendance_permission_submission.create', [
            'title'         => 'Buat Izin Terlambat / Pulang Cepat',
            'breadcrumbs'   => [
                [
                    'title' => 'Izin Terlambat / Pulang Cepat',
                    'link'  => route('employee.attendance_permission_submission')
                ],
                [
                    'title' => 'Buat',
                    'link'  => route('employee.attendance_permission_submission.create')
                ],
            ]
        ]);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $requestedDateTime = Carbon::createFromFormat('Y-m-d H:i', $request->date . ' ' . $request->time);

            if ($requestedDateTime <= Carbon::now()) {
                return \Res::invalid([
                    'message' => 'Waktu yang diajukan harus lebih dari waktu saat ini!',
                ]);
            }

            $attendancePermission = AttendancePermissionSubmission::where('id_employee', $request->id_employee)
                ->where('date', $request->date)
                ->where('type', $request->type)
                ->exists();

            if ($attendancePermission) {
                return \Res::invalid([
                    'message' => 'Pengajuan ' . strtolower($request->type) . ' untuk tanggal tersebut sudah ada. Tidak bisa mengajukan lebih dari satu kali.',
                ]);
            }

            $latestSubmission = AttendancePermissionSubmission::where('id_employee', $request->id_employee)
                ->where('date', $request->date)
                ->where('type', $request->type)
                ->latest()
                ->first();

            if ($latestSubmission && $requestedDateTime <= $latestSubmission->created_at) {
                return \Res::invalid([
                    'message' => 'Waktu yang diajukan harus di atas waktu diajukan sebelumnya!',
                ]);
            }

            $data = $request->all();
            $data['id_employee'] = employee()->id;
            $idEmployee = user()->isEmployee() ? auth()->user()->employee->id : $request->id_employee;
			$overtimeSubmission = AttendancePermissionSubmission::where('id_employee', $idEmployee)->where('date', $request->date)->first();
			if (!$overtimeSubmission) {
                AttendancePermissionSubmission::createAttendancePermissionSubmission($data);
                DB::commit();

			}

            return \Res::success();

        } catch (\Exception $e) {
            DB::rollback();
            return \Res::error($e);
        }
    }


    public function detail(AttendancePermissionSubmission $attendancePermissionSubmission)
    {
        return view('employee.attendance_permission_submission.detail', [
            'title'         => 'Detail Izin Terlambat/Pulang Cepat',
            'attendancePermissionSubmission' => $attendancePermissionSubmission,
            'breadcrumbs'   => [
                [
                    'title' => 'Izin Terlambat/Pulang Cepat',
                    'link'  => route('employee.attendance_permission_submission')
                ],
                [
                    'title' => 'Detail',
                    'link'  => route('employee.attendance_permission_submission.detail', $attendancePermissionSubmission->id)
                ],
            ]
        ]);
    }
}
