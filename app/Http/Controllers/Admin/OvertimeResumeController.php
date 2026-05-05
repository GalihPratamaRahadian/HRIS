<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OvertimeSubmission;

class OvertimeResumeController extends Controller
{
    public function index()
    {
        return view('admin.overtime_resume.index', [
            'title'         => 'Rekap Lembur',
            'breadcrumbs'   => [
                [
                    'title' => 'Rekap Lembur',
                    'link'  => route('admin.overtime_resume')
                ],
            ]
        ]);
    }

    public function generate(Request $request)
    {
        try {
            $action = $request->action;

            if ($action == 'pdf_stream') {
                return OvertimeSubmission::resumeStreamPdfReport($request);
            } elseif ($action == 'pdf_download') {
                return OvertimeSubmission::resumeDownloadPdfReport($request);
            } elseif ($action == 'xlsx_download') {
                $path = OvertimeSubmission::resumeDownloadExcelReport($request);

                return response()->download($path)->deleteFileAfterSend();
            } else {
                return OvertimeSubmission::resumeStreamPdfReport($request);
            }
        } catch (\Exception $e) {
            return \Res::error($e);
        }
    }
}
