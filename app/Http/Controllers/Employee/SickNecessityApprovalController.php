<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SickNecessitySubmissionApproval;
use DB;

class SickNecessityApprovalController extends Controller
{
	public function index(Request $request)
	{
		if($request->ajax()) {
			return SickNecessitySubmissionApproval::dataTable($request);
		}

		return view('employee.sick_necessity_approval.index', [
			'title'         => 'Penyetujuan Sakit/Izin',
			'breadcrumbs'   => [
				[
					'title' => 'Penyetujuan Sakit/Izin',
					'link'  => route('employee.sick_necessity_approval')
				],
			]
		]);
	}

	public function detail(SickNecessitySubmissionApproval $sickNecessitySubmissionApproval)
	{
		$sickNecessitySubmissionApproval->load('sickNecessitySubmission');
		if(!$sickNecessitySubmissionApproval->sickNecessitySubmission) abort(404);

		return view('employee.sick_necessity_approval.detail', [
			'title'         => 'Detail Penyetujuan Sakit/Izin',
			'approval'      => $sickNecessitySubmissionApproval,
			'breadcrumbs'   => [
				[
					'title' => 'Penyetujuan Sakit/Izin',
					'link'  => route('employee.sick_necessity_approval')
				],
				[
					'title' => 'Detail',
					'link'  => route('employee.sick_necessity_approval.detail', $sickNecessitySubmissionApproval->id)
				],
			]
		]);
	}

	public function approve(SickNecessitySubmissionApproval $sickNecessitySubmissionApproval)
	{
		try {
			DB::beginTransaction();
			$sickNecessitySubmissionApproval->approve();
			DB::commit();

			return \Res::success([
				'message'   => 'Berhasil disetujui'
			]);
		} catch (\Exception $e) {
			DB::commit();

			return \Res::error($e);
		}
	}

	public function reject(SickNecessitySubmissionApproval $sickNecessitySubmissionApproval)
	{
		try {
			DB::beginTransaction();
			$sickNecessitySubmissionApproval->reject();
			DB::commit();

			return \Res::success([
				'message'   => 'Berhasil ditolak'
			]);
		} catch (\Exception $e) {
			DB::commit();

			return \Res::error($e);
		}
	}
}
