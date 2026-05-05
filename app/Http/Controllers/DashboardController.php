<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Employee;
use App\Models\Department;
use App\Models\Attendance;

use App\MyClass\Staff;

use Yajra\Datatables\Datatables;

class DashboardController extends Controller
{
	public function index()
	{
		$user = auth()->user();

		if($user->isHrd()) return $this->adminDashboard();
		if($user->isHse()) return view('dashboard.hse');
		if($user->isFrontSecurity()) return view('dashboard.frontSecurity');
		if($user->isBackSecurity()) return view('dashboard.backSecurity');
		if($user->isAdmin()) return $this->adminDashboard();
		if($user->isStaff()) return $this->employeeDashboard();
		if($user->isRegistrant()) return $this->registrantDashboard();

		return $this->adminDashboard();
	}


	private function adminDashboard()
	{
		$employeeWithContractIsAlmostOver = Employee::getEmployeesWithContractIsAlmostOver();
		$employeeWithDoesntHaveContract = Employee::getEmployeesWithDoesntHaveContract();
		$todayAttendances = \App\Models\Attendance::todayAttendance();
		$waitingForClockInEmployees = [];

		foreach(Employee::getActiveEmployees() as $employee) {
			if($employee->isAllowForClockIn()) {
				$waitingForClockInEmployees[] = $employee;
			}
		}


		return view('dashboard.admin', [
			'title'			=> 'Dashboard',
			'breadcrumbs'	=> [],
			'waitingForClockInEmployees' => $waitingForClockInEmployees,
			'todayAttendances' => $todayAttendances,
			'employeeWithContractIsAlmostOver'	=> $employeeWithContractIsAlmostOver,
			'employeeWithDoesntHaveContract'	=> $employeeWithDoesntHaveContract,
		]);
	}


	private function hrdDashboard()
	{
		$hadir  = Kehadiran::where('tgl_absensi', date('Y-m-d'))
				->where('keterangan', 'hadir')
				->count();
		$sakit  = 0;
		$izin   = 0;
		return view('dashboard.hrd', [
			'karyawan'      => Karyawan::count(),
			'habisKontrak'  => $this->getHabisKontrak(),
			'belumAtur'     => $this->getBelumAturKontrak(),
			'kehadiran'     => (object) [
				'hadir' => $hadir,
				'sakit' => $sakit,
				'izin'  => $izin,
			],
		]);
	}




	private function employeeDashboard()
	{
		$employee = auth()->user()->employee;
		$employee->load([ 'department', 'position', 'shift', 'todayAttendance', 'todayAttendanceTypeHadir' ]);

		// Get Off Days
		$offDays = \App\Models\OffDay::getOffDayByDateRange(today(), today()->addDays(365));

		// Get Announcement
		$announcements = \App\Models\Announcement::where(function($query) use($employee) {
			$query->where('id_department', $employee->id_department)
				  ->orWhere('id_department', null);
		})->where(function($query) use($employee){
			$query->where('id_employee_group', $employee->id_employee_group)
				  ->orWhere('id_employee_group', null);
		})->where(function($query) use($employee){
			$query->where('id_position', $employee->id_position)
				  ->orWhere('id_position', null);
		})
		->where('is_published', 'yes')
		->orderBy('created_at', 'desc')
		->take(5)
		->get();
		
		// Get Resume Attendance
		$attendanceResume = $employee->getWorkTimeByDateRange(date('Y-m-01'), date('Y-m-d'));

		// Get Birthday List
		$birthdays = Employee::getEmployeeWithBirthdayThisMonth();
		$employeeWithBirthday = [];
		foreach($birthdays as $emp)
		{
			$dateOfBirth = \Carbon\Carbon::createFromFormat('Y-m-d', $emp->date_of_birth);
			$dateOfBirthday = \Carbon\Carbon::createFromFormat('Y-m-d', $emp->date_of_birth)
											->setYear(date('Y'));
			$emp = [
				'photo_link'	=> $emp->photoLink('face'),
				'employee_name'	=> $emp->employee_name,
				'department_name' => $emp->departmentName(),
				'date_of_birthday'=> $dateOfBirthday->format('d F Y'),
				'age'			=> $dateOfBirth->diffInYears($dateOfBirthday),
				'day'			=> $dateOfBirthday->format('d'),
			];
			$employeeWithBirthday[] = $emp;
		}
		usort($employeeWithBirthday, function($a, $b) {
			return $a['day'] <=> $b['day'];
		});


		$trackingCheckInToday = \App\Models\Tracking::where('id_employee', employee()->id)
								->where('created_at', 'like', '%'.date('Y-m-d').'%')
								->count(); 


		return view('dashboard.employee', [
			'title'				=> 'Dashboard',
			'employee'			=> $employee,
			'offDays'			=> $offDays,
			'announcements'		=> $announcements,
			'isOffDay'			=> $employee->isOffday(),
			'isOvertime'		=> $employee->isOvertime(),
			'isAllowForClockIn'	=> $employee->isAllowForClockIn(),
			'isMustClockIn'		=> $employee->isMustClockIn(),
			'breadcrumbs'		=> [
				[
					'title'	=> 'Dashboard',
					'link'	=> route('dashboard')
				]
			],
			'attendanceResume'	=> $attendanceResume,
			'employeeWithBirthday' => $employeeWithBirthday,
			'amountOfTrackingCheckInToday' => $trackingCheckInToday,
		]);
	}



