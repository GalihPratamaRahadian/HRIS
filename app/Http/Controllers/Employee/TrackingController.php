<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrackingLocation;
use App\Models\Tracking;
use DB;

class TrackingController extends Controller
{
	public function index()
	{
		return view('employee.tracking.index', [
			'title'         => 'Lokasi Tracking',
			'breadcrumbs'   => [
				[
					'title' => 'Lokasi Tracking',
					'link'  => route('employee.tracking')
				],
			]
		]);
	}


	public function getLocation(Request $request)
	{
		try {
			$locations = new TrackingLocation();
			$search = $request->search;
			
			if(!empty($search)) {
				$locations = $locations->where(function($query) use ($search) {
					$query->where('location_name', 'like', '%'.$search.'%')
						  ->orWhere('address', 'like', '%'.$search.'%');
				});
			}

			$locations = $locations->get();

			$results = [];

			foreach($locations as $location) {
				$distanceText = '0 Meter';
				$distance = 0;
				if(!empty($request->latitude) && !empty($request->longitude)) {
					$distance = $location->distanceInMeters($request->latitude, $request->longitude);
					$distanceText = $location->distanceText($request->latitude, $request->longitude);
				}

				$link = route('employee.tracking.location_detail', $location->id);

				$results[] = [
					'location_name'	=> $location->location_name,
					'address'		=> $location->address,
					'distance'		=> $distance,
					'distance_text'	=> $distanceText,
					'direction_link' => \App\MyClass\Location::make($location->latitude, $location->longitude)->gmapsLink(),
					'checkin_link'	=> $link,
					'is_visited_today' => $location->isVisitedToday(),
					'is_checked_in'	=> $location->isCheckedInToday(),
					'is_checked_out'=> $location->isCheckedOutToday(),
				];
			}

			usort($results, function($a, $b) {
			    return $a['distance'] <=> $b['distance'];
			});

			return  \Res::success([
				'locations' => $results
			]);
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}


	public function trackingLocationDetail(TrackingLocation $trackingLocation)
	{
		return view('employee.tracking.location_detail', [
			'title'			=> 'Detail Lokasi',
			'trackingLocation' => $trackingLocation,
			'breadcrumbs'	=> [
				[
					'title'	=> 'Lokasi Tracking',
					'link'	=> route('employee.tracking')
				],
				[
					'title'	=> 'Detail Lokasi',
					'link'	=> route('employee.tracking.location_detail', $trackingLocation->id)
				]
			]
		]);
	}


	public function trackingCheckIn(TrackingLocation $trackingLocation)
	{
		return view('employee.tracking.check_in', [
			'title'			=> 'Check In',
			'trackingLocation' => $trackingLocation,
			'breadcrumbs'	=> [
				[
					'title'	=> 'Lokasi Tracking',
					'link'	=> route('employee.tracking')
				],
				[
					'title'	=> 'Detail Lokasi',
					'link'	=> route('employee.tracking.location_detail', $trackingLocation->id)
				],
				[
					'title'	=> 'Check In',
					'link'	=> route('employee.tracking.check_in', $trackingLocation->id)
				]
			]
		]);
	}


	public function trackingCheckInSave(Request $request, TrackingLocation $trackingLocation)
	{
		DB::beginTransaction();

		try {
			$tracking = Tracking::create(array_merge($request->all(), [
				'id_employee'			=> employee()->id,
				'id_tracking_location'	=> $trackingLocation->id
			]));
			$tracking->saveCheckInPhoto($request);
			DB::commit();

			return \Res::success([
				'message' => 'Berhasil Check-In'
			]);
		} catch (\Exception $e) {
			DB::rollback();
			return \Res::error($e);
		}
	}


	public function trackingCheckDay(TrackingLocation $trackingLocation)
	{
		$tracking = Tracking::where('id_employee', employee()->id)
								->where('id_tracking_location', $trackingLocation->id)
								->where('check_in_at', '>=', now()->format('Y-m-d').' 00:00:00')
								->where('check_in_at', '<=', now()->format('Y-m-d').' 23:59:59')
								->where('check_day_at', null)
								->where('check_out_at', null)
								->first();

		if($tracking) {
			return view('employee.tracking.check_day', [
				'title'			=> 'Check Day',
				'trackingLocation' => $trackingLocation,
				'breadcrumbs'	=> [
					[
						'title'	=> 'Lokasi Tracking',
						'link'	=> route('employee.tracking')
					],
					[
						'title'	=> 'Detail Lokasi',
						'link'	=> route('employee.tracking.location_detail', $trackingLocation->id)
					],
					[
						'title'	=> 'Check Day',
						'link'	=> route('employee.tracking.check_day', $trackingLocation->id)
					]
				]
			]);
		} else {
			return redirect()->back();
		}
	}


	public function trackingCheckDaySave(Request $request, TrackingLocation $trackingLocation)
	{
		try {
			DB::beginTransaction();
			$tracking = Tracking::where('id_employee', employee()->id)
								->where('id_tracking_location', $trackingLocation->id)
								->where('check_in_at', '>=', now()->format('Y-m-d').' 00:00:00')
								->where('check_in_at', '<=', now()->format('Y-m-d').' 23:59:59')
								->where('check_day_at', null)
								->where('check_out_at', null)
								->first();
			if($tracking) {
				$tracking->saveCheckDayPhoto($request);
				DB::commit();

				return \Res::success([
					'message' => 'Berhasil Check-Day'
				]);
			} else {
				DB::rollback();

				return \Res::invalid([
					'message' => 'Terjadi sesuatu yang tidak normal. Harap muat ulang laman ini'
				]);
			}
		} catch (\Exception $e) {
			DB::rollback();
			return \Res::error($e);
		}
	}


	public function trackingCheckOut(TrackingLocation $trackingLocation)
	{
		$tracking = Tracking::where('id_employee', employee()->id)
								->where('id_tracking_location', $trackingLocation->id)
								->where('check_in_at', '>=', now()->format('Y-m-d').' 00:00:00')
								->where('check_in_at', '<=', now()->format('Y-m-d').' 23:59:59')
								->where('check_out_at', null)
								->first();

		if($tracking) {
			return view('employee.tracking.check_out', [
				'title'			=> 'Check Out',
				'trackingLocation' => $trackingLocation,
				'breadcrumbs'	=> [
					[
						'title'	=> 'Lokasi Tracking',
						'link'	=> route('employee.tracking')
					],
					[
						'title'	=> 'Detail Lokasi',
						'link'	=> route('employee.tracking.location_detail', $trackingLocation->id)
					],
					[
						'title'	=> 'Check Out',
						'link'	=> route('employee.tracking.check_out', $trackingLocation->id)
					]
				]
			]);
		} else {
			return redirect()->back();
		}
	}


	public function trackingCheckOutSave(Request $request, TrackingLocation $trackingLocation)
	{
		$request->validate([
			'file_upload_good_receipt' => 'required|file|mimes:jpeg,jpg,png,pdf'
		], [
			'file_upload_good_receipt.mimes' => 'Hanya mendukung ekstensi .jpeg, .jpg, .png, .pdf'
		]);
		
		try {
			DB::beginTransaction();
			$tracking = Tracking::where('id_employee', employee()->id)
								->where('id_tracking_location', $trackingLocation->id)
								->where('check_in_at', '>=', now()->format('Y-m-d').' 00:00:00')
								->where('check_in_at', '<=', now()->format('Y-m-d').' 23:59:59')
								->where('check_out_at', null)
								->first();
			if($tracking) {
				$tracking->saveFileGoodReceipt($request);
				$tracking->saveCheckOutPhoto($request);
				DB::commit();

				return \Res::success([
					'message' => 'Berhasil Check-Out'
				]);
			} else {
				DB::rollback();

				return \Res::invalid([
					'message' => 'Terjadi sesuatu yang tidak normal. Harap muat ulang laman ini'
				]);
			}
		} catch (\Exception $e) {
			DB::rollback();
			return \Res::error($e);
		}
	}
}
