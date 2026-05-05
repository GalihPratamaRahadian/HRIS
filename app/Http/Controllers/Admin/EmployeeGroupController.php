<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmployeeGroup;
use Validations;
use DB;

class EmployeeGroupController extends Controller
{
    /**
     *  Employee Group
     * */
    public function index(Request $request)
    {
        if($request->ajax()) {
            return EmployeeGroup::dataTable($request);
        }

        return view('admin.employee_group.index', [
            'title'         => 'Grup Karyawan',
            'breadcrumbs'   => [
                [
                    'title' => 'Grup Karyawan',
                    'link'  => route('admin.employee_group')
                ]
            ]
        ]);
    }


    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            EmployeeGroup::createEmployeeGroup($request->all());
            DB::commit();

            return \Res::save();
        } catch (\Exception $e) {
            DB::rollback();

            return \Res::error($e);
        }
    }


    public function get(EmployeeGroup $employeeGroup)
    {
        try {
            return \Res::success([
                'employeeGroup' => $employeeGroup
            ]);
        } catch (\Exception $e) {
            return \Res::error($e);
        }
    }


    public function update(Request $request, EmployeeGroup $employeeGroup)
    {
        DB::beginTransaction();

        try {
            $employeeGroup->updateEmployeeGroup($request->all());
            DB::commit();

            return \Res::update();
        } catch (\Exception $e) {
            DB::rollback();

            return \Res::error($e);
        }
    }


    public function destroy(EmployeeGroup $employeeGroup)
    {
        DB::beginTransaction();

        try {
            $employeeGroup->deleteEmployeeGroup();
            DB::commit();

            return \Res::delete();
        } catch (\Exception $e) {
            DB::rollback();

            return \Res::error($e);
        }
    }
}
