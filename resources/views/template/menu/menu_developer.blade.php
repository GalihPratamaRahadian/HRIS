@if(UserPermission::check('department', 'r') || UserPermission::check('position', 'r') || UserPermission::check('shift', 'r') || UserPermission::check('leave_reason', 'r') || UserPermission::check('overtime_reason', 'r') || UserPermission::check('employee_group', 'r') || UserPermission::check('employee', 'r') || UserPermission::check('employee_salary', 'r'))
<li class="nav-item">
	<a class="nav-link" data-toggle="collapse" href="#master-data" aria-expanded="false" aria-controls="master-data">
		<i class="menu-icon mdi mdi-database"></i>
		<span class="menu-title"> Master Karyawan </span>
	</a>
	<div class="collapse" id="master-data">
		<ul class="nav flex-column sub-menu">
			@if(UserPermission::check('department', 'r'))
			<li class="nav-item">
				<a class="nav-link" href="{{ route('admin.department') }}"> Departemen </a>
			</li>
			@endif
			@if(UserPermission::check('position', 'r'))
			<li class="nav-item">
				<a class="nav-link" href="{{ route('admin.position') }}"> Jabatan </a>
			</li>
			@endif
			@if(UserPermission::check('shift', 'r'))
			<li class="nav-item">
				<a class="nav-link" href="{{ route('admin.shift') }}"> Jam Kerja </a>
			</li>
			@endif
			@if(UserPermission::check('employee_group', 'r'))
			<li class="nav-item">
				<a class="nav-link" href="{{ route('admin.employee_group') }}"> Grup Karyawan </a>
			</li>
			@endif
			@if(UserPermission::check('employee', 'r'))
			<li class="nav-item">
				<a class="nav-link" href="{{ route('employee') }}"> Karyawan </a>
			</li>
			@endif
			@if(UserPermission::check('employee_salary', 'r'))
			<li class="nav-item">
				<a class="nav-link" href="{{ route('employee_salary') }}"> Gaji Karyawan </a>
			</li>
			@endif
			@if(UserPermission::check('leave_reason', 'r'))
			<li class="nav-item">
				<a class="nav-link" href="{{ route('admin.leave_reason') }}"> Alasan Cuti </a>
			</li>
			@endif
			@if(UserPermission::check('sick_reason', 'r'))
			<li class="nav-item">
				<a class="nav-link" href="{{ route('admin.sick_reason') }}"> Alasan Sakit </a>
			</li>
			@endif
			@if(UserPermission::check('necessity_reason', 'r'))
			<li class="nav-item">
				<a class="nav-link" href="{{ route('admin.necessity_reason') }}"> Alasan Izin </a>
			</li>
			@endif
			@if(UserPermission::check('overtime_reason', 'r'))
			<li class="nav-item">
				<a class="nav-link" href="{{ route('admin.overtime_reason') }}"> Alasan Lembur </a>
			</li>
			@endif
		</ul>
	</div>
</li>
@endif

@if(UserPermission::check('employee_contract', 'r') || UserPermission::check('employee_leave_quota', 'r') || UserPermission::check('employee_shift_change_schedule', 'r') || UserPermission::check('unroutine_shift', 'r'))
<li class="nav-item">
	<a class="nav-link" data-toggle="collapse" href="#employeeSettingItem" aria-expanded="false" aria-controls="employeeSettingItem">
		<i class="menu-icon mdi mdi-account-cog"></i>
		<span class="menu-title"> Setting Karyawan </span>
	</a>
	<div class="collapse" id="employeeSettingItem">
		<ul class="nav flex-column sub-menu">
			@if(UserPermission::check('employee_contract', 'r'))
			<li class="nav-item">
				<a class="nav-link" href="{{ route('employee_contract') }}"> Kontrak Karyawan </a>
			</li>
			@endif
			@if(UserPermission::check('employee_leave_quota', 'r'))
			<li class="nav-item">
				<a class="nav-link" href="{{ route('employee_leave_quota') }}"> Jatah Cuti Karyawan  </a>
			</li>
			@endif
			@if(UserPermission::check('employee_shift_change_schedule', 'r'))
			<li class="nav-item">
				<a class="nav-link" href="{{ route('employee_shift_change_schedule') }}"> Jadwal Perubahan Jam Kerja </a>
			</li>
			@endif
			@if(UserPermission::check('unroutine_shift', 'r'))
			<li class="nav-item">
				<a class="nav-link" href="{{ route('unroutine_shift') }}"> Shift Harian </a>
			</li>
			@endif
		</ul>
	</div>
