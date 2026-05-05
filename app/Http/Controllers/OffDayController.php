<?php

namespace App\Http\Controllers;

use App\Models\OffDay;
use Illuminate\Http\Request;
use App\MyClass\Validations;
use DB;

class OffDayController extends Controller
{

	public function index(Request $request)
	{
		if($request->ajax()) {
			return OffDay::dt();
		}

		return view('admin.off_day.index', [
			'title'			=> 'Hari Libur',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Hari Libur',
					'link'	=> route('off_day')
				],
			]
		]);
	}

	
	public function create()
	{
		return view('admin.off_day.create', [
			'title'			=> 'Tambah Hari Libur',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Hari Libur',
					'link'	=> route('off_day')
				],
				[
					'title'	=> 'Tambah',
					'link'	=> route('off_day.create')
				],
			]
		]);
	}


	public function store(Request $request)
	{
		Validations::validateOffDay($request);
		DB::beginTransaction();

		try {
			OffDay::createOffDay($request);
			DB::commit();

			return \Res::save();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	public function edit(OffDay $offDay)
	{
		$offDay->load('offDayDetails.employee');
		
		return view('admin.off_day.edit', [
			'title'			=> 'Edit Hari Libur',
			'offDay'		=> $offDay,
			'breadcrumbs'	=> [
				[
					'title'	=> 'Hari Libur',
					'link'	=> route('off_day')
				],
				[
					'title'	=> 'Edit',
					'link'	=> route('off_day.create')
				],
			]
		]);
	}


	public function update(Request $request, OffDay $offDay)
	{
		Validations::validateOffDay($request);
		DB::beginTransaction();

		try {
			$offDay->updateOffDay($request);
			DB::commit();

			return \Res::update();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	public function destroy(OffDay $offDay)
	{
		DB::beginTransaction();

		try {
			$offDay->deleteOffDay();
			DB::commit();

			return \Res::delete();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}
}
