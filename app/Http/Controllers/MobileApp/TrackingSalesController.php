<?php

namespace App\Http\Controllers\MobileApp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\StoreVisit;
use App\Models\SalesEmployee;
use App\Models\MobileAppToken;

use DB;

class TrackingSalesController extends Controller
{
	public function createStore(Request $request)
	{
		$request->validate([
			'store_name'	=> 'required',
			'address'		=> 'required',
			'latitude'		=> 'required',
			'longitude'		=> 'required',
		], [
			'store_name.required' => 'Nama Toko Dibutuhkan',
			'address.required' => 'Alamat Dibutuhkan',
			'latitude.required' => 'Koordinat Latitude Dibutuhkan',
			'longitude.required' => 'Koordinat Longitude Dibutuhkan',
		]);

		DB::beginTransaction();

		try {
			$token = MobileAppToken::getByToken($request->token);
			$employee = $token->employee;

			$salesEmployee = SalesEmployee::where('id_employee', $employee->id)->first();
			if(!$salesEmployee) {
				return \Res::invalid([
					'message'	=> 'Belum terdaftar sebagai sales'
				]);
			}

			$store = Store::create([
				'store_name'	=> $request->store_name,
				'phone_number'	=> $request->phone_number,
				'address'		=> $request->address,
				'latitude'		=> $request->latitude,
				'longitude'		=> $request->longitude,
				'registered_by'	=> $employee->id,
				'handled_by'	=> $employee->id,
				'partner_status' => 'active',
			]);

			$store->load('handledBy');
			$store->handledBy->salesEmployee->countStoreHandle();
			DB::commit();

			return \Res::success([
				'result' => [
					'store'	=> $store
				]
			]);
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	public function listStore(Request $request)
	{
		$request->validate([
			'latitude'		=> 'required',
			'longitude'		=> 'required',
		], [
			'latitude.required' => 'Koordinat Latitude Dibutuhkan',
			'longitude.required' => 'Koordinat Longitude Dibutuhkan',
		]);

		try {
			$token = MobileAppToken::getByToken($request->token);
			$employee = $token->employee;

			$stores = Store::where('handled_by', $employee->id);
			$search = $request->search;
			$latitude = $request->latitude;
			$longitude = $request->longitude;
			
			if(!empty($search)) {
				$stores = $stores->where(function($query) use ($search) {
					$query->where('store_name', 'like', '%'.$search.'%')
						  ->orWhere('address', 'like', '%'.$search.'%');
				});
			}

			$stores = $stores->get();

			$results = [];

			foreach($stores as $store) {
				$distanceText = '0 Meter';
				$distance = 0;
				if(!empty($latitude) && !empty($longitude)) {
					$distance = $store->distanceInMeters($latitude, $longitude);
					$distanceText = $store->distanceText($latitude, $longitude);
				}

				$results[] = (object) [
					'id'			=> $store->id,
					'store_name'	=> $store->store_name,
					'address'		=> $store->address,
					'distance'		=> $distance,
					'distance_text'	=> $distanceText,
					'is_visited_today' => $store->isVisitedToday(),
				];
			}

			return \Res::success([
				'result' => [
					'stores' => $results,
				]
			]);
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}


	public function getStore(Request $request)
	{
		$request->validate([
			'id_store'		=> 'required|exists:stores,id',
		], [
			'id_store.required' => 'ID Store Dibutuhkan',
			'id_store.exists' => 'Toko Tidak Ditemukan',
		]);

		try {
			$token = MobileAppToken::getByToken($request->token);
			$employee = $token->employee;

			$store = Store::find($request->id_store);

			return \Res::success([
				'result' => [
					'store' => $store->fetchData(),
				]
			]);
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}


	public function checkInStore(Request $request)
	{
		$request->validate([
			'id_store'		=> 'required|exists:stores,id',
			'latitude'		=> 'required',
			'longitude'		=> 'required',
			'purchase'		=> 'required|in:yes,no',
			'photo'			=> 'required',
		], [
			'latitude.required' => 'Koordinat Latitude Dibutuhkan',
			'longitude.required' => 'Koordinat Longitude Dibutuhkan',
			'purchase.required' => 'Pembelian Dibutuhkan',
			'purchase.in' => 'Pembelian Hanya Menerima "yes" atau "no"',
			'id_store.required' => 'ID Store Dibutuhkan',
			'id_store.exists' => 'Toko Tidak Ditemukan',
			'photo.required' => 'Foto Dibutuhkan',
		]);

		DB::beginTransaction();

		try {
			$token = MobileAppToken::getByToken($request->token);
			$employee = $token->employee;
			
			$latitude = $request->latitude;
			$longitude = $request->longitude;

			$storeVisit = StoreVisit::where('id_store', $request->id_store)
									->where('id_employee', $employee->id)
									->where('visited_at', '>=', today())
									->first();
			if($storeVisit) {
				return \Res::invalid([
					'message'	=> 'Toko Sudah Dikunjungi'
				]);
			}

			$store = Store::find($request->id_store);

			if($store->isLocationValid($latitude, $longitude)) {
				$storeVisit = StoreVisit::create([
					'id_employee'	=> $employee->id,
					'id_store'		=> $request->id_store,
					'latitude'		=> $request->latitude,
					'longitude'		=> $request->longitude,
					'visited_at'	=> now(),
					'purchase'		=> $request->purchase,
				]);
				$storeVisit->setPhoto($request);
				DB::commit();

				return  \Res::success([
					'result' => [
						'storeVisit' => $storeVisit->fetchData(),
					]
				]);
			} else {
				return \Res::invalid([
					'message'	=> 'Harap Lakukan Check In di Area Toko'
				]);
			}
		} catch (\Exception $e) {
			DB::rollback();
			return \Res::error($e);
		}
	}


	public function getStoreVisit(Request $request)
	{
		$request->validate([
			'id_store'		=> 'required|exists:stores,id',
		], [
			'id_store.required' => 'ID Store Dibutuhkan',
			'id_store.exists' => 'Toko Tidak Ditemukan',
		]);

		try {
			$token = MobileAppToken::getByToken($request->token);
			$employee = $token->employee;

			$storeVisit = StoreVisit::where('id_store', $request->id_store)
									->where('id_employee', $employee->id)
									->where('visited_at', '>=', today())
									->first();

			if($storeVisit) {
				return \Res::success([
					'result' => [
						'storeVisit' => $storeVisit->fetchData(),
					]
				]);
			} else {
				$store = Store::find($request->id_store);
				return \Res::invalid([
					'message'	=> 'Kamu belum melakukan kunjungan ke '.$store->store_name. ' hari ini'
				]);
			}

		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}
}
