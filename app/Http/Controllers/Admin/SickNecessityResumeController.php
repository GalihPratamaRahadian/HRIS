<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SickNecessitySubmission;
use Illuminate\Http\Request;

class SickNecessityResumeController extends Controller
{
	public function index()
	{
		return view('admin.sick_necessity_resume.index', [
			'title'         => 'Rekap Sakit/Izin',
			'breadcrumbs'   => [
				[
					'title' => 'Rekap Sakit/Izin',
					'link'  => route('admin.sick_necessity_resume')
				],
			]
		]);
	}

	public function generate(Request $request)
	{
		try {
			$action = $request->action;

			if ($action == 'pdf_stream') {
				return SickNecessitySubmission::resumeStreamPdfReport($request);
			} elseif ($action == 'pdf_download') {
				return SickNecessitySubmission::resumeDownloadPdfReport($request);
			} elseif ($action == 'xlsx_download') {
                $path = SickNecessitySubmission::resumeDownloadExcelReport($request);

                return response()->download($path)->deleteFileAfterSend();
			} else {
				return SickNecessitySubmission::resumeStreamPdfReport($request);
			}
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}
}
