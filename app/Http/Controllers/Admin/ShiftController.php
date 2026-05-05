<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shift;
use Validations;
use DB;

class ShiftController extends Controller
{
    /**
    *   Shift
    *
    */
    public function index(Request $request)
    {
        if($request->ajax()) {
            return Shift::dataTable($request);
        }

        return view('admin.shift.index', [
            'title'         => 'Jam Kerja',
            'breadcrumbs'   => [
                [
                    'title' => 'Jam Kerja',
                    'link'  => route('admin.shift')
                ],
            ]
        ]);
    }

    public function create()
    {
        return view('admin.shift.create', [
            'title'         => 'Tambah Jam Kerja',
            'breadcrumbs'   => [
                [
                    'title' => 'Jam Kerja',
                    'link'  => route('admin.shift')
                ],
                [
                    'title' => 'Tambah',
                    'link'  => route('admin.shift.create')
                ],
            ]
        ]);
    }

    public function store(Request $request)
    {
        Validations::validateShift($request);
        DB::beginTransaction();

        try {
            Shift::createShift($request);
            DB::commit();

            return \Res::save();
        } catch (\Exception $e) {
            DB::rollback();

            return \Res::error($e);
        }
    }

    public function detail(Shift $shift)
    {
        return view('admin.shift.detail', [
            'title'         => 'Detail Jam Kerja',
            'shift'         => $shift,
            'breadcrumbs'   => [
                [
                    'title' => 'Jam Kerja',
                    'link'  => route('admin.shift')
                ],
                [
                    'title' => 'Detail',
                    'link'  => route('admin.shift.detail', $shift->id)
                ],
            ]
        ]);
    }

    public function edit(Shift $shift)
    {
        return view('admin.shift.edit', [
            'title'         => 'Edit Jam Kerja',
            'shift'         => $shift,
            'breadcrumbs'   => [
                [
                    'title' => 'Jam Kerja',
                    'link'  => route('admin.shift')
                ],
                [
                    'title' => 'Edit',
                    'link'  => route('admin.shift.edit', $shift->id)
                ],
            ]
        ]);
    }

    public function update(Request $request, Shift $shift)
    {
        Validations::validateShift($request);
        DB::beginTransaction();

        try {
            $shift->updateShift($request);
            DB::commit();

            return \Res::update();
        } catch (\Exception $e) {
            DB::rollback();

            return \Res::error($e);
        }
    }

    public function destroy(Shift $shift)
    {
        DB::beginTransaction();

        try {
            $shift->deleteShift();
            DB::commit();

            return \Res::delete();
        } catch (\Exception $e) {
            DB::rollback();

            return \Res::error($e);
        }
    }
}
