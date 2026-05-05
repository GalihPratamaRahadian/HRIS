<?php

namespace App\Http\Controllers\MobileApp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MobileAppToken;

class HomeController extends Controller
{
	public function dashboard(Request $request)
	{
		try {
			$token = MobileAppToken::getByToken($request->token);
			$employee = $token->employee;

			$statusAction = 'Not Allowed';
			$statusCode = 0;
			if($employee->isAllowCreateAttendanceViaWeb()) {
				$att = $employee->latestAttendance;
				if($employee->isAllowForClockIn()) {
					$statusAction = 'Allow For Clock In';
					$statusCode = 1;
				} elseif($employee->isAllowForClockOut()) {
					$statusAction = 'Allow For Clock Out';
					$statusCode = 3;
				} elseif($att) {
					if(!$att->isAlreadyClockOut()) {
						$statusAction = 'Already Clock In';
						$statusCode = 2;
					}
				}
			}

			$clockStart = '-';
			$clockEnd = '-';
			if($shift = $employee->todayShift()) {
				$clockStart = $shift->clock_start;
				$clockEnd = $shift->clock_end;
			}

			if($att = $employee->latestAttendance) {
				if(!$att->isAlreadyClockOut()) {
					$clockStart = new \Carbon\Carbon($att->shift_clock_in);
					$clockStart = $clockStart->format('H:i:s');
					$clockEnd = new \Carbon\Carbon($att->shift_clock_out);
					$clockEnd = $clockEnd->format('H:i:s');
				}
			}

			return \Res::success([
				'result'    => [
					'attendance_action_code' => $statusCode,
					'attendance_action' => $statusAction,
					'shift_clock_start' => $clockStart,
					'shift_clock_end'   => $clockEnd,
					'employee'  		=> $employee->fetchData(),
				]
			]);
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}
}
