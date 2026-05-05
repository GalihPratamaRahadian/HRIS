<li class="nav-item">
	<a class="nav-link" href="{{ route('dashboard') }}">
		<i class="menu-icon mdi mdi-view-dashboard"></i>
		<span class="menu-title"> Dashboard </span>
	</a>
</li>

@if(auth()->user()->isHrd() || auth()->user()->isAdmin())

<li class="nav-item">
	<a class="nav-link" data-toggle="collapse" href="#employeeItem" aria-expanded="false" aria-controls="employeeItem">
		<i class="menu-icon mdi mdi-database"></i>
		<span class="menu-title"> Data Master Karyawan </span>
	</a>
	<div class="collapse" id="employeeItem">
		<ul class="nav flex-column sub-menu">
			<li class="nav-item">
				<a class="nav-link" href="{{ route('department') }}"> Departemen </a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="{{ route('position') }}"> Jabatan </a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="{{ route('shift') }}"> Shift </a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="{{ route('employee') }}"> Karyawan </a>
			</li>
		</ul>
	</div>
</li>

<li class="nav-item">
	<a class="nav-link" data-toggle="collapse" href="#employeeSettingItem" aria-expanded="false" aria-controls="employeeSettingItem">
		<i class="menu-icon mdi mdi-account-cog"></i>
		<span class="menu-title"> Setting Karyawan </span>
	</a>
	<div class="collapse" id="employeeSettingItem">
		<ul class="nav flex-column sub-menu">
			<li class="nav-item">
				<a class="nav-link" href="{{ route('employee_contract') }}"> Kontrak Karyawan </a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="{{ route('employee_salary') }}"> Gaji Karyawan </a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="{{ route('employee_shift_change_schedule') }}"> Jadwal Perubahan Shift </a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="{{ route('employee_leave_quota') }}"> Jatah Cuti </a>
			</li>
		</ul>
	</div>
</li>

<li class="nav-item">
	<a class="nav-link" data-toggle="collapse" href="#hariLiburItem" aria-expanded="false" aria-controls="hariLiburItem">
		<i class="menu-icon mdi mdi-calendar"></i>
		<span class="menu-title"> Cuti & Hari Libur </span>
	</a>
	<div class="collapse" id="hariLiburItem">
		<ul class="nav flex-column sub-menu">
			<li class="nav-item">
				<a class="nav-link" href="{{ route('employee_leave') }}">Sakit/Izin/Cuti</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="{{ route('off_day') }}"> Hari Libur </a>
			</li>
		</ul>
	</div>
</li>

<li class="nav-item">
	<a class="nav-link" data-toggle="collapse" href="#pengajuan-staff-item" aria-expanded="false" aria-controls="pengajuan-staff-item">
		<i class="menu-icon mdi mdi-file-document"></i>
		<span class="menu-title"> Pengajuan </span>
	</a>
	<div class="collapse" id="pengajuan-staff-item">
		<ul class="nav flex-column sub-menu">
			<li class="nav-item">
				<a class="nav-link" href="{{ route('submission.leave') }}"> Sakit/Izin/Cuti </a>
			</li>
			<!-- <li class="nav-item">
				<a class="nav-link" href="{{ url('pengajuan/lembur') }}">Lembur</a>
			</li> -->
		</ul>
	</div>
</li>

<li class="nav-item">
	<a class="nav-link" href="{{ route('payroll') }}">
		<i class="menu-icon mdi mdi-cash"></i>
		<span class="menu-title"> Penggajian </span>
	</a>
</li>

<li class="nav-item">
	<a class="nav-link" href="{{ route('attendance') }}">
		<i class="menu-icon mdi mdi-account-tie"></i>
		<span class="menu-title"> Kehadiran </span>
	</a>
</li>