</li>
@endif


@if(UserPermission::check('employee_leave', 'r') || UserPermission::check('off_day', 'r'))
<li class="nav-item">
	<a class="nav-link" data-toggle="collapse" href="#hariLiburItem" aria-expanded="false" aria-controls="hariLiburItem">
		<i class="menu-icon mdi mdi-calendar"></i>
		<span class="menu-title"> Cuti, Libur & Pengajuan </span>
	</a>
	<div class="collapse" id="hariLiburItem">
		<ul class="nav flex-column sub-menu">
			@if(UserPermission::check('leave_submission', 'r'))
			<li class="nav-item">
				<a class="nav-link" href="{{ route('admin.leave_submission') }}"> Pengajuan Cuti </a>
			</li>
			@endif
			@if(UserPermission::check('attendance_permission_submission', 'r'))
			<li class="nav-item">
				<a class="nav-link" href="{{ route('admin.sick_necessity_submission') }}"> Pengajuan Izin / Sakit </a>
			</li>
			@endif
			@if(UserPermission::check('attendance_permission_submission', 'r'))
			<li class="nav-item">
				<a class="nav-link" href="{{ route('admin.attendance_permission_submission') }}"> Pengajuan Izin Terlambat <br> / Pulang Cepat </a>
			</li>
			@endif
			@if(UserPermission::check('submission.overtime', 'r'))
			<li class="nav-item">
				<a class="nav-link" href="{{ route('submission.overtime') }}"> Pengajuan Lembur </a>
			</li>
			@endif
			@if(UserPermission::check('off_day', 'r'))
			<li class="nav-item">
				<a class="nav-link" href="{{ route('off_day') }}"> Input Hari Libur </a>
			</li>
			@endif
			@if(UserPermission::check('leave_resume', 'r'))
			<li class="nav-item">
				<a class="nav-link" href="{{ route('admin.leave_resume') }}"> Buat Rekap Cuti </a>
			</li>
			@endif
		</ul>
	</div>
</li>
@endif


@if(UserPermission::check('leave_submission', 'r') || UserPermission::check('submission.overtime', 'r'))
@if(setting('menu_submission', 'yes') == 'yes')
<li class="nav-item">
	<a class="nav-link" data-toggle="collapse" href="#pengajuan-staff-item" aria-expanded="false" aria-controls="pengajuan-staff-item">
		<i class="menu-icon mdi mdi-file-document"></i>
		<span class="menu-title"> Pengajuan </span>
	</a>
	<div class="collapse" id="pengajuan-staff-item">
		<ul class="nav flex-column sub-menu">
			@if(UserPermission::check('leave_submission', 'r'))
			<li class="nav-item">
				<a class="nav-link" href="{{ route('admin.leave_submission') }}"> Pengajuan Cuti </a>
			</li>
			@endif
			@if(UserPermission::check('sick_necessity_submission', 'r'))
			<li class="nav-item">
				<a class="nav-link" href="{{ route('admin.sick_necessity_submission') }}"> Pengajuan Sakit/Izin </a>
			</li>
			@endif
			@if(UserPermission::check('attendance_permission_submission', 'r'))
			<li class="nav-item">
				<a class="nav-link" href="{{ route('admin.attendance_permission_submission') }}"> Pengajuan Izin Terlambat <br> / Pulang Cepat </a>
			</li>
			@endif
			@if(UserPermission::check('submission.overtime', 'r'))
			<li class="nav-item">
				<a class="nav-link" href="{{ route('submission.overtime') }}"> Pengajuan Lembur </a>
			</li>
			@endif
		</ul>
	</div>
