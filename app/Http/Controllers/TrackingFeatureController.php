<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesEmployee;
use App\Models\StoreVisit;
use App\Models\Store;
use DB;

class TrackingFeatureController extends Controller
{
	public function salesEmployeeIndex(Request $request)
	{
		if($request->ajax()) {
			return SalesEmployee::dt($request);
		}

		return view('admin.sales_employee.index', [
			'title'			=> 'Sales',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Sales',
					'link'	=> route('sales_employee'),
				]
			]
		]);
	}

	public function salesEmployeeCreate()
	{
		return view('admin.sales_employee.create', [
			'title'			=> 'Tambah Sales',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Sales',
					'link'	=> route('sales_employee'),
				],
				[
					'title'	=> 'Tambah Sales',
					'link'	=> route('sales_employee.create'),
				]
			]
		]);
	}


	public function salesEmployeeStore(Request $request)
	{
		$request->validate([
			'id_employees'	=> 'required'
		], [
			'id_employees.required'	=> 'Harap tambahkan minimal 1 karyawan'
		]);

		try {
			SalesEmployee::createSalesEmployee($request);
			DB::commit();
			return \Res::success();
		} catch (\Exception $e) {
			DB::rollback();
			return \Res::error($e);
		}
	}


	public function salesEmployeeDestroy(SalesEmployee $salesEmployee)
	{
		try {
			$salesEmployee->deleteSalesEmployee();
			DB::commit();
			return \Res::success();
		} catch (\Exception $e) {
			DB::rollback();
			return \Res::error($e);
		}
	}


	/**
	 * 	Store
	 * */
	public function storeIndex(Request $request)
	{
		if($request->ajax()) {
			return Store::dt($request);
		}

		return view('admin.store.index', [
			'title'			=> 'Toko',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Toko',
					'link'	=> route('store'),
				]
			]
		]);
	}

	public function storeDetail(Store $store)
	{
		return view('admin.store.detail', [
			'title'			=> 'Detail Toko',
			'store'			=> $store,
			'breadcrumbs'	=> [
				[
					'title'	=> 'Toko',
					'link'	=> route('store'),
				],
				[
					'title'	=> 'Detail Toko',
					'link'	=> route('store.detail', $store->id),
				]
			]
		]);
	}

	public function storeSetActive(Store $store)
	{
		DB::beginTransaction();

		try {
			$store->setActivePartnerStatus();
			DB::commit();

			return \Res::success();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function storeSetInactive(Store $store)
	{
		DB::beginTransaction();

		try {
			$store->setInactivePartnerStatus();
			DB::commit();

			return \Res::success();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function storeDestroy(Store $store)
	{
		DB::beginTransaction();

		try {
			$store->deleteStore();
			DB::commit();

			return \Res::success();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}



	/**
	 * 	Store Visit
	 * */
	public function storeVisitIndex(Request $request)
	{
		if($request->ajax()) {
			return StoreVisit::dt($request);
		}

		return view('admin.store_visit.index', [
			'title'			=> 'Riwayat Kunjungan',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Riwayat Kunjungan',
					'link'	=> route('store_visit'),
				]
			]
		]);
	}

	public function storeVisitDetail(StoreVisit $storeVisit)
	{
		$storeVisit->load('employee');
		$storeVisit->load('store');

		return view('admin.store_visit.detail', [
			'title'			=> 'Detail Riwayat Kunjungan',
			'storeVisit'	=> $storeVisit,
			'breadcrumbs'	=> [
				[
					'title'	=> 'Riwayat Kunjungan',
					'link'	=> route('store_visit'),
				],
				[
					'title'	=> 'Detail Riwayat Kunjungan',
					'link'	=> route('store_visit.detail', $storeVisit->id),
				]
			]
		]);
	}


	/**
	 * 	Sales Visit
	 * */
	public function salesVisitIndex(Request $request)
	{
		if($request->ajax()) {
			return SalesEmployee::salesVisitDt($request);
		}

		return view('admin.sales_visit.index', [
			'title'			=> 'Kunjungan Sales',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Kunjungan Sales',
					'link'	=> route('sales_visit'),
				]
			]
		]);
	}

	public function salesVisitDetail(Request $request, SalesEmployee $salesEmployee)
	{
		$salesEmployee->load('employee');
		$date = $request->date;
		if(empty($date)) $date = date('Y-m-d');
		$storeVisits = StoreVisit::where('id_employee', $salesEmployee->id_employee)
								 ->where('visited_at', '>=', $date.' 00:00:00')
								 ->where('visited_at', '<=', $date.' 23:59:59')
								 ->orderBy('visited_at', 'desc')
								 ->with('store')
								 ->get();
		$date = new \Carbon\Carbon($date);

		return view('admin.sales_visit.detail', [
			'title'			=> 'Detail Kunjungan Sales',
			'salesEmployee'	=> $salesEmployee,
			'storeVisits'	=> $storeVisits,
			'date'			=> $date,
			'breadcrumbs'	=> [
				[
					'title'	=> 'Kunjungan Sales',
					'link'	=> route('sales_visit'),
				],
				[
					'title'	=> 'Detail Kunjungan Sales',
					'link'	=> route('sales_visit.detail', $salesEmployee->id),
				]
			]
		]);
	}
}
