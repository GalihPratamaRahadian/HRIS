<?php

namespace App\Http\Controllers\MobileApp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MobileAppToken;
use App\Models\MobileAppNotification;

class NotificationController extends Controller
{
	public function list(Request $request)
	{
		try {
			$token = MobileAppToken::getByToken($request->token);
			$employee = $token->employee;

			$notifications = MobileAppNotification::getNotifications($employee->id_user, $request->limit);

			return \Res::success([
				'result'    => [
					'notifications'	=> $notifications,
				]
			]);
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}
}
