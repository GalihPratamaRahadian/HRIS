<?php

namespace App\Http\Controllers\MobileApp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MobileAppToken;
use App\Models\LeaveReason;
use DB;

class LeaveReasonController extends Controller
{
	public function list(Request $request)
	{
		try {
			$token = MobileAppToken::getByToken($request->token);
			$employee = $token->employee;

			$page = $request->page ?? 1;
			$limit = $request->limit ?? 5;

			$leaveReasons = LeaveReason::all();
				
			return \Res::success([
				'result' => [
					'leaveReasons'   => LeaveReason::fetchLeaveReasons($leaveReasons),
				]
			]);
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}
}
