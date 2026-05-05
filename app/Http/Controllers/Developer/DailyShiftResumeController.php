<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DailyShiftResume;

class DailyShiftResumeController extends Controller
{
    public function index(Request $request)
    {
        if($request->ajax())
        {
            return DailyShiftResume::dataTable($request);
        }

        return view('developer.daily_shift_resume.index', [
            'title'         => 'Rekap Shift',
            'breadcrumbs'   => [
                [
                    'title' => 'Rekap Shift',
                    'link'  => route('developer.daily_shift_resume')
                ],
            ]
        ]);
    }
}