	private function registrantDashboard()
	{
		return view('dashboard.registrant', [
			'title'			=> 'Dashboard',
			'breadcrumbs'	=> [
			]
		]);
	}



	public function getFrontSecurityData()
	{
		$pengunjungAktif = VisitorAkses::where('status', 2);
		$jmlSehari = VisitorAkses::where('tgl', date('Y-m-d'))->count();
		$jmlSebulan = VisitorAkses::where('tgl', '>=', date('Y-m').'-01')->where('tgl', '<=', date('Y-m-t'))->count();
		$perusahaanAktif = VisitorAkses::where('tgl', date('Y-m-d'))->select('perusahaan')->join('visitor', 'visitor.id', '=', 'visitor_akses.visitor_id')->groupBy('visitor.perusahaan');

		$dataPengunjung = '';
		foreach ($pengunjungAktif->get() as $d) {
			// $html = '
			//     <tr>
			//         <td>'.$d->.'</td>
			//         <td>'.'</td>
			// ';
		}

		$data = [
			'jmlAktif'      => $pengunjungAktif->count(),
			'jmlSehari'     => $jmlSehari,
			'jmlSebulan'    => $jmlSebulan,
			'jmlPerusahaan' => $perusahaanAktif->count(),
		];

		return json_encode($data);
	}

	public function getHseData()
	{
		$jmlAuthGateAktif = GateAuth::where('status', 2)->count();
		$jmlSehari = SiteAkses::where('tgl', date('Y-m-d'))->count();
		$jmlSebulan = SiteAkses::where('tgl', '>=', date('Y-m').'-01')->where('tgl', '<', date('Y-m-d', strtotime('+1 days', strtotime(date('Y-m-t')))))->count();

		// Pengunjung dapat izin
		$dataDiizinkan = '';
		foreach(SiteAkses::where('status', 2)->get() as $d)
		{
			$html = "
				<tr>
					<td>".$d->visitor->nama_visitor;
			$statusOnSite = GateAuth::where([
				'ref_type'  => 'visitor',
				'ref_id'    => $d->visitor_id,
			])->first()->status;
			if($statusOnSite == 2)
			{
				$html .= '<span class="badge badge-success ml-1">Aktif</span>';
			}
			$html .= "</td>
					<td>".date('d-m-Y H:i:s', strtotime($d->valid_until))."</td>
				</tr>";
			$dataDiizinkan .= $html;
		}

		// Karyawan di site
		$dataKaryawan = '';
		$karyawanOnSite = GateAuth::where([
			'ref_type'  => 'employee',
			'status'    => 2,
		])->get();
		foreach($karyawanOnSite as $d) {
			$html = "
			<tr>
				<td>".$d->karyawan->nama."</td>
				<td>".$d->karyawan->departemen->nama_departemen."</td>
			</tr>";

			$dataKaryawan .= $html;
		}


		$data = [
			'jmlAktif'      => $jmlAuthGateAktif,
			'jmlSehari'     => $jmlSehari,
			'jmlSebulan'    => $jmlSebulan,
			'jmlSite'       => Site::count(),
			'dataDiizinkan' => $dataDiizinkan,
			'dataKaryawan'  => $dataKaryawan,
		];

		return json_encode($data);
	}

	public function getHabisKontrak()
	{
		$date = date('Y-m-d');
		$habisKontrak = [];
		$kontrak = KaryawanKontrak::where('akhir_kontrak', '<', $date)->get();

		return $kontrak;
	}

	public function getBelumAturKontrak()
	{
		$belumAtur = [];
		$karyawan = Karyawan::all();
		foreach($karyawan as $d) 
		{
			if($d->status == 'kontrak')
			{
				$cekKontrak = KaryawanKontrak::where(['karyawan_id' => $d->id])->count();
				if($cekKontrak == 0)
				{   
					$arr = [
						'nama'          => $d->nama,
						'departemen'    => $d->departemen->nama_departemen
					];
					array_push($belumAtur, (object) $arr);
				}
			}
		}

		return $belumAtur;
	}


	private function generateJadwalMingguan()
	{
		$karyawanId = \App::make('karyawan')->id;
		if(Karyawan::exists($karyawanId))
		{
			$dayNow = date('N');
			$dateNow = date('Y-m-d');
			$date = date('Y-m-d', strtotime($dateNow . ' -'.($dayNow - 1).' day'));
			$result = [];
			for ($i = 1; $i <= 7 ; $i++)
			{
				$day = null;
				$day = $i == 1 ? 'Sen' : $day; 
				$day = $i == 2 ? 'Sel' : $day; 
				$day = $i == 3 ? 'Rab' : $day; 
				$day = $i == 4 ? 'Kam' : $day; 
				$day = $i == 5 ? 'Jum' : $day; 
				$day = $i == 6 ? 'Sab' : $day; 
				$day = $i == 7 ? 'Min' : $day; 
				$arr = (object) [
					'day'       => $day,
					'date'      => $date,
					'isLibur'   => Staff::isLibur($karyawanId, $date),
				];
				array_push($result, $arr);
				$date = date('Y-m-d', strtotime($date . ' +1 day'));
			}
			return $result;
		}
		else
		{
			return [];
		}
	}


	public function loginUsingId($userId)
	{
		$user = \App\User::find($userId);
		if($user) {
			auth()->loginUsingId($userId);
			return redirect('login');
		}

		return 'not found';
	}
}
