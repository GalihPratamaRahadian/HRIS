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
				<a class="nav-link" href="{{ route('employee_leave_quota') }}"> Jatah Cuti Karyawan  </a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="{{ route('employee_shift_change_schedule') }}"> Jadwal Perubahan Jam Kerja </a>
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
				<a class="nav-link" href="{{ route('employee_leave') }}"> Cuti </a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="{{ route('off_day') }}"> Hari Libur </a>
			</li>
		</ul>
	</div>
</li>

<li class="nav-item">
	<a class="nav-link" href="{{ route('attendance') }}">
		<i class="menu-icon mdi mdi-account-tie"></i>
		<span class="menu-title"> Kehadiran </span>
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