<!DOCTYPE html>
<html>
<head>
	<title>Recent | Face Terminal</title>
	<link rel="stylesheet" href="{{ asset('vendors/iconfonts/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/vendor.bundle.addons.css') }}">

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}" />

    <!-- <link rel="stylesheet" href="{{ asset('css/recent-style.css') }}"> -->

    <script src="{{ asset('') }}vendors/js/vendor.bundle.base.js"></script>
    <script src="{{ asset('') }}vendors/js/vendor.bundle.addons.js"></script>

    <!-- Pace -->
    <link rel="stylesheet" href="{{ asset('') }}vendors/pace/green/pace-theme-flash.css">
    <script src="{{ asset('') }}vendors/pace/pace.min.js"></script>

    <!-- Daterange Picker -->
    <link rel="stylesheet" href="{{ asset('') }}vendors/daterange/daterangepicker.css">
    <script src="{{ asset('') }}vendors/daterange/daterangepicker.js"></script>

    <script src="{{ asset('js/myJs.js') }}"></script>

    <style type="text/css">
    	.nav-log {
    		background: #0a003a;
    		height: 100vh;
    	}

    	#dateRange {
    		background: inherit;
    	}

    	#latestLabel:before {
    		border-radius: unset !important;
    		border: 1px solid #fff;
    	}

    	.custom-checkbox #latest.custom-control-input:checked ~ .custom-control-label::before {
    		background: #0a003a;
    	}

    	.log-row {
    		padding: 1rem;
    	}

    	.log-row-danger {
    		background: #9c0007 !important;
    	}

    	.log-row-danger {
    		background: #9c0007 !important;
    	}

    	.log-row:hover {
    		background: #291a6f;
    		cursor: pointer;
    	}

    	.log-row.active {
    		background: #291a6f;
    	}

    	.log-desc {
    		margin-left: 1rem;
    	}

    	.log-nama,
    	.log-asal {
    		margin-bottom: 0.25rem;
    	}

    	.log-nama,
    	.log-asal,
    	.log-temperature {
    		color: white;
    	}

    	.logNameWrapper {
    		position: absolute;
    		top: 30px;
    		width: 100%;
    		text-align: center;
    	}

    	.logName {
    		font-size: 30pt;
		    font-weight: bold;
		    color: white;
		    text-shadow: 2px 2px 3px black;
    	}

    	.logPhoto {
    		height: calc(100vh - 130px);
		    background-size: cover !important;
		    background-position: center !important;
		    position: relative;
    	}

    	.logTemperature {
			position: absolute;
			bottom: 0;
			padding: 80px 30px 40px 30px;
			width: 100%;
			text-align: center;
			background: linear-gradient(to bottom, #98f99800, #2bea2b82, #2cf3089e, #099c09);
			color: white;
			font-size: 25pt;
			font-weight: 900;
    	}

    	.logTemperature-danger {
    		background: linear-gradient(to bottom, #f9989800, #ea2b2b82, #f308089e, #9c0909);
    	}

    	.logFace {
    		width: 60px;
    		margin-right: 10px;
    	}

    	::-webkit-scrollbar {
    		width: 6px;
		}
		 
		::-webkit-scrollbar-track {
		    border-radius: 10px;
		}
		 
		::-webkit-scrollbar-thumb {
		    border-radius: 10px;
		    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,1); 
    		background-color: #000;
		}

		.mynav {
			position: absolute;
			width: 100%;
			z-index: 200;
		}

		.mynav .mynav-body {
			z-index: 200;
			padding: 2.5rem;
			background-color: white;
			box-shadow: 0 0px 23px 4px rgba(0,0,0,0.3);
		}

		.text-danger {
			color: #f90700 !important;
		}

		.text-success {
			color: #00da11 !important;
		}

		.company-logo {
			position: absolute;
			left: 45px;
			top: 30px;
		}

    </style>
</head>
<body style="overflow-y: hidden;">
	<div class="row no-gutters bg-white mynav" style="z-index: 3000">
		<div class="col-8" id="recentDetail">
			<div class="mynav-body">
				<h1 align="center">Face Recognition</h1>
			</div>
			<div class="row">
				<div class="col-lg-12 text-center">
					<div class="logPhoto">

						<!-- Company Logo -->
						<img class="company-logo" src="{{ url('images/app/company-logo.png') }}">

						<p class="logException pt-5" align="center">
							<span class="h2">Tidak ada log<span>
						</p>

						<div class="logNameWrapper">
							<span class="logName"></span>
						</div>

						<div class="logTemperature" style="display: none;">
							<img src="#" class="logFace">
							<span class="temperature">36.5&deg;C</span>
							<img class="logMask" src="{{ asset('images/mask/mask.png') }}" style="height: 160px;">
						</div>

					</div>
				</div>
			</div>
		</div>

		<div class="col-4 nav-log">
			<div class="mx-auto mt-2 text-center w-auto text-white">
				<h2>Face Recognition</h2>
				<div id="dateRange" style="cursor: pointer; padding: 5px 10px; display: inline-block;" class="text-center">
					<i class="mdi mdi-calendar"></i>&nbsp;
					<span></span> <i class="mdi mdi-chevron-down"></i>
				</div>
				<div class="custom-control custom-checkbox">
					<input type="checkbox" class="custom-control-input" id="latest" checked>
					<label class="custom-control-label" for="latest" id="latestLabel">Tampilkan terbaru</label>
				</div>
				<div class="row">
					<div class="col-12">
						<button class="btn btn-primary" id="fullscreenBtn"><i class="mdi mdi-fullscreen"></i> Fullscreen</button>
						<button class="btn btn-success" id="reloadBtn"><i class="mdi mdi-sync"></i> Reload</button>
					</div>
				</div>
			</div>
			<div class="row mt-4">
				<div class="col-12">
					<div style="overflow-y: auto; height: calc(100vh - 168px);" id="recentList">
						<!-- Ajax -->
					</div>
				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript">
		$(function(){

			const protocol = window.location.protocol;
			let wsRoute = `{{ appconfig('recent_ws_url', '') }}`;

			const faceLog = log => {
				let path = '{{ url("") }}';
				if(!isEmpty(log.auth)) {
					if(!isEmpty(log.auth.visitor)) {
						return `${path}/uploads/face/${log.auth.face}`;
					} else if(!isEmpty(log.auth.karyawan)) {
						return `${path}/uploads/face/${log.auth.face}`;
					} else {
						return `${path}/log/face/${log.filename}`;
					}
				} else {
					return `${path}/log/face/${log.filename}`;
				}
			}


			const isAbnormal = log => {
				let { temperature, mask } = log
				let abnormalCheck = 0;

				if(parseFloat(temperature) > parseFloat(`{{ $maxTemp }}`) || parseFloat(temperature) < parseFloat(`{{ $minTemp }}`)) {
					abnormalCheck++;
				}

				if(parseInt(`{{ $mustMask }}`) == 1 && !mask) {
					abnormalCheck++;
				}

				if(abnormalCheck > 0) {
					return true;
				} else {
					return false;
				}
			}

			const logItem = (data) => {
				let { name, from, facePhoto, photo, temperature, mask } = data

				let html = '';
				if(temperature == null) temperature = 0;

				if(mask) {
					maskImg = `{{ asset('images/mask/mask.png') }}`;
				} else {
					maskImg = `{{ asset('images/mask/mask-slash.png') }}`;
				}
				
				if(isAbnormal(data)) {
					html = `
					<div class="d-flex log-row log-row-danger" data-id="${data.id}">`
				} else {
					html = `
					<div class="d-flex log-row" data-id="${data.id}">`
				}

				html += `
						<img src="${facePhoto}" class="log-img" style="width: 100px; height: auto;" loading="lazy">
						<div class="log-desc my-auto">
							<div class="row">
								<div class="col-6">
									<h2 class="log-temperature">
										@if(appconfig('using_temperature', true) == true)
										${temperature}&deg;C
										@else
										-
										@endif
									</h2>
								</div>
								<div class="col-6 text-right">
									<img style="height: 45px; width: auto;" src="${maskImg}" loading="lazy">
								</div>
							</div>
							<h4 class="log-nama">${data.name}</h4>
							<h5 class="log-asal">${data.from}</h5>
							<span class="text-white">${data.date}</span>
						</div>
					</div>`;
				return html;
			}


			const detail = id => {
				$.get({
					url : `{{ url("recent/detail") }}/${id}`,
					dataType : 'json',
					success : res => {
						let detail = $('#recentDetail');

						let { name, from, facePhoto, photo, temperature, temperatureText, mask } = res

						detail.find('.logPhoto').css('background', `url(${photo})`);

						clearColorText(detail.find('.logName'));
						if(res.name == '-----') {
							detail.find('.logName').html(`Stranger`);
							detail.find('.logName').addClass('text-danger');
						} else {
							detail.find('.logName').html(`${name}`)
							detail.find('.logName').addClass('text-success');
						}

						if(mask) {
							detail.find('.logTemperature').find('.logMask').attr('src', '{{ asset("images/mask/mask.png") }}');
						} else {
							detail.find('.logTemperature').find('.logMask').attr('src', '{{ asset("images/mask/mask-slash.png") }}');
						}
						
						detail.find('.logTemperature').removeClass('logTemperature-danger');

						// Cek Abnormal
						if(isAbnormal(res)) {
							detail.find('.logTemperature').addClass('logTemperature-danger');
						}

						detail.find('.logTemperature').fadeIn(500);
						detail.find('.logName').fadeIn(500);

						let temperatureBg = setTimeout(() => {
							detail.find('.logTemperature').fadeOut(1000);
							detail.find('.logName').fadeOut(1000);
						}, 4000);

						setTimeout(() => {
							clearTimeout(temperatureBg);
						}, 4000)

						detail.find('.logTemperature').find('.temperature').html(`${temperatureText}`);
						detail.find('.logFace').attr('src', facePhoto);
					}
				})
			}

			const get = () => {
				$('#recentList').html(`<h3 align="center" style="color: white;"><i class="mdi mdi-loading mdi-spin"></i> Loading..</h3>`);
				$.get({
					url : `{{ url("recent/get") }}/${dateFormat(start)}/${dateFormat(end)}`,
					dataType : 'json',
					success  : res => {
						let html = '';
						if(res.length > 0) {
							$('.logException').hide()
							$('.logName').show()
							$('.logTemperature').show()
							detail(res[0].id)
							$.each(res, (i, d) => {
								html += logItem(d);
							})
						} else {
							$('.logException').show()
							$('.logPhoto').css('background', '');
							$('.logName').hide()
							$('.logTemperature').hide()
							html = `<h3 align="center" style="color: white;">Tidak ditemukan</h3>`
						}
						$('#recentList').html(html);
					},
					error : res => {

					}
				})
			}

			let start = moment();
			let end = moment();

			function cb(startDate, endDate) {
				let month = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
					startHtml = startDate.format('D') + " " + month[startDate.format('M') - 1] + " " + startDate.format('YYYY'),
					endHtml = endDate.format('D') + " " + month[endDate.format('M') - 1] + " " + endDate.format('YYYY');
				$('#dateRange span').html(startHtml + ' - ' + endHtml);
				// $('#dateRange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
				// $('#')
				// reload();
				start = startDate;
				end = endDate;
				get();
			}

			$('#dateRange').daterangepicker({
				startDate: start,
				endDate: end,
				ranges: {
				   'Hari ini': [moment(), moment()],
				   'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
				   '7 hari terakhir': [moment().subtract(6, 'days'), moment()],
				   '30 hari terakhir': [moment().subtract(29, 'days'), moment()],
				   'Bulan ini': [moment().startOf('month'), moment().endOf('month')],
				   'Bulan kemarin': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
				}
			}, cb);

			cb(start, end);

			$(document).on('click', '.log-row', function(){
				$('html').find('.log-row').removeClass('active');
				$(this).addClass('active');

				let id = $(this).data('id');
				detail(id);
			})



			

			let temperatureBg = null;

			function getLatest()
			{
				$.get({
					url : '{{ url("recent/latest") }}',
					dataType : 'json',
					success : res => {
						let list = $('#recentList');
						if(!isEmpty(res)) {
							if($('#latest').is(':checked')) {
								detail(res.id)
							}

							if(list.find('.log-row').first().data('id') != res.id) {
								let html = logItem(res),
									cekLogRow = $('.log-row').length;
								if(cekLogRow > 0) {
									list.prepend(html);
								} else {
									list.html(html);
								}
								if($('#latest').is(':checked')) {
									list.find('.log-row').removeClass('active');
									list.find('.log-row').first().addClass('active');
								}
								$('.logPhoto').show();
								$('.logName').show();
								$('.logException').hide();
							}
						} else {
							$('.logPhoto').hide();
							$('.logName').hide();
							$('.logException').show();
						}
					} 
				})
			}

			

			function dateFormat(data) {
				return data.format('YYYY') + "-" + data.format('MM')+ "-" + data.format('DD');
			}

			// getLatest()

			$('#fullscreenBtn').on('click', function(){
				if(!document.fullscreeenElement) {
					document.documentElement.requestFullscreen();
				} else {
					document.exitFullscreen();
				}
			})

			$('#reloadBtn').on('click', function(){
				window.location.reload();
			})


			const reload = () => {
				window.location.reload();
			}

			let day = 0;
				
			const checkDay = () => {
				let date = new Date();

				if(day == 0) {
					day = date.getDate();
				} else if(day != date.getDate()) {
					reload()
				}
			}

			setInterval(checkDay, 1000);


			@if(\App\Models\FaceTerminalLog::count() > 0)
			let latestLogAt = `{{ \App\Models\FaceTerminalLog::orderBy('created_at', 'desc')->first()->created_at }}`
			@else
			let latestLogAt = null
			@endif

			const requestRecentInfo = () => {
				$.get({
					url: `{{ route('helper.recent.info') }}`,
					dataType: 'json'
				})
				.done(response => {
					const { latest_log_at } = response

					if(latestLogAt != latest_log_at) {
						latestLogAt = latest_log_at
						getLatest();
					}

					setTimeout(() => {
						requestRecentInfo();
					}, 500)
				})
				.fail(error => {
					setTimeout(() => {
						requestRecentInfo();
					}, 2000)
				})
			}

			requestRecentInfo();

		})
	</script>
</body>
</html>