<li class="nav-item">
	<a class="nav-link" data-toggle="collapse" href="#menu-setting-kehadiran-via-web" aria-expanded="false" aria-controls="menu-setting-kehadiran-via-web">
		<i class="menu-icon mdi mdi-account-cog"></i>
		<span class="menu-title"> Setting Kehadiran via Web </span>
	</a>
	<div class="collapse" id="menu-setting-kehadiran-via-web">
		<ul class="nav flex-column sub-menu">
			<li class="nav-item">
				<a class="nav-link" href="{{ route('attendance_location_rules') }}"> Master Lokasi </a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="{{ route('web_attendance_permissions') }}"> Izin Kehadiran via Web </a>
			</li>
		</ul>
	</div>
</li>

<li class="nav-item">
	<a class="nav-link" href="{{ route('registration') }}">
		<i class="menu-icon mdi mdi-account-group"></i>
		<span class="menu-title"> Pendaftaran Pengguna 
		@if(\App\Models\Registrant::isExistsRegistrantWithStatusWaiting())
		<span class="badge badge-primary ml-1">
			{{ \App\Models\Registrant::amountOfRegistrantWithStatusWaiting() }}
		</span>
		@endif
		</span>
	</a>
</li>
@endif


@if(auth()->user()->isAdmin())
<li class="nav-item">
	<a class="nav-link" href="{{ route('log') }}">
		<i class="menu-icon mdi mdi-account-group"></i>
		<span class="menu-title"> Log Face Terminal </span>
	</a>
</li>
@endif


@if(\Setting::isAccessControlModuleActive())
@if(auth()->user()->isFrontSecurity() || auth()->user()->isAdmin())
<li class="nav-item">
	<a class="nav-link" data-toggle="collapse" href="#visitor-item" aria-expanded="false" aria-controls="visitor-item">
		<i class="menu-icon mdi mdi-database"></i>
		<span class="menu-title"> Data Master Visitor </span>
	</a>
	<div class="collapse" id="visitor-item">
		<ul class="nav flex-column sub-menu">
			<li class="nav-item">
				<a class="nav-link" href="{{ url('master/visitor') }}"> Visitor </a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="{{ url('master/perusahaan') }}"> Perusahaan Visitor </a>
			</li>
		</ul>
	</div>
</li>

<li class="nav-item">
	<a class="nav-link" href="{{ url('akses/visitor') }}">
		<i class="menu-icon mdi mdi-boom-gate"></i>
		<span class="menu-title"> Akses Visitor </span>
	</a>
</li>

<li class="nav-item">
	<a class="nav-link" href="{{ url('laporan/pengunjung/umum') }}">
		<i class="menu-icon mdi mdi-file-account"></i>
		<span class="menu-title"> Laporan Pengunjung Umum </span>
	</a>
</li>
@endif


@if(auth()->user()->isHse() || auth()->user()->isAdmin())
<li class="nav-item">
	<a class="nav-link" href="{{ url('master/site') }}">
		<i class="menu-icon mdi mdi-office-building"></i>
		<span class="menu-title"> Site </span>
	</a>
</li>
<li class="nav-item">
	<a class="nav-link" href="{{ url('akses/site') }}">
		<i class="menu-icon mdi mdi-boom-gate"></i>
		<span class="menu-title"> Akses Site </span>
	</a>
</li>
<li class="nav-item">
	<a class="nav-link" href="{{ url('laporan/pengunjung/site') }}">
		<i class="menu-icon mdi mdi-file-account"></i>
		<span class="menu-title"> Laporan Pengunjung Site </span>
	</a>
</li>
@endif


@if(auth()->user()->isBackSecurity() || auth()->user()->isAdmin())
<li class="nav-item">
	<a class="nav-link" href="{{ url('emergency/visitor') }}">
		<i class="menu-icon mdi mdi-account-group"></i>
		<span class="menu-title"> Visitor On Site </span>
	</a>
</li>
@endif

@endif

