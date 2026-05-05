<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmployeeLeave;
use App\Models\Employee;
use App\MyClass\Validations;
use DB;

class EmployeeLeaveController extends Controller
{
    public function index(Request $request)
    {
        if($request->ajax()) {
            return EmployeeLeave::dt();
        }

        return view('admin.employee_leave.index', [
            'title'         => 'Cuti',
            'breadcrumbs'   => [
                [
                    'title' => 'Cuti',
                    'link'  => route('admin.employee_leave')
                ],
            ]
        ]);
    }


    public function create()
    {
        return view('admin.employee_leave.create', [
            'title'         => 'Buat Cuti',
            'breadcrumbs'   => [
                [
                    'title' => 'Cuti',
                    'link'  => route('admin.employee_leave')
                ],
                [
                    'title' => 'Buat',
                    'link'  => route('admin.employee_leave.create')
                ],
            ]
        ]);
    }


    public function store(Request $request)
    {
        Validations::validateEmployeeLeave($request);
        DB::beginTransaction();

        try {
            $amountOfDays = \App\MyClass\Date::amountOfDays($request->start_date, $request->end_date);
            $employee = Employee::find($request->id_employee);
            if($employee->leaveQuotaAvailable() < $amountOfDays) {
                return \Res::invalid([
                    'message'   => $employee->employee_name.' hanya punya jatah cuti '.$employee->leaveQuotaAvailable().' hari',
                ]);
            }

            EmployeeLeave::createEmployeeLeave($request);
            DB::commit();

            return \Res::save();
        } catch (\Exception $e) {
            DB::rollback();

            return \Res::error($e);
        }
    }


    public function edit(EmployeeLeave $employeeLeave)
    {
        return view('admin.employee_leave.edit', [
            'title'         => 'Edit Cuti',
            'employeeLeave' => $employeeLeave,
            'breadcrumbs'   => [
                [
                    'title' => 'Cuti',
                    'link'  => route('admin.employee_leave')
                ],
                [
                    'title' => 'Edit',
                    'link'  => route('admin.employee_leave.create')
                ],
            ]
        ]);
    }


    public function update(Request $request, EmployeeLeave $employeeLeave)
    {
        Validations::validateEmployeeLeave($request);
        DB::beginTransaction();

        try {
            $employeeLeave->updateEmployeeLeave($request);
            DB::commit();

            return \Res::update();
        } catch (\Exception $e) {
            DB::rollback();

            return \Res::error($e);
        }
    }


    public function destroy(EmployeeLeave $employeeLeave)
    {
        DB::beginTransaction();

        try {
            $employeeLeave->deleteEmployeeLeave();
            DB::commit();

            return \Setting::deleteResponse();
        } catch (\Exception $e) {
            DB::rollback();

            return \Res::error($e);
        }
    }


    public function detail(EmployeeLeave $employeeLeave)
    {
        return view('admin.employee_leave.detail', [
            'title'         => 'Detail Cuti',
            'employeeLeave' => $employeeLeave,
            'breadcrumbs'   => [
                [
                    'title' => 'Cuti',
                    'link'  => route('admin.employee_leave')
                ],
                [
                    'title' => 'Detail',
                    'link'  => route('admin.employee_leave.create')
                ],
            ]
        ]);
    }
}