</li>
@endif
@endif


@if(UserPermission::check('payroll', 'r') || UserPermission::check('salary_slip', 'r'))
<li class="nav-item">
	<a class="nav-link" data-toggle="collapse" href="#payroll-menu" aria-expanded="false" aria-controls="payroll-menu">
		<i class="menu-icon mdi mdi-cash-multiple"></i>
		<span class="menu-title"> Payroll </span>
	</a>
	<div class="collapse" id="payroll-menu">
		<ul class="nav flex-column sub-menu">
			@if(UserPermission::check('payroll', 'r'))
			<li class="nav-item">
				<a class="nav-link" href="{{ route('payroll') }}"> Payroll </a>
			</li>
			@endif
			@if(UserPermission::check('salary_slip', 'r'))
			<li class="nav-item">
				<a class="nav-link" href="{{ route('salary_slip') }}"> Slip Gaji </a>
			</li>
			@endif
			@if(UserPermission::check('payroll_resume', 'r'))
			<li class="nav-item">
				<a class="nav-link" href="{{ route('admin.payroll_resume') }}"> Buat Rekap Payroll </a>
			</li>
			@endif
		</ul>
	</div>
</li>
@endif


@if(UserPermission::check('attendance', 'r'))
<li class="nav-item">
	<a class="nav-link" data-toggle="collapse" href="#attendance-menu" aria-expanded="false" aria-controls="attendance-menu">
		<i class="menu-icon mdi mdi-account-tie"></i>
		<span class="menu-title"> Kehadiran </span>
	</a>
	<div class="collapse" id="attendance-menu">
		<ul class="nav flex-column sub-menu">
			<li class="nav-item">
				<a class="nav-link" href="{{ route('attendance') }}"> Kehadiran </a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="{{ route('check_day') }}"> Check Day </a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="{{ route('attendance.create_summary') }}"> Buat Rekap Kehadiran </a>
			</li>
		</ul>
	</div>
</li>
@endif


@if(UserPermission::check('attendance_location_rules', 'r') || UserPermission::check('web_attendance_permissions', 'r'))
<li class="nav-item">
	<a class="nav-link" data-toggle="collapse" href="#menu-wfh" aria-expanded="false" aria-controls="menu-wfh">
		<i class="menu-icon mdi mdi-account-cog"></i>
		<span class="menu-title"> WFH / Kerja Diluar Kantor </span>
	</a>
	<div class="collapse" id="menu-wfh">
		<ul class="nav flex-column sub-menu">
			@if(UserPermission::check('attendance_location_rules', 'r'))
			<li class="nav-item">
				<a class="nav-link" href="{{ route('attendance_location_rules') }}"> Master Lokasi </a>
			</li>
			@endif
			@if(UserPermission::check('web_attendance_permissions', 'r'))
			<li class="nav-item">
				<a class="nav-link" href="{{ route('web_attendance_permissions') }}"> Setting WFH </a>
			</li>
			@endif
		</ul>
	</div>
</li>
@endif


@if(setting('menu_registration', 'yes') == 'yes')
@if(UserPermission::check('registration', 'r'))
<li class="nav-item">
	<a class="nav-link" href="{{ route('registration') }}">
		<i class="menu-icon mdi mdi-account-group"></i>
		<span class="menu-title"> Pendaftaran Karyawan 
		@if(\App\Models\Registrant::isExistsRegistrantWithStatusWaiting())
		<span class="badge badge-primary ml-1">
			{{ \App\Models\Registrant::amountOfRegistrantWithStatusWaiting() }}
		</span>
		@endif
		</span>
	</a>
</li>
@endif
@endif


