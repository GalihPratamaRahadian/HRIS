<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class UserPermission extends Model
{
	protected $fillable = [ 'id_user', 'menu', 'access_allowed' ];



	/**
	 * 	Relationship methods
	 * */
	public function user()
	{
		return $this->belongsTo('App\User', 'id_user');
	}



	/**
	 * 	Static methods
	 * */
	public static function check($menu, $requiredAccess, $role = 'admin')
	{
		$user = auth()->user();

		if($user->role == $role || $user->role == User::ROLE_DEVELOPER) {
			if(!$user->isRestrictedAccess()) return true;

			$permission = self::where('id_user', auth()->user()->id)
							  ->where('menu', $menu)
							  ->first();
			if($permission)
			{
				return \Str::contains($permission->access_allowed, $requiredAccess);
			}
		}

		return false;
	}


	public static function adminAccessList()
	{
		$accesses = [
			(object) [
				'title'		=> 'Master Karyawan',
				'submenus'	=> [
					(object) [
						'title'		=> 'Department',
						'menu'		=> 'department',
						'access'	=> [
							'no'	=> 'Tidak Diizinkan',
							'r'		=> 'Hanya Lihat',
							'cr'	=> 'Lihat dan Tambah',
							'cru'	=> 'Lihat, Tambah dan Edit',
							'crud'	=> 'Lihat, Tambah, Edit dan Hapus'
						]
					],
					(object) [
						'title'		=> 'Jabatan',
						'menu'		=> 'position',
						'access'	=> [
							'no'	=> 'Tidak Diizinkan',
							'r'		=> 'Hanya Lihat',
							'cr'	=> 'Lihat dan Tambah',
							'cru'	=> 'Lihat, Tambah dan Edit',
							'crud'	=> 'Lihat, Tambah, Edit dan Hapus'
						]
					],
					(object) [
						'title'		=> 'Jam Kerja',
						'menu'		=> 'shift',
						'access'	=> [
							'no'	=> 'Tidak Diizinkan',
							'r'		=> 'Hanya Lihat',
							'cr'	=> 'Lihat dan Tambah',
							'cru'	=> 'Lihat, Tambah dan Edit',
							'crud'	=> 'Lihat, Tambah, Edit dan Hapus'
						]
					],
					(object) [
						'title'		=> 'Grup Karyawan',
						'menu'		=> 'employee_group',
						'access'	=> [
							'no'	=> 'Tidak Diizinkan',
							'r'		=> 'Hanya Lihat',
							'cr'	=> 'Lihat dan Tambah',
							'cru'	=> 'Lihat, Tambah dan Edit',
							'crud'	=> 'Lihat, Tambah, Edit dan Hapus'
						]
					],
					(object) [
						'title'		=> 'Karyawan',
						'menu'		=> 'employee',
						'access'	=> [
							'no'	=> 'Tidak Diizinkan',
							'r'		=> 'Hanya Lihat',
							'cr'	=> 'Lihat dan Tambah',
							'cru'	=> 'Lihat, Tambah dan Edit',
							'crud'	=> 'Lihat, Tambah, Edit dan Hapus'
						]
					],
					(object) [
						'title'		=> 'Gaji Karyawan',
						'menu'		=> 'employee_salary',
						'access'	=> [
							'no'	=> 'Tidak Diizinkan',
							'r'		=> 'Hanya Lihat',
							'cr'	=> 'Lihat dan Tambah',
							'cru'	=> 'Lihat, Tambah dan Edit',
							'crud'	=> 'Lihat, Tambah, Edit dan Hapus'
						]
					],
					(object) [
						'title'		=> 'Alasan Cuti',
						'menu'		=> 'leave_reason',
						'access'	=> [
							'no'	=> 'Tidak Diizinkan',
							'r'		=> 'Hanya Lihat',
							'cr'	=> 'Lihat dan Tambah',
							'cru'	=> 'Lihat, Tambah dan Edit',
							'crud'	=> 'Lihat, Tambah, Edit dan Hapus'
						]
					],
					(object) [
						'title'		=> 'Alasan Sakit',
						'menu'		=> 'sick_reason',
						'access'	=> [
							'no'	=> 'Tidak Diizinkan',
							'r'		=> 'Hanya Lihat',
							'cr'	=> 'Lihat dan Tambah',
							'cru'	=> 'Lihat, Tambah dan Edit',
							'crud'	=> 'Lihat, Tambah, Edit dan Hapus'
						]
					],
					(object) [
						'title'		=> 'Alasan Izin',
						'menu'		=> 'necessity_reason',
						'access'	=> [
							'no'	=> 'Tidak Diizinkan',
							'r'		=> 'Hanya Lihat',
							'cr'	=> 'Lihat dan Tambah',
							'cru'	=> 'Lihat, Tambah dan Edit',
							'crud'	=> 'Lihat, Tambah, Edit dan Hapus'
						]
					],
					(object) [
						'title'		=> 'Alasan Lembur',
						'menu'		=> 'overtime_reason',
						'access'	=> [
							'no'	=> 'Tidak Diizinkan',
							'r'		=> 'Hanya Lihat',
							'cr'	=> 'Lihat dan Tambah',
							'cru'	=> 'Lihat, Tambah dan Edit',
							'crud'	=> 'Lihat, Tambah, Edit dan Hapus'
						]
					],
				]
			],
			(object) [
				'title'		=> 'Setting Karyawan',
				'submenus'	=> [
					(object) [
						'title'		=> 'Kontrak Karyawan',
						'menu'		=> 'employee_contract',
						'access'	=> [
							'no'	=> 'Tidak Diizinkan',
							'r'		=> 'Hanya Lihat',
							'cr'	=> 'Lihat dan Tambah',
							'cru'	=> 'Lihat, Tambah dan Edit',
							'crud'	=> 'Lihat, Tambah, Edit dan Hapus'
						]
					],
					(object) [
						'title'		=> 'Jatah Cuti Karyawan',
						'menu'		=> 'employee_leave_quota',
						'access'	=> [
							'no'	=> 'Tidak Diizinkan',
							'r'		=> 'Hanya Lihat',
							'cr'	=> 'Lihat dan Tambah',
							'cru'	=> 'Lihat, Tambah dan Edit',
							'crud'	=> 'Lihat, Tambah, Edit dan Hapus'
						]
					],
					(object) [
						'title'		=> 'Jadwal Perubahan Jam Kerja',
						'menu'		=> 'employee_shift_change_schedule',
						'access'	=> [
							'no'	=> 'Tidak Diizinkan',
							'r'		=> 'Hanya Lihat',
							'cr'	=> 'Lihat dan Tambah',
							'cru'	=> 'Lihat, Tambah dan Edit',
							'crud'	=> 'Lihat, Tambah, Edit dan Hapus'
						]
					],
					(object) [
						'title'		=> 'Shift Harian',
						'menu'		=> 'unroutine_shift',
						'access'	=> [
							'no'	=> 'Tidak Diizinkan',
							'r'		=> 'Hanya Lihat',
							'cr'	=> 'Lihat dan Tambah',
							'cru'	=> 'Lihat, Tambah dan Edit',
							'crud'	=> 'Lihat, Tambah, Edit dan Hapus'
						]
					]
				]
			],
			(object) [
				'title'		=> 'Cuti & Hari Libur',
				'submenus'	=> [
					(object) [
						'title'		=> 'Cuti',
						'menu'		=> 'employee_leave',
						'access'	=> [
							'no'	=> 'Tidak Diizinkan',
							'r'		=> 'Hanya Lihat',
							'cru'	=> 'Lihat, Tambah dan Edit',
							'crud'	=> 'Lihat, Tambah, Edit dan Hapus'
						]
					],
					(object) [
						'title'		=> 'Hari Libur',
						'menu'		=> 'off_day',
						'access'	=> [
							'no'	=> 'Tidak Diizinkan',
							'r'		=> 'Hanya Lihat',
							'cru'	=> 'Lihat, Tambah dan Edit',
							'crud'	=> 'Lihat, Tambah, Edit dan Hapus'
						]
					],
				]
			]
		];

		if(setting('menu_submission', 'yes') == 'yes') {
			$accesses = array_merge($accesses, [
				(object) [
					'title'		=> 'Pengajuan',
					'submenus'	=> [
						(object) [
							'title'		=> 'Pengajuan Cuti',
							'menu'		=> 'leave_submission',
							'access'	=> [
								'no'	=> 'Tidak Diizinkan',
								'r'		=> 'Hanya Lihat',
								'cr'	=> 'Lihat & Tambah',
								'cra'	=> 'Lihat, Tambah & Penyetujuan',
							]
						],
						(object) [
							'title'		=> 'Pengajuan Izin/Sakit',
							'menu'		=> 'sick_necessity_submission',
							'access'	=> [
								'no'	=> 'Tidak Diizinkan',
								'r'		=> 'Hanya Lihat',
								'cr'	=> 'Lihat & Tambah',
								'cra'	=> 'Lihat, Tambah & Penyetujuan',
							]
						],
						(object) [
							'title'		=> 'Pengajuan Lembur',
							'menu'		=> 'overtime_submission',
							'access'	=> [
								'no'	=> 'Tidak Diizinkan',
								'r'		=> 'Hanya Lihat',
								'cr'	=> 'Lihat & Tambah',
								'cra'	=> 'Lihat, Tambah & Penyetujuan',
							]
						],
						(object) [
							'title'		=> 'Pengajuan Izin Terlambat / Pulang Cepat',
							'menu'		=> 'attendance_permission_submission',
							'access'	=> [
								'no'	=> 'Tidak Diizinkan',
								'r'		=> 'Hanya Lihat',
								'cr'	=> 'Lihat & Tambah',
								'cra'	=> 'Lihat, Tambah & Penyetujuan',
							]
						],
						(object) [
							'title'		=> 'Rekap Cuti',
							'menu'		=> 'leave_resume',
							'access'	=> [
								'r'		=> 'Lihat & Buat Rekap'
							]
						],
						(object) [
							'title'		=> 'Rekap Sakit/Izin',
							'menu'		=> 'sick_necessity_resume',
							'access'	=> [
								'r'		=> 'Lihat & Buat Rekap'
							]
						],
						(object) [
							'title'		=> 'Rekap Lembur',
							'menu'		=> 'overtime_resume',
							'access'	=> [
								'r'		=> 'Lihat & Buat Rekap'
							]
						],
					]
				]
			]);
		}

		$accesses = array_merge($accesses, [
			(object) [
				'title'		=> 'Penggajian',
				'submenus'	=> [
					(object) [
						'title'		=> 'Penggajian',
						'menu'		=> 'payroll',
						'access'	=> [
							'no'	=> 'Tidak Diizinkan',
							'r'		=> 'Hanya Lihat',
							'cru'	=> 'Lihat dan Membuat',
							'crud'	=> 'Lihat, Membuat dan Hapus'
						]
					],
					(object) [
						'title'		=> 'Slip Gaji',
						'menu'		=> 'salary_slip',
						'access'	=> [
							'no'	=> 'Tidak Diizinkan',
							'r'		=> 'Hanya Lihat',
							'cru'	=> 'Lihat dan Membuat',
							'crud'	=> 'Lihat, Membuat dan Hapus'
						]
					],
					(object) [
						'title'		=> 'Rekap Payroll',
						'menu'		=> 'payroll_resume',
						'access'	=> [
							'no'	=> 'Tidak Diizinkan',
							'r'		=> 'Membuat Rekap',
						]
					]
				]
			],
			(object) [
				'title'		=> 'Kehadiran',
				'submenus'	=> [
					(object) [
						'title'		=> 'Kehadiran',
						'menu'		=> 'attendance',
						'access'	=> [
							'no'	=> 'Tidak Diizinkan',
							'r'		=> 'Hanya Lihat',
							'cru'	=> 'Lihat dan Edit',
							'crud'	=> 'Lihat, Edit dan Hapus'
						]
					]
				]
			],
			(object) [
				'title'		=> 'WFH / Kerja Diluar Kantor',
				'submenus'	=> [
					(object) [
						'title'		=> 'Master Lokasi',
						'menu'		=> 'attendance_location_rules',
						'access'	=> [
							'no'	=> 'Tidak Diizinkan',
							'r'		=> 'Hanya Lihat',
							'cru'	=> 'Lihat, Tambah dan Edit',
							'crud'	=> 'Lihat, Tambah, Edit dan Hapus'
						]
					],
					(object) [
						'title'		=> 'Setting WFH',
						'menu'		=> 'web_attendance_permissions',
						'access'	=> [
							'no'	=> 'Tidak Diizinkan',
							'r'		=> 'Hanya Lihat',
							'cru'	=> 'Lihat, Tambah dan Edit',
							'crud'	=> 'Lihat, Tambah, Edit dan Hapus'
						]
					]
				]
			]
		]);


		if(setting('menu_registration', 'yes') == 'yes') {
			$accesses = array_merge($accesses, [
				(object) [
					'title'		=> 'Pendaftaran Karyawan',
					'submenus'	=> [
						(object) [
							'title'		=> 'Pendaftaran Karyawan',
							'menu'		=> 'registration',
							'access'	=> [
								'no'	=> 'Tidak Diizinkan',
								'r'		=> 'Hanya Lihat',
								'rud'	=> 'Lihat, Rotasi Foto dan Hapus',
								'ruda'	=> 'Lihat, Rotasi Foto, Hapus dan Approval'
							]
						]
					]
				]
			]);
		}


		if(setting('menu_announcement', 'yes') == 'yes') {
			$accesses = array_merge($accesses, [
				(object) [
					'title'		=> 'Pengumuman',
					'submenus'	=> [
						(object) [
							'title'		=> 'Pengumuman',
							'menu'		=> 'announcement',
							'access'	=> [
								'no'	=> 'Tidak Diizinkan',
								'r'		=> 'Hanya Lihat',
								'cr'	=> 'Lihat dan Tambah',
								'cru'	=> 'Lihat, Tambah dan Edit',
								'crud'	=> 'Lihat, Tambah, Edit dan Hapus'
							]
						]
					]
				]
			]);
		}

		if(setting('menu_warning_letter', 'yes') == 'yes') {
			$accesses = array_merge($accesses, [
				(object) [
					'title'		=> 'Surat Peringatan',
					'submenus'	=> [
						(object) [
							'title'		=> 'Surat Peringatan',
							'menu'		=> 'warning_letter',
							'access'	=> [
								'no'	=> 'Tidak Diizinkan',
								'r'		=> 'Hanya Lihat',
								'cr'	=> 'Lihat dan Tambah',
								'cru'	=> 'Lihat, Tambah dan Edit',
								'crud'	=> 'Lihat, Tambah, Edit dan Hapus'
							]
						]
					]
				]
			]);
		}

		if(setting('menu_elearning', 'yes') == 'yes') {
			$accesses = array_merge($accesses, [
				(object) [
					'title'		=> 'E-Learning',
					'submenus'	=> [
						(object) [
							'title'		=> 'Course',
							'menu'		=> 'course',
							'access'	=> [
								'no'	=> 'Tidak Diizinkan',
								'r'		=> 'Lihat dan Komentar',
								'cru'	=> 'Lihat, Komentar, Tambah dan Edit',
								'crud'	=> 'Lihat, Komentar, Tambah, Edit dan Hapus'
							]
						],
						(object) [
							'title'		=> 'Exam',
							'menu'		=> 'course_exam',
							'access'	=> [
								'no'	=> 'Tidak Diizinkan',
								'r'		=> 'Hanya Lihat',
								'cru'	=> 'Lihat, Tambah dan Edit',
								'crud'	=> 'Lihat, Tambah, Edit dan Hapus'
							]
						],
						(object) [
							'title'		=> 'Hasil Course',
							'menu'		=> 'course_result',
							'access'	=> [
								'no'	=> 'Tidak Diizinkan',
								'r'		=> 'Hanya Lihat'
							]
						],
						(object) [
							'title'		=> 'Riwayat Exam',
							'menu'		=> 'course_exam_history',
							'access'	=> [
								'no'	=> 'Tidak Diizinkan',
								'r'		=> 'Hanya Lihat',
							]
						]
					]
				]
			]);
		}

		if(setting('menu_face_terminal_log', 'yes') == 'yes') {
			$accesses = array_merge($accesses, [
				(object) [
					'title'		=> 'Log Face Terminal',
					'submenus'	=> [
						(object) [
							'title'		=> 'Log Face Terminal',
							'menu'		=> 'face_terminal_log',
							'access'	=> [
								'no'	=> 'Tidak Diizinkan',
								'r'		=> 'Hanya Lihat'
							]
						]
					]
				]
			]);
		}

		if(setting('menu_face_terminal_device', 'yes') == 'yes') {
			$accesses = array_merge($accesses, [
				(object) [
					'title'		=> 'Device Face Terminal',
					'submenus'	=> [
						(object) [
							'title'		=> 'Device Face Terminal',
							'menu'		=> 'face_terminal_device',
							'access'	=> [
								'no'	=> 'Tidak Diizinkan',
								'r'		=> 'Hanya Lihat',
								'cru'	=> 'Lihat, Tambah dan Edit',
								'crud'	=> 'Lihat, Tambah, Edit dan Hapus'
							]
						]
					]
				]
			]);
		}


		$accesses = array_merge($accesses, [
			(object) [
				'title'		=> 'User',
				'submenus'	=> [
					(object) [
						'title'		=> 'User',
						'menu'		=> 'user',
						'access'	=> [
							'no'	=> 'Tidak Diizinkan',
							'r'		=> 'Hanya Lihat',
							'cru'	=> 'Lihat, Tambah dan Edit',
							'crud'	=> 'Lihat, Tambah, Edit dan Hapus'
						]
					]
				]
			]
		]);


		if(setting('menu_tracking', 'yes') == 'yes') {
			$accesses = array_merge($accesses, [
				(object) [
					'title'		=> 'Tracking',
					'submenus'	=> [
						(object) [
							'title'		=> 'Lokasi Tracking',
							'menu'		=> 'tracking_location',
							'access'	=> [
								'no'	=> 'Tidak Diizinkan',
								'r'		=> 'Hanya Lihat',
								'cru'	=> 'Lihat, Tambah dan Edit',
								'crud'	=> 'Lihat, Tambah, Edit dan Hapus'
							]
						],
						(object) [
							'title'		=> 'Karyawan Yg Di Tracking',
							'menu'		=> 'tracking_employee',
							'access'	=> [
								'no'	=> 'Tidak Diizinkan',
								'r'		=> 'Hanya Lihat',
								'cr'	=> 'Lihat dan Tambah',
								'crd'	=> 'Lihat, Tambah dan Hapus'
							]
						],
						(object) [
							'title'		=> 'Hasil Tracking',
							'menu'		=> 'tracking',
							'access'	=> [
								'no'	=> 'Tidak Diizinkan',
								'r'		=> 'Hanya Lihat'
							]
						]
					]
				],
			]);
		}

		if(setting('menu_advance', 'yes') == 'yes') {
			$accesses = array_merge($accesses, [
				(object) [
					'title'		=> 'Lanjutan',
					'submenus'	=> [
						(object) [
							'title'		=> 'Komparasi Wajah',
							'menu'		=> 'face_compare',
							'access'	=> [
								'no'	=> 'Tidak Diizinkan',
								'r'		=> 'Izinkan Penggunaan',
							]
						],
						(object) [
							'title'		=> 'Kirim Pesan Ke Karyawan',
							'menu'		=> 'send_message_to_employee',
							'access'	=> [
								'no'	=> 'Tidak Diizinkan',
								'r'		=> 'Izinkan Penggunaan',
							]
						]
					]
				],
			]);
		}

		return $accesses;
	}
}
