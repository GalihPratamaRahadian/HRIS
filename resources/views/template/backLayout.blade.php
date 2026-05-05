<!DOCTYPE html>
<html lang="en">

<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="_token" content="{{ csrf_token() }}">
	<link rel="manifest" href="/manifest.json">
	<link rel="shortcut icon" href="{{ url('favicon.png') }}" />

	<title> {{ Setting::getValue('app_name') }} | {!! !empty($title) ? $title : 'Judul nya disini' !!} </title>
	
	<link rel="stylesheet" href="{{ url('vendors/iconfonts/mdi/css/materialdesignicons.min.css') }}">
	<link rel="stylesheet" href="{{ url('vendors/iconfonts/flag-icon-css/css/flag-icon.min.css') }}">
	<link rel="stylesheet" href="{{ url('vendors/iconfonts/simple-line-icon/css/simple-line-icons.css') }}">
	<link rel="stylesheet" href="{{ url('vendors/css/vendor.bundle.base.css') }}">
	<link rel="stylesheet" href="{{ url('vendors/css/vendor.bundle.addons.css') }}">

	<link rel="stylesheet" href="{{ url('css/style.css') }}">
	<link rel="stylesheet" href="{{ url('css/myCss.css') }}">
	<link rel="stylesheet" href="{{ url('css/responsive-display.css') }}">

	<link rel="stylesheet" href="{{ url('vendors/pace/green/pace-theme-flash.css') }}">

	<link rel="stylesheet" href="{{ url('vendors/swal2/sweetalert.css') }}">
	<link rel="stylesheet" href="{{ url('vendors/toastr/toastr.min.css') }}">
	<link rel="stylesheet" href="{{ url('vendors/icheck/skins/all.css') }}">
	<link rel="stylesheet" href="{{ url('vendors/jquery-ui/jquery-ui.css') }}">
	<link rel="stylesheet" href="{{ url('vendors/daterange/daterangepicker.css') }}">
	<link rel="stylesheet" href="{{ url('vendors/ladda/ladda-themeless.min.css') }}">
	<link rel="stylesheet" href="{{ url('vendors/summernote/summernote-bs4.css') }}">
	<link rel="stylesheet" href="{{ url('vendors/clockpicker/dist/jquery-clockpicker.min.css') }}">

	@yield('style')

	<link rel="stylesheet" href="{{ url('css/adminCss.css') }}">

	<style type="text/css">
		.rounded-border {
			border: 1px solid #e0e0e0;
			padding: 5px 9px;
			border-radius: 5px;
			transition: .3s;
		}

		.rounded-border:hover {
			background: #ededed;
			border: 1px solid #ededed;
		}

		.small {
			font-size: 70%;
		}
	</style>

</head>

