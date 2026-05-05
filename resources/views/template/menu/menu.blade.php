<li class="nav-item">
	<a class="nav-link" href="{{ route('dashboard') }}">
		<i class="menu-icon mdi mdi-view-dashboard"></i>
		<span class="menu-title"> Dashboard </span>
	</a>
</li>

<?php 
	$user = auth()->user();
?>

@if($user->isAdmin())

	@include('template.menu.menu_admin')

@elseif($user->isHrd())

	@include('template.menu.menu_hrd')

@elseif($user->isEmployee())

	@include('template.menu.menu_employee')

@elseif($user->isRegistrant())

	@include('template.menu.menu_registrant')

@elseif($user->isDeveloper())

	@include('template.menu.menu_developer')

@endif
