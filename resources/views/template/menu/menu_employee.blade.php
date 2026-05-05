<li class="nav-item">
	<a class="nav-link" href="{{ route('emp.personal_profile') }}">
		<i class="menu-icon mdi mdi-card-account-details"></i>
		<span class="menu-title"> Data Diri </span>
	</a>
</li>

<li class="nav-item">
	<a class="nav-link" href="{{ route('attendance') }}">
		<i class="menu-icon mdi mdi-account-tie"></i>
		<span class="menu-title"> Kehadiran </span>
	</a>
</li>

@if(appconfig('menu_submission', false))
@if(setting('menu_submission', 'yes') == 'yes')
<li class="nav-item">
	<a class="nav-link" data-toggle="collapse" href="#pengajuan-item" aria-expanded="false" aria-controls="pengajuan-item">
		<i class="menu-icon mdi mdi-file-document"></i>
		<span class="menu-title"> Pengajuan </span>
	</a>
	<div class="collapse" id="pengajuan-item">
		<ul class="nav flex-column sub-menu">
			<li class="nav-item">
				<a class="nav-link" href="{{ route('employee.leave_submission') }}"> Cuti </a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="{{ route('employee.sick_necessity_submission') }}"> Sakit / Izin </a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="{{ route('employee.overtime_submission') }}"> Lembur </a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="{{ route('employee.attendance_permission_submission') }}"> Izin Terlambat / Pulang Cepat </a>
			</li>
		</ul>
	</div>
</li>
@endif
@endif

@if(employee()->isApprover())
@if(setting('menu_submission', 'yes') == 'yes')
<li class="nav-item">
	<a class="nav-link" data-toggle="collapse" href="#penyetujuan-item" aria-expanded="false" aria-controls="penyetujuan-item">
		<i class="menu-icon mdi mdi-check-all"></i>
		<span class="menu-title"> Penyetujuan </span>
	</a>
	<div class="collapse" id="penyetujuan-item">
		<ul class="nav flex-column sub-menu">
			<li class="nav-item">
				<a class="nav-link" href="{{ route('employee.leave_approval') }}"> Cuti </a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="{{ route('employee.sick_necessity_approval') }}"> Sakit / Izin </a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="{{ route('employee.overtime_approval') }}"> Lembur </a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="{{ route('employee.attendance_permission_approval') }}"> Izin Terlambat / Pulang Cepat </a>
			</li>
		</ul>
	</div>
</li>
@endif
@endif

@if(setting('menu_warning_letter', 'yes') == 'yes')
<li class="nav-item">
	<a class="nav-link" href="{{ route('warning_letter') }}">
		<i class="menu-icon mdi mdi-email-alert"></i>
		<span class="menu-title"> Riwayat Surat Peringatan </span>
	</a>
</li>
@endif

<li class="nav-item">
	<a class="nav-link" href="{{ route('salary_slip') }}">
		<i class="menu-icon mdi mdi-cash"></i>
		<span class="menu-title"> Slip Gaji </span>
	</a>
</li>

@if(setting('menu_elearning', 'yes') == 'yes')
<li class="nav-item">
	<a class="nav-link" data-toggle="collapse" href="#elearning-item" aria-expanded="false" aria-controls="visitor-item">
		<i class="menu-icon mdi mdi-book-open-page-variant"></i>
		<span class="menu-title"> E-Learning </span>
	</a>
	<div class="collapse" id="elearning-item">
		<ul class="nav flex-column sub-menu">
			<li class="nav-item">
				<a class="nav-link" href="{{ route('employee.elearning') }}"> Course </a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="{{ route('employee.elearning_certificate') }}"> Sertifikat Kelulusan </a>
			</li>
		</ul>
	</div>
</li>
@endif

@if(setting('menu_elearning', 'yes') == 'yes')
<li class="nav-item">
	<a class="nav-link" href="{{ route('employee.training') }}">
		<i class="menu-icon mdi mdi-book-open-page-variant"></i>
		<span class="menu-title"> Program Training </span>
	</a>
</li>
@endif

<li class="nav-item">
	<a class="nav-link" href="{{ route('employee.company_rules') }}">
		<i class="menu-icon mdi mdi-file-document"></i>
		<span class="menu-title"> Peraturan Perusahaan </span>
	</a>
</li>

<li class="nav-item">
	<a class="nav-link" data-toggle="collapse" href="#system-item" aria-expanded="false" aria-controls="visitor-item">
		<i class="menu-icon mdi mdi-cogs"></i>
		<span class="menu-title"> Setting </span>
	</a>
	<div class="collapse" id="system-item">
		<ul class="nav flex-column sub-menu">
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