@if(UserPermission::check('announcement', 'r'))
@if(setting('menu_announcement', 'yes') == 'yes')
<li class="nav-item">
	<a class="nav-link" href="{{ route('announcement') }}">
		<i class="menu-icon mdi mdi-bullhorn"></i>
		<span class="menu-title"> Pengumuman </span>
	</a>
</li>
@endif
@endif


@if(UserPermission::check('warning_letter', 'r'))
@if(setting('menu_warning_letter', 'yes') == 'yes')
<li class="nav-item">
	<a class="nav-link" href="{{ route('warning_letter') }}">
		<i class="menu-icon mdi mdi-email-alert"></i>
		<span class="menu-title"> Surat Peringatan </span>
	</a>
</li>
@endif
@endif


@if(UserPermission::check('course', 'r') || UserPermission::check('course_exam', 'r') || UserPermission::check('course_result', 'r') || UserPermission::check('course_exam_history', 'r') || UserPermission::check('training', 'r'))
@if(setting('menu_elearning', 'yes') == 'yes')
<li class="nav-item">
	<a class="nav-link" data-toggle="collapse" href="#menu-training" aria-expanded="false" aria-controls="menu-training">
		<i class="menu-icon mdi mdi-book-open-page-variant"></i>
		<span class="menu-title"> Training </span>
	</a>
	<div class="collapse" id="menu-training">
		<ul class="nav flex-column sub-menu">
			@if(UserPermission::check('course', 'r'))
			<li class="nav-item">
				<a class="nav-link" href="{{ route('admin.course') }}"> Course </a>
			</li>
			@endif
			@if(UserPermission::check('course_exam', 'r'))
			<li class="nav-item">
				<a class="nav-link" href="{{ route('admin.course_exam') }}"> Exam </a>
			</li>
			@endif
			@if(UserPermission::check('course_result', 'r'))
			<li class="nav-item">
				<a class="nav-link" href="{{ route('admin.course_result') }}"> Hasil Course </a>
			</li>
			@endif
			@if(UserPermission::check('course_exam_history', 'r'))
			<li class="nav-item">
				<a class="nav-link" href="{{ route('admin.course_exam_history') }}"> Riwayat Exam </a>
			</li>
			@endif
			@if(UserPermission::check('training', 'r'))
			<li class="nav-item">
				<a class="nav-link" href="{{ route('admin.training') }}"> Program Training </a>
			</li>
			@endif
		</ul>
	</div>
</li>
@endif
@endif

<li class="nav-item">
	<a class="nav-link" href="{{ route('admin.company_rules') }}">
		<i class="menu-icon mdi mdi-file-document"></i>
		<span class="menu-title"> Peraturan Perusahaan </span>
	</a>
</li>


@if(UserPermission::check('face_terminal_log', 'r'))
@if(setting('menu_face_terminal_log', 'yes') == 'yes')
<li class="nav-item">
	<a class="nav-link" data-toggle="collapse" href="#menu-log" aria-expanded="false" aria-controls="menu-log">
		<i class="menu-icon mdi mdi-account-group"></i>
		<span class="menu-title"> Log Face Terminal </span>
	</a>
	<div class="collapse" id="menu-log">
		<ul class="nav flex-column sub-menu">
			<li class="nav-item">
				<a class="nav-link" href="{{ route('face_terminal_log') }}"> Data Log </a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="{{ route('developer.face_terminal_log') }}"> Cari Log Untuk Presensi </a>
			</li>
		</ul>
	</div>
</li>
@endif
@endif


@if(UserPermission::check('face_terminal_device', 'r'))
@if(setting('menu_face_terminal_device', 'yes') == 'yes')
<li class="nav-item">
	<a class="nav-link" href="{{ route('face_terminal_device') }}">
		<i class="menu-icon mdi mdi-face-recognition"></i>
		<span class="menu-title"> Device Face Terminal </span>
	</a>
</li>
@endif
@endif


