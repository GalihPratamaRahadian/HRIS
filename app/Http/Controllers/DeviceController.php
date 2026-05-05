<?php

namespace App\Http\Controllers;

use App\Models\FaceTerminalDevice;
use Illuminate\Http\Request;
use App\MyClass\Validations;
use DB;

class DeviceController extends Controller
{


	/**
	*       FACETERMINAL DEVICE
	*  
	*/
	public function faceTerminalDeviceIndex(Request $request)
	{
		if($request->ajax()) {
			return FaceTerminalDevice::apiDT();
		}

		return view('admin.device.faceTerminalDeviceIndex', [
			'title'			=> 'Device Face Terminal',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Device Face Terminal',
					'link'	=> route('face_terminal_device')
				],
			]
		]);
	}


	public function faceTerminalDeviceCreate()
	{
		return view('admin.device.faceTerminalDeviceCreate', [
			'title'			=> 'Tambah Device Face Terminal',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Device Face Terminal',
					'link'	=> route('face_terminal_device')
				],
				[
					'title'	=> 'Tambah',
					'link'	=> route('face_terminal_device.create')
				],
			]
		]);
	}


	public function faceTerminalDeviceStore(Request $request)
	{
		Validations::validateFaceTerminalDevice($request);
		DB::beginTransaction();

		try {
			if(FaceTerminalDevice::isDeviceReachLimit()) {
				return \Setting::invalidResponse([
					'message'	=> 'Jumlah device telah mencapai batas',
				]);
			}

			FaceTerminalDevice::createFaceTerminalDevice($request);
			DB::commit();

			return \Res::save();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	public function faceTerminalDeviceEdit(FaceTerminalDevice $faceTerminalDevice)
	{
		return view('admin.device.faceTerminalDeviceEdit', [
			'title'				=> 'Edit Device Face Terminal',
			'faceTerminalDevice'=> $faceTerminalDevice,
			'breadcrumbs'		=> [
				[
					'title'	=> 'Device Face Terminal',
					'link'	=> route('face_terminal_device')
				],
				[
					'title'	=> 'Edit',
					'link'	=> route('face_terminal_device.edit', $faceTerminalDevice->id)
				],
			]
		]);
	}


	public function faceTerminalDeviceUpdate(Request $request, FaceTerminalDevice $faceTerminalDevice)
	{
		Validations::validateFaceTerminalDevice($request);
		DB::beginTransaction();

		try {
			$faceTerminalDevice->updateFaceTerminalDevice($request);
			DB::commit();

			return \Res::update();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	public function faceTerminalDeviceDestroy(FaceTerminalDevice $faceTerminalDevice)
	{
		DB::beginTransaction();

		try {
			$faceTerminalDevice->deleteFaceTerminalDevice();
			DB::commit();

			return \Res::delete();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}
}
