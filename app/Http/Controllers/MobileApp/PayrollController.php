<?php

namespace App\Http\Controllers\MobileApp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MobileAppToken;
use App\Models\Payroll;

class PayrollController extends Controller
{
	public function list(Request $request)
	{
		try {
			$token = MobileAppToken::getByToken($request->token);
			$employee = $token->employee;

			$page = $request->page ?? 1;
			$limit = $request->limit ?? 5;

			$payrolls = Payroll::where('id_employee', $employee->id)
							   ->orderBy('period_start', 'desc')
							   ->take($limit)
							   ->skip(($page - 1) * $limit)
							   ->get();
				
			return \Res::success([
				'result' => [
					'payrolls'   => Payroll::fetchPayrolls($payrolls),
				]
			]);
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}


	public function detail(Request $request)
	{
		$request->validate([
			'id_payroll' => 'required|exists:payrolls,id',
		], [
			'id_payroll.required'=> 'ID Payroll diperlukan',
			'id_payroll.exists'  => 'Data payroll tidak ditemukan'
		]);

		try {
			$payroll = Payroll::find($request->id_payroll);
				
			return \Res::success([
				'result' => [
					'payroll'    => $payroll->fetchData(),
				]
			]);
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}
}
