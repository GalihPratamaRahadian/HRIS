<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CompanyRulesController extends Controller
{
    public function index()
    {
        return view('employee.company_rules.index', [
            'title'         => 'Peraturan Perusahaan',
            'breadcrumbs'   => [
                [
                    'title' => 'Peraturan Perusahaan',
                    'link'  => route('employee.company_rules')
                ],
            ]
        ]);
    }
}