<body>
	<div class="container-scroller">

		<nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
			<div class="text-center navbar-brand-wrapper d-flex align-items-top justify-content-center">
				<a class="navbar-brand brand-logo text-white" href="{{ url('dashboard') }}">
					<h3 class="mt-2"> {{ Setting::getValue('app_name') }} </h3>
				</a>
				<a class="navbar-brand brand-logo-mini text-white" href="{{ url('dashboard') }}">
					<h3 class="mt-2">FT</h3>
				</a>
			</div>
			<div class="navbar-menu-wrapper d-flex align-items-center">
				<button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
					<span class="mdi mdi-menu"></span>
				</button>
				<ul class="navbar-nav navbar-nav-right ml-auto">

					<li class="nav-item dropdown d-none d-xl-inline-block user-dropdown">
						<a class="nav-link dropdown-toggle" id="UserDropdown" href="#" data-toggle="dropdown" aria-expanded="false">
							<div class="dropdown-toggle-wrapper">
								<div class="inner">

									<img class="img-xs rounded-circle" src="{{ auth()->user()->profilePhotoUrl() }}" alt="{{ auth()->user()->getName() }}" style="object-fit: cover; object-position: center;">

								</div>
								<div class="inner">
									<div class="inner">
										<span class="profile-text font-weight-bold">{{ auth()->user()->getName() }}</span>
										<small class="profile-text small">{{ auth()->user()->roleText() }}</small>
									</div>
									<div class="inner">
										<div class="icon-wrapper">
											<i class="mdi mdi-chevron-down"></i>
										</div>
									</div>
								</div>
							</div>
						</a>

						<div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
							<a class="dropdown-item mt-3" href="#" data-toggle="modal" data-target="#manageAccountModal">
								<i class="mdi mdi-account-cog"></i> Atur Akun
							</a>
							<a class="dropdown-item" href="{{ route('setting.password') }}">
								<i class="mdi mdi-lock"></i> Ganti Password
							</a>
							<a class="dropdown-item mb-3" href="javascript:void(0);" onclick="$('#logout-form').submit();">
								<i class="mdi mdi-logout"></i>Log Out
							</a>
							<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
								@csrf
							</form>
						</div>
					</li>
					<li class="nav-item dropdown">
						<a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#" data-toggle="dropdown">
							<i class="mdi mdi-bell-outline"></i>
							<span class="count"> {{ amountOfUnreadNotifications() }} </span>
						</a>
						
						@include('template.notification')
					</li>
				</ul>
				<button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
					<span class="mdi mdi-menu"></span>
				</button>
			</div>
		</nav>

		<div class="container-fluid page-body-wrapper">
			<nav class="sidebar sidebar-offcanvas sidebar-dark" id="sidebar">

				<ul class="nav">

					<li class="nav-item nav-profile">
						<img src="{{ auth()->user()->profilePhotoUrl() }}" alt="{{ auth()->user()->getName() }}" style="max-height: 82px; object-fit: cover;">
						<p class="text-center font-weight-medium">
							{{ auth()->user()->getName() }}
						</p>
					</li>

					@include('template.menu.menu')

				</ul>
			</nav>



			<div class="main-panel">
				<div class="content-wrapper">

					<div class="row">
						<div class="col-12 d-flex align-items-center justify-content-between">
							<h4 class="page-title"> {!! !empty($title) ? $title : 'Judul nya disini' !!} </h4>
							<div class="d-flex align-self-start">
								<div class="wrapper">
									@yield('action')
								</div>
							</div>
						</div>
						@if(isset($breadcrumbs))
						<div class="col-12">
							<nav aria-label="breadcrumb">
								<ol class="breadcrumb">
									<li class="breadcrumb-item">
										<a href="{{ route('dashboard') }}"> Dashboard </a>
									</li>

									@foreach($breadcrumbs as $breadcrumb)
									@if($loop->last)
									<li class="breadcrumb-item active"> {{ $breadcrumb['title'] }} </li>
									@else
									<li class="breadcrumb-item">
										<a href="{{ $breadcrumb['link'] }}"> {{ $breadcrumb['title'] }} </a>
									</li>
									@endif
									@endforeach

								</ol>
							</nav>
						</div>
						@endif
					</div>
					
					@yield('content')
				</div>

				<footer class="footer">
					<div class="container-fluid clearfix">
						<span class="text-muted d-block text-center text-sm-left d-sm-inline-block"> Copyright © {{ date('Y') }}
						<a href="{{ appconfig('developer_url') }}" target="_blank"> {{ appconfig('developer_name') }} </a>. All rights reserved.</span>
					</div>
				</footer>
				<!-- partial -->
			</div>
			<!-- main-panel ends -->
		</div>
	</div>
	<!-- container-scroller -->

	@yield('modal')

	<script src="{{ url('vendors/js/vendor.bundle.base.js') }}"></script>
	<script src="{{ url('vendors/js/vendor.bundle.addons.js') }}"></script>
	<script src="{{ url('js/off-canvas.js') }}"></script>
	<script src="{{ url('js/hoverable-collapse.js') }}"></script>
	<script src="{{ url('js/sidebar.js') }}"></script>
	<script src="{{ url('js/settings.js') }}"></script>
	<script src="{{ url('js/todolist.js') }}"></script>
	<script src="{{ url('js/dashboard.js') }}"></script>
	<script src="{{ url('js/tabs.js') }}"></script>
	<script src="{{ url('js/misc.js') }}"></script>

	<script src="{{ url('vendors/pace/pace.min.js') }}"></script>
	<script src="{{ url('vendors/moment/moment.js') }}"></script>

	<script src="{{ url('vendors/swal2/sweetalert.min.js') }}"></script>
	<script src="{{ url('vendors/toastr/toastr.min.js') }}"></script>
	<script src="{{ url('js/iCheck.js') }}"></script>
	<script src="{{ url('vendors/jquery-ui/jquery-ui.js') }}"></script>
	<script src="{{ url('vendors/daterange/daterangepicker.js') }}"></script>
	<script src="{{ url('vendors/ladda/spin.min.js') }}"></script>
	<script src="{{ url('vendors/ladda/ladda.min.js') }}"></script>
	<script src="{{ url('vendors/ladda/ladda.jquery.min.js') }}"></script>
	<script src="{{ url('vendors/summernote/summernote-bs4.js') }}"></script>
	<script src="{{ url('vendors/clockpicker/dist/jquery-clockpicker.min.js') }}"></script>

	<script src="{{ url('js/myJs.js') }}"></script>

	<script>
		$(function(){
			$('.sel2').select2();
		});
	</script>

	@yield('script')
	<!-- Service Worker Registration -->
	<script src="{{ url('js/myApp.js') }}"></script>

</body>

</html>