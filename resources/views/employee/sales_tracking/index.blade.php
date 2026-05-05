@extends('template.backLayout')


@section('content')
<div class="row">
	<div class="col-md-6">

		<div class="grid-margin">
			<a class="btn btn-success" href="{{ route('emp.sales_tracking.create_store') }}">
				<i class="mdi mdi-plus-thick"></i> Daftarkan Toko Baru
			</a>
		</div>

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

				<div id="store-list">
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

		const getStore = () => {
			let search = $(`[name="search"]`).val();
			let latitude = $(`[name="latitude"]`).val();
			let longitude = $(`[name="longitude"]`).val();
			$.get({
				url: `{{ route('emp.sales_tracking.get_store') }}?search=${search}&latitude=${latitude}&longitude=${longitude}`,
				dataType: 'json'
			})
			.done(response => {
				let { stores } = response
				let html = ''

				if(stores.length > 0) {
					stores.forEach(store => {
						let storeHtml = $('#storeTemplate').text()
										.replaceAll(`{store_name}`, store.store_name)
										.replaceAll(`{address}`, store.address)
										.replaceAll(`{distance_text}`, store.distance_text)
						if(store.is_visited_today) {
							storeHtml = storeHtml.replaceAll(`data-done="false"`, `data-done="true"`)
										.replaceAll(`{checkin_link}`, 'javascript:void(0);');
						} else {
							storeHtml = storeHtml.replaceAll(`{checkin_link}`, store.checkin_link);
						}

						html += storeHtml
					})
				} else {
					if(search.trim() != "") {
						html = `<p align="center"> "${search.trim()}" tidak ditemukan </p>`
					} else {
						html = `<p align="center"> Tidak ada toko </p>`
					}
				}

				$('#store-list').html(html)
			})
		}

		$(`[name="search"]`).on('keyup', function(){
			getStore()
		})

		const getLocation = () => {
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

		getLocation();
		setTimeout(() => {
			getStore();
		}, 1000)


	});
</script>
<script type="text/html" id="storeTemplate">
	<a class="store-item grid-margin" href="{checkin_link}" data-done="false">
		<div class="store-name"> {store_name} </div>
		<div class="store-address"> {address} </div>
		<div class="store-distance"> {distance_text} </div>
	</a>
</script>
@endsection


@section('style')
<style type="text/css">
	
	.store-item {
		display: block;
		/*border: 1px solid #e3e3e3;*/
		padding: 10px 15px;
		border-radius: 8px;
		box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px !important;
		color: black !important;
		text-decoration: none !important;
		background: white;
		position: relative;
	}

	.store-item:hover {
		background: #f9f9f9;
	}

	.store-item .store-name {
		font-size: 16pt;
		font-weight: bold;
	}

	.store-item .store-address {
		font-size: 12pt;
		color: #4e4e4e;
	}

	.store-item .store-distance {
		text-align: right;
	}

	.store-item[data-done="true"]::before {
		font: normal normal normal 24px/1 "Material Design Icons";
		content: "\F05E1";
		position: absolute;
		right: 10px;
		top: 10px;
		font-size: 30pt;
		color: white;
	}

	.store-item[data-done="true"], .store-item[data-done="true"]:hover {
		background: #0cca8e;
	}

	.store-item[data-done="true"] .store-name,
	.store-item[data-done="true"] .store-distance {
		color: white;
	}

	.store-item[data-done="true"] .store-address {
		color: #e8e8e8;
	}

</style>
@endsection