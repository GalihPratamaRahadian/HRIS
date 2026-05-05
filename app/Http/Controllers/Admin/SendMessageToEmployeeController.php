<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\MyClass\Helper;
use App\MyClass\WhatsappNew;

class SendMessageToEmployeeController extends Controller
{
	public function index(Request $request)
	{
		$employee = Employee::find($request->id_employee);
		// dd($employee->id);
		return view('admin.send_message_to_employee.index', [
			'title'         => 'Kirim Pesan Ke Karyawan',
			'employee'		=> $employee,
			'breadcrumbs'   => [
				[
					'title' => 'Kirim Pesan Ke Karyawan',
					'link'  => route('admin.send_message_to_employee')
				],
			]
		]);
	}

	public function send(Request $request)
	{
		try {
			$employee = Employee::find($request->id_employee);
			$message = $request->message;

			$EndPointWa = WhatsappNew::END_POINT_WA;
				if($EndPointWa == 'WA Baru'){
					// wa Baru
					Helper::sendNotificationWhatsapp($phoneNumber = $employee->phone_number, $message);
				}else{
					\App\MyClass\Whatsapp::sendChat([
						'to'	=> $employee->phone_number,
						'text'	=> $message
					]);
				}

			return \Res::success([
				'message'	=> 'Berhasil dikirim'
			]);
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}
}
