<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payroll;

class PayrollResumeController extends Controller
{
    public function index()
    {
        return view('admin.payroll_resume.index', [
            'title'         => 'Rekap Payroll',
            'breadcrumbs'   => [
                [
                    'title' => 'Rekap Payroll',
                    'link'  => route('admin.payroll_resume')
                ],
            ]
        ]);
    }

    public function generate(Request $request)
    {
        try {
            $action = $request->action;

            if ($action == 'pdf_stream') {
                return Payroll::resumeStreamPdfReport($request);
            } elseif ($action == 'pdf_download') {
                return Payroll::resumeDownloadPdfReport($request);
            } elseif ($action == 'xlsx_download') {
                $path = Payroll::resumeDownloadExcelReport($request);

                return response()->download($path)->deleteFileAfterSend();
            } else {
                return Payroll::resumeStreamPdfReport($request);
            }
        } catch (\Exception $e) {
            return \Res::error($e);
        }
    }
}