@if(auth()->user()->isAdmin())
<!-- <li class="nav-item">
	<a class="nav-link" href="{{ url('master/card') }}">
		<i class="menu-icon mdi mdi-card"></i>
		<span class="menu-title"> Kartu </span>
	</a>
</li> -->

<li class="nav-item">
	<a class="nav-link" href="{{ route('face_terminal_device') }}">
		<i class="menu-icon mdi mdi-face-recognition"></i>
		<span class="menu-title"> Device Face Terminal </span>
	</a>
</li>

<li class="nav-item">
	<a class="nav-link" data-toggle="collapse" href="#menu-api" aria-expanded="false" aria-controls="menu-api">
		<i class="menu-icon mdi mdi-api"></i>
		<span class="menu-title"> API </span>
	</a>
	<div class="collapse" id="menu-api">
		<ul class="nav flex-column sub-menu">
			<li class="nav-item">
				<a class="nav-link" href="{{ route('api.google_geolocation') }}"> Google Geolocation </a>
			</li>
			<!-- <li class="nav-item">
				<a class="nav-link" href="{{ url('pengajuan/lembur') }}">Lembur</a>
			</li> -->
		</ul>
	</div>
</li>
@endif


@if(auth()->user()->isStaff())
<li class="nav-item">
	<a class="nav-link" href="{{ route('attendance') }}">
		<i class="menu-icon mdi mdi-account-tie"></i>
		<span class="menu-title"> Kehadiran </span>
	</a>
</li>

<li class="nav-item">
	<a class="nav-link" data-toggle="collapse" href="#pengajuan-staff-item" aria-expanded="false" aria-controls="pengajuan-staff-item">
		<i class="menu-icon mdi mdi-file-document"></i>
		<span class="menu-title"> Pengajuan </span>
	</a>
	<div class="collapse" id="pengajuan-staff-item">
		<ul class="nav flex-column sub-menu">
			<li class="nav-item">
				<a class="nav-link" href="{{ route('submission.leave') }}"> Sakit/Izin/Cuti </a>
			</li>
			<!-- <li class="nav-item">
				<a class="nav-link" href="{{ url('pengajuan/lembur') }}">Lembur</a>
			</li> -->
		</ul>
	</div>
</li>

<li class="nav-item">
	<a class="nav-link" href="{{ route('payroll') }}">
		<i class="menu-icon mdi mdi-cash"></i>
		<span class="menu-title"> Slip Gaji </span>
	</a>
</li>
@endif


@if(auth()->user()->isRegistrant())
<li class="nav-item">
	<a class="nav-link" href="{{ route('profile') }}">
		<i class="menu-icon mdi mdi-account-edit"></i>
		<span class="menu-title"> Edit Profil </span>
	</a>
</li>
@endif


<li class="nav-item">
	<a class="nav-link" data-toggle="collapse" href="#system-item" aria-expanded="false" aria-controls="visitor-item">
		<i class="menu-icon mdi mdi-cogs"></i>
		<span class="menu-title"> Setting </span>
	</a>
	<div class="collapse" id="system-item">
		<ul class="nav flex-column sub-menu">
			
			@if(auth()->user()->isAdmin())
			<li class="nav-item">
				<a class="nav-link" href="{{ route('setting.app') }}"> Aplikasi </a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="{{ route('setting.background_login') }}"> Background Login </a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="{{ route('setting.late_cut') }}"> Potongan Keterlambatan </a>
			</li>
			@endif

			<li class="nav-item">
				<a class="nav-link" href="{{ route('setting.profile') }}"> Ubah Profil </a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="{{ route('setting.password') }}"> Ganti Password </a>
			</li>
		</ul>
	</div>
</li>

<li class="nav-item">
	<a class="nav-link" href="javascript:void(0);" onclick="$('#logout-form').submit();">
		<i class="menu-icon mdi mdi-logout"></i>
		<span class="menu-title"> Log Out </span>
	</a>
</li>