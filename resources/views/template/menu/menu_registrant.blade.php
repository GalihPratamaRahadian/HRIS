<li class="nav-item">
	<a class="nav-link" href="{{ route('profile') }}">
		<i class="menu-icon mdi mdi-account-edit"></i>
		<span class="menu-title"> Edit Profil </span>
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