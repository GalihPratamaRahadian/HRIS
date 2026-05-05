<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CheckDay;
use DB;

class CheckDayController extends Controller
{
	public function index(Request $request)
	{
		if($request->ajax()) {
			return CheckDay::dt($request);
		}

		return view('admin.check_day.index', [
			'title'         => 'Check Day',
			'breadcrumbs'   => [
				[
					'title' => 'Check Day',
					'link'  => route('check_day')
				],
			]
		]);
	}

	public function detail(CheckDay $checkDay)
	{
		if(auth()->user()->isEmployee()) {
			if(auth()->user()->employee->id != $checkDay->id_employee) {
				abort(404);
			}
		}

		return view('admin.check_day.detail', [
			'title'			=> 'Detail Check Day',
			'checkDay'		=> $checkDay,
			'breadcrumbs'	=> [
				[
					'title'	=> 'Check Day',
					'link'	=> route('check_day')
				],
				[
					'title'	=> 'Detail Check Day',
					'link'	=> route('check_day.detail', $checkDay->id)
				]
			]
		]);
	}

	public function destroy(CheckDay $checkDay)
	{

	}

	public function xhrGetCheckDayData(Request $request)
	{
		try {
			return \Setting::successResponse([
				'date'		=> date('d M Y', strtotime($request->date)),
			]);
		} catch (\Exception $e) {
			return \Setting::errorResponse($e);
		}
	}
}
