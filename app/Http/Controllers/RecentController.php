<?php

namespace App\Http\Controllers;

use App\FTLog;
use App\FTEventLog;
use App\VisitorAkses;
use App\SiteAkses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Models\FaceTerminalLog;
use Setting;

class RecentController extends Controller
{
	
	public function index()
	{
		return view('log/dataLog', [
			'mustMask'  => Setting::getValue('gate_must_masking'),
			'minTemp'   => Setting::getValue('temperature_min'),
			'maxTemp'   => Setting::getValue('temperature_max'),
		]);
	}


	public function xhrGetLogs($start, $end)
	{
		$logs = FaceTerminalLog::fetchedDataForRecentPanel($start, $end);

		return response()->json($logs, 200);
	}


	public function detail(FaceTerminalLog $faceTerminalLog)
	{
		$log = $faceTerminalLog->fetchData();

		return response()->json($log, 200);
	}


	public function xhrGetLatest()
	{
		if(FaceTerminalLog::count() == 0) return [];

		$log = FaceTerminalLog::orderBy('id', 'desc')->first();

		return response()->json($log->fetchData(), 200);
	}
}
