<?php 

/**
 *	Develop by Rohim Wahyudin (adiva)
 *	Helper function
 *	-> Mengeluarkan response
 * 
 *	@static error (Exception $e) untuk mengeluarkan respon error dengan code 500
 *	@static success (array|null $array) untuk mengeluarkan respon success code 200
 *	@static save (array|null $array) untuk mengeluarkan respon tersimpan code 200
 *	@static update (array|null $array) untuk mengeluarkan respon terupdate code 200
 *	@static delete (array|null $array) untuk mengeluarkan respon terhapus code 200
 *	@static invalid (array|null $array) untuk mengeluarkan respon invalid code 422
 * */

namespace App\MyClass;

class Response
{

	public static function error($e)
	{
		return response()->json([
			'message'	=> "{$e->getFile()} : {$e->getLine()} {$e->getMessage()}",
			'trace'		=> $e->getTraceAsString()
		], 500);
	}


	public static function success($array = [])
	{
		if(!array_key_exists('message', $array)) {
			$array['message'] = 'Berhasil';
		}

		return response()->json(array_merge($array, [
			'code'		=> 200,
		]));
	}


	public static function save($array = [])
	{
		return response()->json(array_merge($array, [
			'message'	=> 'Berhasil disimpan',
			'code'		=> 200,
		]));
	}


	public static function update($array = [])
	{
		return response()->json(array_merge($array, [
			'message'	=> 'Berhasil diupdate',
			'code'		=> 200,
		]));
	}


	public static function delete($array = [])
	{
		return response()->json(array_merge($array, [
			'message'	=> 'Berhasil dihapus',
			'code'		=> 200,
		]));
	}


	public static function invalid($array = [])
	{
		if(!array_key_exists('message', $array)) {
			$array['message'] = 'Tidak valid';
		}

		return response()->json(array_merge($array, [
			'code'		=> 422,
		]), 422);
	}
}