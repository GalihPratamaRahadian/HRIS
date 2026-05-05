<?php

/**
 *	Develop by Rohim Wahyudin (adiva)
 *	Helper function
 *	-> Mengambil setting
 * 	-> Menyimpan setting
 * 	-> Mengambil data employee untuk user karyawan
 * 	-> Mengambil data config
 * 
 *	@method any|null setting(string $key, any|null $default) untuk mengambil setting
 *	@method Setting setting(string $key, any $default) untuk menyimpan setting
 * 	@method Employee employee() untuk mengambil setting untuk user karyawan
 * 	@method any|null appconfig(string $key, any|null $default) untuk mengambil config
 * */


	function setting($key, $default = null)
	{
		return \Setting::getValue($key, $default);
	}

	function saveSetting($key, $value)
	{
		return \Setting::setValue($key, $value);
	}

	function employee()
	{
		return \App\Models\Employee::where('id_user', auth()->user()->id)->first();
	}

	function unreadNotifications($take = 0)
	{
		$notification = \App\Models\Notification::where('id_user', auth()->user()->id)
						->where('is_read', 'no')
						->orderBy('created_at', 'desc');

		if($take > 0) {
			$notification = $notification->take($take);
		}

		return $notification->get();
	}

	function amountOfUnreadNotifications($take = 0)
	{
		$notification = \App\Models\Notification::where('id_user', auth()->user()->id)
						->where('is_read', 'no');

		return $notification->count();
	}


	/**
	 * 	Get App config
	 * */
	function appconfig($key, $default = null)
	{
		$config = config('appconfig');
		if(array_key_exists($key, $config)) return $config[$key];
		return $default;
	}


	/**
	 * 	Make location instance
	 * 	@see App\MyClass\Location
	 * */
	function location($latitude, $longitude)
	{
		return \App\MyClass\Location::make($latitude, $longitude);
	}


	function user()
	{
		return auth()->user();
	}

?>