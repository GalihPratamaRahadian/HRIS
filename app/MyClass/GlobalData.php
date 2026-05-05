<?php 

namespace App\MyClass;

class GlobalData
{

	public static function salaryPercentForEmployeeLeave()
	{
		return [
			'100'	=> 'Tidak Potong Gaji',
			'75'	=> 'Potong Gaji Harian 25%',
			'50'	=> 'Potong Gaji Harian 50%',
			'25'	=> 'Potong Gaji Harian 75%',
			'0'		=> 'Potong Gaji Harian',
		];
	}


	public static function imageExtensions()
	{
		return [ 'png', 'jpeg', 'jpg', 'gif', 'tiff' ];
	}


	public static function educationLevel()
	{
		return [
			'Tidak/Belum Sekolah',
			'Tidak Tamat SD/Sederajat',
			'SD/Sederajat',
			'SLTP/Sederajat',
			'SLTA/Sederajat',
			'Diploma I',
			'Diploma II',
			'Akademi',
			'Diploma III',
			'Diploma IV',
			'S1',
			'S2',
			'S3',
		];
	}

	public static function educationLevelAlt()
	{
		return [
			'SD',
			'SLTP',
			'SLTA',
			'Diploma I',
			'Diploma II',
			'Akademi',
			'Diploma III',
			'Diploma IV',
			'S1',
			'S2',
			'S3',
		];
	}

	public static function maritalStatus()
	{
		return [
			'Belum Kawin',
			'Kawin',
			'Cerai Hidup',
			'Cerai Mati',
		];
	}

	public static function bloodType()
	{
		return [
			'A', 'B', 'AB', 'O'
		];
	}

	public static function relationshipStatus()
	{
		return [
			'Suami', 'Istri', 'Anak', 'Menantu', 'Cucu', 'Orang Tua', 'Mertua', 'Famili Lain', 'Pembantu'
		];
	}

}