<!DOCTYPE html>
<html lang="en">

<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="_token" content="{{ csrf_token() }}">
	<title> {{ Setting::getValue('app_name', 'Attendance App') }} | {{ $title ?? 'Judul' }} </title>

	<link rel="stylesheet" href="{{ url('vendors/iconfonts/mdi/css/materialdesignicons.min.css') }}">
	<link rel="stylesheet" href="{{ url('vendors/iconfonts/flag-icon-css/css/flag-icon.min.css') }}">
	<link rel="stylesheet" href="{{ url('vendors/iconfonts/simple-line-icon/css/simple-line-icons.css') }}">
	<link rel="stylesheet" href="{{ url('vendors/css/vendor.bundle.base.css') }}">
	<link rel="stylesheet" href="{{ url('vendors/css/vendor.bundle.addons.css') }}">
	<link rel="stylesheet" href="{{ url('css/style.css') }}">
	
	<link rel="stylesheet" href="{{ url('vendors/pace/blue/pace-theme-flash.css') }}">
	<link rel="stylesheet" href="{{ url('vendors/ladda/ladda-themeless.min.css') }}">
	<script src="{{ url('vendors/pace/pace.min.js') }}"></script>
	<link rel="stylesheet" href="{{ url('vendors/toastr/toastr.min.css') }}">

	<link rel="shortcut icon" href="{{ url('') }}images/favicon.png" />

	<style type="text/css">
		.auth-bg-pltu {
			background: url({{ Setting::getLoginBackgroundLink(false) }});
			background-size: cover;
			filter: blur({{ Setting::getValue('background_blur', 0) }}px);
			height: 100%;
			width: 100%;
			position: absolute;
			top: 0px;
			left: 0px;
		}
	</style>

	@yield('style')

</head>

<body>
	<div class="container-scroller">
		<div class="container-fluid page-body-wrapper full-page-wrapper">
			<div class="auth-bg-pltu"></div>
			<div class="content-wrapper d-flex align-items-center auth theme-one">

				@yield('content')

			</div>

		</div>

	</div>

	@yield('modal')

	<script src="{{ url('vendors/js/vendor.bundle.base.js') }}"></script>
	<script src="{{ url('vendors/js/vendor.bundle.addons.js') }}"></script>
	<script src="{{ url('js/myJs.js') }}"></script>
	<script src="{{ url('js/off-canvas.js') }}"></script>
	<script src="{{ url('js/hoverable-collapse.js') }}"></script>
	<script src="{{ url('js/misc.js') }}"></script>
	<script src="{{ url('js/settings.js') }}"></script>
	<script src="{{ url('js/todolist.js') }}"></script>
	<script src="{{ url('vendors/ladda/spin.min.js') }}"></script>
	<script src="{{ url('vendors/ladda/ladda.min.js') }}"></script>
	<script src="{{ url('vendors/ladda/ladda.jquery.min.js') }}"></script>
	<script src="{{ url('vendors/toastr/toastr.min.js') }}"></script>

	@yield('script')

	<script src="{{ url('js/myApp.js') }}"></script>
</body>

</html>