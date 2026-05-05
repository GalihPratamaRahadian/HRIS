<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CompanyRulesController extends Controller
{
	public function index()
	{
		return view('admin.company_rules.index', [
			'title'			=> 'Peraturan Perusahaan',
			'breadcrumbs'	=> [
				[
					'title' => 'Peraturan Perusahaan',
					'link'  => route('admin.company_rules')
				],
			]
		]);
	}

	public function save(Request $request)
	{
		try {
			saveSetting('link_pkb', $request->link_pkb);
			return \Res::success();
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}
}
