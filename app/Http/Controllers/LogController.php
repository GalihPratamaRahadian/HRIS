<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FaceTerminalLog;

class LogController extends Controller
{
	
	public function index(Request $request)
	{
		if($request->ajax())
		{
			return FaceTerminalLog::apiDT($request);
		}

		return view('admin.face_terminal_log.index', [
			'title'			=> 'Log Face Terminal',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Log Face Terminal',
					'route'	=> route('face_terminal_log')
				]
			]
		]);
	}


	
}
