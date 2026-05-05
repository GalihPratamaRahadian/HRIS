<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WarningLetter;
use DB;

class WarningLetterController extends Controller
{
	
	public function index(Request $request)
	{
		if($request->ajax()) {
			return WarningLetter::dt($request);
		}

		return view('admin.warning_letter.index', [
			'title'			=> 'Surat Peringatan',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Surat Peringatan',
					'link'	=> route('warning_letter')
				]
			]
		]);
	}


	public function create()
	{
		return view('admin.warning_letter.create', [
			'title'			=> 'Buat Surat Peringatan',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Surat Peringatan',
					'link'	=> route('warning_letter')
				],
				[
					'title'	=> 'Buat',
					'link'	=> route('warning_letter.create')
				]
			]
		]);
	}


	public function store(Request $request)
	{
		$request->validate([
			'id_employee'	=> 'required|exists:employees,id',
			'end_date'		=> 'required|after_or_equal:start_date'
		], [
			'end_date.after_or_equal'	=> 'Jangka waktu akhir tidak valid'
		]);
		DB::beginTransaction();

		try {
			WarningLetter::createWarningLetter($request->all());
			DB::commit();

			return \Res::save();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	public function edit(WarningLetter $warningLetter)
	{
		return view('admin.warning_letter.edit', [
			'title'			=> 'Edit Surat Peringatan',
			'warningLetter'	=> $warningLetter,
			'breadcrumbs'	=> [
				[
					'title'	=> 'Surat Peringatan',
					'link'	=> route('warning_letter')
				],
				[
					'title'	=> 'Edit',
					'link'	=> route('warning_letter.edit', $warningLetter->id)
				]
			]
		]);
	}


	public function update(Request $request, WarningLetter $warningLetter)
	{
		$request->validate([
			'id_employee'	=> 'required|exists:employees,id',
			'end_date'		=> 'required|after_or_equal:start_date'
		], [
			'end_date.after_or_equal'	=> 'Jangka waktu akhir tidak valid'
		]);
		DB::beginTransaction();

		try {
			$warningLetter->updateWarningLetter($request->all());
			DB::commit();

			return \Res::update();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function destroy(WarningLetter $warningLetter)
	{
		DB::beginTransaction();

		try {
			$warningLetter->deleteWarningLetter();
			DB::commit();

			return \Res::delete();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}
}
