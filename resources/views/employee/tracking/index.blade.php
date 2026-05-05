@extends('template.backLayout')


@section('content')
<div class="row">
	<div class="col-md-6">

		<!-- <div class="grid-margin">
			<a class="btn btn-success" href="{{ route('emp.sales_tracking.create_store') }}">
				<i class="mdi mdi-plus-thick"></i> Daftarkan Toko Baru
			</a>
		</div> -->

		<div class="card support-pane-card">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					{!! Template::titleBanner($title) !!}
				</div>

				<div class="form-group">
					<label> Cari Toko / Alamat </label>
					<input type="text" name="search" placeholder="Cari Disini" class="form-control">
					<input type="hidden" name="latitude">
					<input type="hidden" name="longitude">
				</div>

				<hr>

				<div id="location-list">
					<p align="center"> Loading... </p>
				</div>

			</div>
		</div>
	</div>
</div>
@endsection


@section('script')
<script type="text/javascript">
	$(function(){

		const getLocation = () => {
			let search = $(`[name="search"]`).val();
			let latitude = $(`[name="latitude"]`).val();
			let longitude = $(`[name="longitude"]`).val();
			$.get({
				url: `{{ route('employee.tracking.get_location') }}?search=${search}&latitude=${latitude}&longitude=${longitude}`,
				dataType: 'json'
			})
			.done(response => {
				let { locations } = response
				let html = ''

				if(locations.length > 0) {
					locations.forEach(location => {
						let locationHtml = $('#locationTemplate').text()
										.replaceAll(`{location_name}`, location.location_name)
										.replaceAll(`{address}`, location.address)
										.replaceAll(`{distance_text}`, location.distance_text)
										.replaceAll(`{direction_link}`, location.direction_link)
						if(location.is_checked_out) {
							locationHtml = locationHtml.replaceAll(`{status}`, `checked-out`)
										.replaceAll(`{checkin_link}`, 'javascript:void(0);');
						} else if(location.is_checked_in) {
							locationHtml = locationHtml.replaceAll(`{status}`, `checked-in`)
										.replaceAll(`{checkin_link}`, location.checkin_link);
						} else {
							locationHtml = locationHtml.replaceAll(`{status}`, `no`)
										.replaceAll(`{checkin_link}`, location.checkin_link);
						}

						html += locationHtml
					})
				} else {
					if(search.trim() != "") {
						html = `<p align="center"> "${search.trim()}" tidak ditemukan </p>`
					} else {
						html = `<p align="center"> Tidak ada toko </p>`
					}
				}

				$('#location-list').html(html)
			})
		}

		$(`[name="search"]`).on('keyup', function(){
			getLocation()
		})

		const getLocationGPS = () => {
			if (navigator.geolocation) {
				navigator.geolocation.getCurrentPosition(showPosition, showError);
				console.log("Geolocation get the position.");
			} else {
				console.log("Geolocation is not supported by this browser.");
			}
		}

		const showPosition = position => {
			const { latitude, longitude } = position.coords

			$(`[name="latitude"]`).val(latitude)
			$(`[name="longitude"]`).val(longitude)
		}

		const showError = error => {
			switch(error.code) {
				case error.PERMISSION_DENIED:
					alert(`Mohon berikan izin akses lokasi anda. Cek link berikut : https://support.google.com/chrome/answer/142065?hl=en`);
					$('.address').html(`Aplikasi tidak berjalan semesti nya. <br>Mohon berikan izin akses lokasi anda. Petunjuk <a target="_blank" href="https://support.google.com/chrome/answer/142065?hl=en">Klik disini</a>`)
					$('.mapBtn').hide();
					break;
				case error.POSITION_UNAVAILABLE:
					alert("Location information is unavailable.");
					break;
				case error.TIMEOUT:
					alert("The request to get user location timed out.");
					break;
				case error.UNKNOWN_ERROR:
					alert("An unknown error occurred.");
					break;
			}
		}

		getLocationGPS();
		setTimeout(() => {
			getLocation();
		}, 1000)


	});
</script>
<script type="text/html" id="locationTemplate">
	<div class="location-item grid-margin" href="{checkin_link}" data-status="{status}">
		<div class="location-name"> {location_name} </div>
		<div class="location-address"> {address} </div>
		<a class="location-direction" href="{direction_link}" target="_blank">
			Lihat Petunjuk di Peta
		</a>
		<a href="{checkin_link}" class="location-check-in">
			<i class="mdi mdi-chevron-right-circle-outline"></i>
		</a>
		<div class="location-distance"> {distance_text} </div>
	</div>
</script>
@endsection


@section('style')
<style type="text/css">
	
	.location-item {
		display: block;
		padding: 10px 15px;
		border-radius: 8px;
		box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px !important;
		color: black !important;
		text-decoration: none !important;
		background: white;
		position: relative;
	}

	.location-item:hover {
		background: #f9f9f9;
	}

	.location-item .location-name {
		font-size: 16pt;
		font-weight: bold;
	}

	.location-item .location-address {
		font-size: 12pt;
		color: #4e4e4e;
		margin-bottom: 10px;
	}

	.location-item .location-distance {
		text-align: right;
	}

	.location-item[data-status="checked-out"]::before {
		font: normal normal normal 24px/1 "Material Design Icons";
		content: "\F05E1";
		position: absolute;
		right: 10px;
		top: 10px;
		font-size: 30pt;
		color: white;
	}

	.location-item[data-status="checked-in"] .location-name,
	.location-item[data-status="checked-in"] .location-distance,
	.location-item[data-status="checked-out"] .location-name,
	.location-item[data-status="checked-out"] .location-distance {
		color: white;
	}

	.location-item[data-status="checked-in"] .location-address,
	.location-item[data-status="checked-out"] .location-address {
		color: #e8e8e8;
	}

	.location-item[data-status="checked-in"],
	.location-item[data-status="checked-in"]:hover {
		background: #0c72ca;
	}

	.location-item[data-status="checked-out"],
	.location-item[data-status="checked-out"]:hover {
		background: #0cca8e;
	}

	.location-item .location-check-in {
		display: inline-block;
		padding: 15px 20px;
		position: absolute;
		top: 10px;
		right: 10px;
		font-size: 35px;
		color: #3c3c3c;
	}

	.location-item[data-status="checked-out"] .location-check-in {
		display: none;
	}

	.location-item[data-status="checked-in"] .location-direction,
	.location-item[data-status="checked-out"] .location-direction,
	.location-item[data-status="checked-in"] .location-check-in {
		color: white;
	}

</style>
@endsection