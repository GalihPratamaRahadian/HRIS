<?php

namespace App\MyClass;

class StaffCommandCentre
{

	public static function command($command)
	{
		$command = strtolower($command);

		if(\Str::startsWith($command, '#menu')) {
			return self::toMenu($command);
		}
	}

	public static function toMenu($command)
	{
		$result = "*Menu Staff Command Centre*
		\n1. Cek Data Pelanggan
		\n2. Cek Pemasangan Terpending
		\n0. Keluar Menu";

		return $result;
	}
}