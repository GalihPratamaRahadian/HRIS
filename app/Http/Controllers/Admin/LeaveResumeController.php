<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmployeeLeave;

class LeaveResumeController extends Controller
{
	public function index()
	{
		return view('admin.leave_resume.index', [
			'title'         => 'Rekap Cuti',
			'breadcrumbs'   => [
				[
					'title' => 'Rekap Cuti',
					'link'  => route('admin.leave_resume')
				],
			]
		]);
	}

	public function generate(Request $request)
	{
		try {
			$action = $request->action;

			if ($action == 'pdf_stream') {
				return EmployeeLeave::resumeStreamPdfReport($request);
			} elseif ($action == 'pdf_download') {
				return EmployeeLeave::resumeDownloadPdfReport($request);
			} elseif ($action == 'xlsx_download') {
				$path = EmployeeLeave::resumeDownloadExcelReport($request);

				return response()->download($path)->deleteFileAfterSend();
			} else {
				return EmployeeLeave::resumeStreamPdfReport($request);
			}
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}
}
