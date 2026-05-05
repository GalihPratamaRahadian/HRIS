<?php 

namespace App\MyClass;

class HrisApiValidation
{

	public static function validateAttendance($request)
	{
		$request->validate([
			'start_date' => 'required|date',
			'end_date'	=> 'required|date|after_or_equal:start_date',
		], [
			'start_date.required'	=> 'Harap masukan tanggal awal',
			'start_date.date'		=> 'Tanggal awal wajib tanggal valid',
			'end_date.required'		=> 'Harap masukan tanggal akhir',
			'end_date.date'			=> 'Tanggal akhir wajib tanggal valid',
			'end_date.after_or_equal' => 'Tanggal akhir harus sama atau lebih dari tanggal awal',
		]);
	}
}