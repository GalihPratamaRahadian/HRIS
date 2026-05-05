<?php 

namespace App\MyClass;

use App\Kehadiran;
use App\Karyawan;
use App\Libur;

class Staff
{

	/**
	*	Cek status libur hari untuk seorang karyawan
	*	@param Int $karyawanId => ID Karyawan
	*	@param (Opt) String $tgl => Tgl Hari (Format : Y-m-d), default hari ini
	* 	@return Object $result;
	*	- Boolean status => Status Libur
	*	- String msg => Pesan
	* 
	*/
	public static function isLibur($karyawanId, $tgl = null)
	{
		return Staff::analizeLibur($karyawanId, $tgl)->status;
	}

	public static function analizeLibur($karyawanId, $tgl = null)
	{
		// Default
		$tgl = isset($tgl) || $tgl != null ? date('Y-m-d', strtotime($tgl)) : date('Y-m-d');

		$result = null;
		if(Karyawan::exists($karyawanId))
		{
			$karyawan = Karyawan::find($karyawanId);
			if(!empty($karyawan->karyawanShift))
			{
				if(!empty($karyawan->karyawanShift->shift))
				{
					$hari = date('N', strtotime($tgl));
					// Cek libur serentak
					$liburSerentak = Libur::where('awal_libur', '<=', $tgl)
											->where('akhir_libur', '>=', $tgl)
											->first();
					if(!empty($liburSerentak))
					{
						// Libur karena event tertentu
						$result = [
							'status'	=> true,
							'msg'		=> 'Hari ini sedang libur '.$liburSerentak->nama_libur,
						];
					}
					else
					{
						// Cek libur mingguan
						$libur = $karyawan->karyawanShift->shift->hari_libur;
						if(preg_match("/$hari/i", $libur))
						{
							// Libur sesuai jadwal
							$result = [
								'status'	=> true,
								'msg'		=> 'Hari ini sedang libur'
							];
						}
						else
						{
							// Sedang tidak libur
							$result = [
								'status'	=> false,
								'msg'		=> 'Hari ini tidak libur'
							];
						}
					}

				}
				else
				{
					// Data shift tidak ditemukan
					$result = [
						'status'	=> false,
						'msg'		=> 'Data shift tidak ditemukan',
					];
				}
			}
			else
			{
				// Karyawan belum disetting shift
				$result = [
					'status'	=> false,
					'msg'		=> 'Karyawan belum setting shift',
				];
			}
		}
		else
		{
			// Data karyawan tidak ada
			$result = [
				'status'	=> false,
				'msg'		=> 'Data karyawan tidak ditemukan',
			];
		}

		return (object) $result;
	}