@if(UserPermission::check('user', 'r'))
<li class="nav-item">
	<a class="nav-link" href="{{ route('user') }}">
		<i class="menu-icon mdi mdi-account-group"></i>
		<span class="menu-title"> User </span>
	</a>
</li>
@endif


@if(setting('menu_sales_tracking', 'yes') == 'yes')
<li class="nav-item">
	<a class="nav-link" data-toggle="collapse" href="#sales-tracking" aria-expanded="false" aria-controls="sales-tracking">
		<i class="menu-icon mdi mdi-map-marker-multiple"></i>
		<span class="menu-title"> Tracking Sales </span>
	</a>
	<div class="collapse" id="sales-tracking">
		<ul class="nav flex-column sub-menu">
			<li class="nav-item">
				<a class="nav-link" href="{{ route('sales_employee') }}"> Sales </a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="{{ route('store') }}"> Toko </a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="{{ route('sales_visit') }}"> Kunjungan Sales </a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="{{ route('store_visit') }}"> Riwayat Kunjungan </a>
			</li>
		</ul>
	</div>
</li>
@endif

@if(setting('menu_tracking', 'yes') == 'yes')
@if(UserPermission::check('tracking_location', 'r') || UserPermission::check('tracking_employee', 'r') || UserPermission::check('tracking', 'r'))
<li class="nav-item">
	<a class="nav-link" data-toggle="collapse" href="#tracking" aria-expanded="false" aria-controls="sales-tracking">
		<i class="menu-icon mdi mdi-map-marker-multiple"></i>
		<span class="menu-title"> Tracking </span>
	</a>
	<div class="collapse" id="tracking">
		<ul class="nav flex-column sub-menu">
			@if(UserPermission::check('tracking_location', 'r'))
			<li class="nav-item">
				<a class="nav-link" href="{{ route('admin.tracking_location') }}"> Lokasi Tracking </a>
			</li>
			@endif
			@if(UserPermission::check('tracking_employee', 'r'))
			<li class="nav-item">
				<a class="nav-link" href="{{ route('admin.tracking_employee') }}"> Karyawan Yg Di Tracking </a>
			</li>
			@endif
			@if(UserPermission::check('tracking', 'r'))
			<li class="nav-item">
				<a class="nav-link" href="{{ route('admin.tracking') }}"> Hasil Tracking </a>
			</li>
			@endif
		</ul>
	</div>
</li>
@endif
@endif


@if(setting('menu_advance', 'yes') == 'yes')
@if(UserPermission::check('face_compare', 'r') || UserPermission::check('send_message_to_employee', 'r'))
<li class="nav-item">
	<a class="nav-link" data-toggle="collapse" href="#advance-item" aria-expanded="false" aria-controls="visitor-item">
		<i class="menu-icon mdi mdi-cogs"></i>
		<span class="menu-title"> Lanjutan </span>
	</a>
	<div class="collapse" id="advance-item">
		<ul class="nav flex-column sub-menu">
			@if(UserPermission::check('face_compare', 'r'))
			<li class="nav-item">
				<a class="nav-link" href="{{ route('face_compare') }}"> Komparasi Wajah </a>
			</li>
			@endif
			@if(UserPermission::check('send_message_to_employee', 'r'))
			<li class="nav-item">
				<a class="nav-link" href="{{ route('admin.send_message_to_employee') }}"> Kirim Pesan Ke Karyawan </a>
			</li>
			@endif
		</ul>
	</div>
</li>
@endif
@endif

<li class="nav-item">
	<a class="nav-link" data-toggle="collapse" href="#system-item" aria-expanded="false" aria-controls="visitor-item">
		<i class="menu-icon mdi mdi-cogs"></i>
		<span class="menu-title"> Setting </span>
	</a>
	<div class="collapse" id="system-item">
		<ul class="nav flex-column sub-menu">
			<li class="nav-item">
				<a class="nav-link" href="{{ route('setting.app') }}"> Aplikasi </a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="{{ route('setting.profile') }}"> Edit Profil Akun </a>
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