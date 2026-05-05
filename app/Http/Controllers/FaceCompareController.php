<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\FaceTerminalDevice;
use App\Models\Employee;
use DB;

class FaceCompareController extends Controller
{

	public function index()
	{
		return view('admin.face_compare.index', [
			'title'			=> 'Komparasi Wajah',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Lanjutan',
					'link'	=> 'javascript:void(0);',
				],
				[
					'title'	=> 'Komparasi Wajah',
					'link'	=> route('face_compare')
				]
			]
		]);
	}
	

	public function compare(Request $request)
	{
		try {
			set_time_limit(0);
			$file = $request->file('photo');
			$filename = 'compare_'.date('YmdHis').'.'.$file->getClientOriginalExtension();
			$path = \App\MyClass\Helper::tempsPath();
			$file->move($path, $filename);
			$filepath = $path.$filename;

			$results = [];
			foreach(Employee::getActiveEmployees() as $employee) {
				$similarity = FaceTerminalDevice::faceCompare($filepath, $employee->photoPath('face'));

				if($similarity >= 50.0) {
					$results[] = [
						'id'			=> $employee->id,
						'employee_name'	=> $employee->employee_name,
						'similarity'	=> $similarity,
						'photo_link'	=> $employee->photoLink('face'),
					];
				}
			}

			usort($results, function($a, $b) {
			    return $a['similarity'] <=> $b['similarity'];
			});
			$results = array_reverse($results);

			return \Res::success([
				'results'	=> $results
			]);
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}
}