	public static function hasShift($karyawanId)
	{
		if(Karyawan::exists($karyawanId))
		{
			$karyawan = Karyawan::find($karyawanId);
			if(!empty($karyawan->karyawanShift))
			{
				if(!empty($karyawan->karyawanShift->shift))
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}


	public static function getShiftMaster($karyawanId)
	{
		if(Staff::hasShift($karyawanId))
		{
			$karyawan = Karyawan::find($karyawanId);
			return $karyawan->karyawanShift->shift;
		}
		else
		{
			return false;
		}
	}


	public static function getShift($karyawanId, $tgl = null)
	{
		$tgl = isset($tgl) || $tgl != null ? date('Y-m-d', strtotime($tgl)) : date('Y-m-d');
		if(Staff::hasShift($karyawanId))
		{
			$shift = Staff::getShiftMaster($karyawanId);
			if(count($shift->addon) > 0)
			{
				foreach($shift->addon as $addon)
				{
					if($addon->hari == date('N', strtotime($date)))
					{
						$shift->jam_mulai = $addon->jam_mulai;
						$shift->jam_selesai = $addon->jam_selesai;
					}
				}
			}
			unset($shift->addon);
			return $shift;
		}
		else
		{
			return false;
		}
	}


	public static function getShiftHariIni($karyawanId)
	{
		if(Staff::hasShift($karyawanId))
		{
			$shift = Staff::getShiftMaster($karyawanId);
			if(count($shift->addon) > 0)
			{
				foreach($shift->addon as $addon)
				{
					if($addon->hari == date('N'))
					{
						$shift->jam_mulai = $addon->jam_mulai;
						$shift->jam_selesai = $addon->jam_selesai;
					}
				}
			}
			unset($shift->addon);
			return $shift;
		}
		else
		{
			return false;
		}
	}

	public static function getMenitKerja($karyawanId, $tgl = null)
	{
		$tgl = isset($tgl) || $tgl != null ? date('Y-m-d', strtotime($tgl)) : date('Y-m-d');
		if(Staff::hasShift($karyawanId))
		{
			if(Staff::isLibur($karyawanId, $tgl))
			{
				// Libur (Bukan hari aktif)
				return 0;
			}
			else
			{
				$shift = Staff::getShift($karyawanId, $tgl);
				$menitKerja = floor((strtotime($shift->jam_selesai) - strtotime($shift->jam_mulai))/60);

				return $menitKerja;
			}
		}
		else
		{
			// Tidak memiliki shift
			return 0;
		}
	}


	public static function getMenitKehadiran($karyawanId)
	{
		if(!empty(Staff::getHadir($karyawanId)))
		{
			$shift = Staff::getShiftHariIni($karyawanId);
			$hadir = Staff::getHadir($karyawanId);
			if($hadir->lembur == 'Y' || empty($shift))
			{
				$jamMasuk = $hadir->jam_masuk;
			}
			else
			{
				$jamMasuk = $hadir->jam_masuk <= $shift->jam_mulai? $shift->jam_mulai : $hadir->jam_masuk;
			}

			$jamKeluar = $hadir->jam_keluar != null? $hadir->jam_keluar : date('H:i:s');
			
			$menitKerja = floor((strtotime($jamKeluar) - strtotime($jamMasuk))/60);

			return $menitKerja;
		}
		else
		{
			// Tidak ada kehadiran
			return 0;
		}
	}







	/**
	*	Untuk Cek Bisa Clockout atau tidak
	*	@param Int $karyawanId => ID Karyawan
	*	@return Boolean
	*/
	public static function isBolehClockOut($karyawanId)
	{
		return Staff::analizeBolehClockOut($karyawanId)->status;
	}

	public static function msgBolehClockOut($karyawanId)
	{
		return Staff::analizeBolehClockOut($karyawanId)->msg;
	}

	public static function analizeBolehClockOut($karyawanId)
	{
		$result = null;

		// Cek Karyawan
		if(Karyawan::exists($karyawanId))
		{
			// Cek Kehadiran
			$paramCheck = [
				'karyawan_id'   => $karyawanId, 
				'tgl_absensi'   => date('Y-m-d'),
				'keterangan'	=> 'hadir',
				'lembur'		=> 'N',
				'jam_keluar'	=> null,
			];

			if(Kehadiran::exists($paramCheck))
			{
				// Cek kesediaan shift
				if(Staff::hasShift($karyawanId))
				{
					// Ambil data shift
					$shift = Staff::getShiftHariIni($karyawanId);
					$jamKeluar = $shift->jam_selesai;

					// Cek sudah jam keluar atau belum
					if(date('H:i:s') >= $jamKeluar)
					{
						// Boleh keluar
						$result = [
							'status'	=> true,
							'msg'		=> 'Boleh keluar',
						];
					}
					else
					{
						// Belum boleh keluar
						$result = [
							'status'	=> false,
							'tolerance'	=> true,
							'msg'		=> 'Belum waktunya jam keluar',
						];
					}
				}
				else
				{
					// Tidak punya shift boleh keluar kapan saja
					$result = [
						'status'	=> true,
						'msg'		=> 'Tidak punya shift boleh keluar kapan saja',
					];
				}
			}
			else
			{
				// Cek Lembur
				$paramCheck = [
					'karyawan_id'   => $karyawanId, 
					'tgl_absensi'   => date('Y-m-d'),
					'keterangan'	=> 'hadir',
					'lembur'		=> 'Y',
					'jam_keluar'	=> null,
				];

				if(Kehadiran::exists($paramCheck))
				{
					// Lembur tidak mengikuti waktu shift
					$result = [
						'status'	=> true,
						'msg'		=> 'Lembur tidak mengikuti waktu shift',
					];
				}
				else
				{
					// Tidak ada kehadiran yang belum clockout
					$result = [
						'status'	=> false,
						'tolerance'	=> false,
						'msg'		=> 'Tidak ada kehadiran yang belum clockout',
					];
				}
			}
		}
		else
		{
			// Data karyawan tidak ditemukan
			$result = [
				'status'	=> false,
				'tolerance'	=> false,
				'msg'		=> 'Data karyawan tidak ditemukan',
			];
		}

		return (object) $result;
	}


	public static function isHadir($karyawanId){}

	public static function getLibur($karyawanId){}
	public static function getJadwal($karyawanId){}


	public static function getHariKerja($karyawanId, $bulan = null, $tahun = null)
	{
		// Default
		$bulan = isset($bulan) ? date('m', strtotime($bulan)) : date('m');
		$tahun = isset($tahun) ? date('Y', strtotime($tahun)) : date('Y');

		if(Karyawan::exists($karyawanId))
		{
			// Karyawan ditemukan
			$tglAkhir = date('Y-m-t', strtotime("$tahun-$bulan-01"));
			$hariKerja = Staff::getHariKerjaByDaterange($karyawanId, date("Y-m-d", strtotime("$tahun-$bulan-01")), $tglAkhir);

			return $hariKerja;
		}
		else
		{
			// Data karyawan tidak ditemukan
			return 0;
		}
	}



	public static function getHariKerjaByDaterange($karyawanId, $tglAwal, $tglAkhir)
	{
		if(Karyawan::exists($karyawanId))
		{
			// Karyawan ditemukan
			if($tglAwal > $tglAkhir)
			{
				// Jika terbalik
				$tgl = $tglAkhir;
				$tglAkhir = $tglAwal;
				$tglAwal = $tgl;
			}

			$tglAwal = date('Y-m-d', strtotime($tglAwal));
			$tglAkhir = date('Y-m-d', strtotime($tglAkhir));

			$hariKerja = 0;
			$tgl = $tglAwal;
			while($tgl <= $tglAkhir)
			{
				if(!Staff::isLibur($karyawanId, date('Y-m-d', strtotime($tgl))))
				{
					$hariKerja++;
				}
				$tgl = date('Y-m-d', strtotime($tgl." +1 day"));
			}

			return $hariKerja;
		}
		else
		{
			// Data karyawan tidak ditemukan
			return 0;
		}
	}



	public static function hasGaji($karyawanId)
	{
		// Cek Data Karyawan
		if(Karyawan::exists($karyawanId))
		{
			$karyawan = Karyawan::find($karyawanId);
			// Cek Gaji
			if(!empty($karyawan->gaji))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			// Data karyawan tidak ditemukan
			return false;
		}
	}


	public static function getGaji($karyawanId)
	{
		if(Staff::hasGaji($karyawanId))
		{
			$karyawan = Karyawan::find($karyawanId);
			return $karyawan->gaji;
		}
		else
		{
			return false;
		}
	}


	public static function getGajiHarian($karyawanId)
	{
		if(Staff::hasGaji($karyawanId))
		{
			$karyawan = Karyawan::find($karyawanId);
			$gajiHarian = (int) $karyawan->gaji->gaji_pokok / Staff::getHariKerja($karyawanId);
			return $gajiHarian;
		}
		else
		{
			return false;
		}
	}


	public static function getHadir($karyawanId, $tgl = null)
	{
		return Staff::getDataKehadiran($karyawanId, 'hadir', $tgl);
	}

	public static function getSakit($karyawanId, $tgl = null)
	{
		return Staff::getDataKehadiran($karyawanId, 'sakit', $tgl);
	}

	public static function getCuti($karyawanId, $tgl = null)
	{
		return Staff::getDataKehadiran($karyawanId, 'cuti', $tgl);
	}

	public static function getIzin($karyawanId, $tgl = null)
	{
		return Staff::getDataKehadiran($karyawanId, 'izin', $tgl);
	}

	public static function getLembur($karyawanId, $tgl = null)
	{
		return $this->getDataKehadiran($karyawanId, 'lembur', $tgl);
	}


	private static function getDataKehadiran($karyawanId, $keterangan, $tgl = null)
	{
		// Default
		$tgl = isset($tgl) || $tgl != null ? date('Y-m-d', strtotime($tgl)) : date('Y-m-d');

		if(Karyawan::exists($karyawanId))
		{
			$paramCheck = [
				'karyawan_id'	=> $karyawanId,
				'tgl_absensi'	=> $tgl,
				'keterangan'	=> $keterangan,
			];
			if(Kehadiran::exists($paramCheck))
			{
				return Kehadiran::where($paramCheck)
								->orderBy('created_at', 'desc')
								->first();
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}


}