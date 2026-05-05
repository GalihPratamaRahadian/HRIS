<?php 

return [

	/**
	 * 	Developer Info
	 * 	@see Untuk info tentang developer
	 * 	@see Biasanya ditempatkan difooter aplikasi
	 * */
	'developer_name'	=> 'PT. Adiva Sumber Solusi',
	'developer_url'		=> 'https://adiva.co.id',


	/**
	 * 	Menggunakan Temperatur
	 * */
	'using_temperature'	=> true,


	/**
	 * 	Limit Karyawan
	 * */
	'employee_limit' => 1000,


	/**
	 * 	Proxy
	 * 	@see Konfigurasi proxy
	 * 	@see Biasanya kalo server ditempatkan di server sendiri (selain dicpanel)
	 * 	@see Mengaktif/nonaktifkan cukup dengan memberi nilai true/false di is_using_proxy
	 * */
	'is_using_proxy'	=> false,
	'proxy_url'			=> 'https://faceterminal.adiva.co.id',
	'proxy_schema'		=> 'https',


	/**
	 * 	Face Compare
	 * 	@see Fitur untuk komparasi wajah ketika absensi lewat smartphone/laptop melalui web
	 * 	@see Fitur ini memanfaatkan fitur face compare dari faceterminal china
	 * 	@see Mengaktif/nonaktifkan fitur dengan memberi nilai true/false di is_using_face_compare
	 * */
	'face_compare_url'		=> 'http://103.242.105.85:61788',
	'face_compare_username'	=> 'admin',
	'face_compare_password'	=> 'admin',


	/**
	 * 	Pengisian jam keluar otomatis
	 * 	@see Fitur pengisian jam keluar otomatis jika karyawan lupa isi jam keluar
	 * 	@see Mengaktif/nonaktifkan fitur dengan memberi nilai true/false di is_using_autofill_clock_out
	 * 	@see autofill_method tersedia beberapa opsi :
	 *       shift_clock_end => jam keluar diambil dari jam akhir shift
	 *       last_log => jam keluar diambil dari log terakhir deteksi dari faceterminal
	 * */
	'is_using_autofill_clock_out'	=> false,
	'autofill_method' 				=> 'shift_clock_end',



	/**
	 * 	Menu Aplikasi
	 * */
	'menu_submission'	=> true,

];