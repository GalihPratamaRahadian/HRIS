@extends('template/backEnd')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h4 class="page-title">Isi Kehadiran</h4>
        <div class="d-flex align-items-center">
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ url('kehadiran') }}">Kehadiran</a></li>
                <li class="breadcrumb-item active" aria-current="page">Jam Masuk</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <form id="attendanceForm">
            <div class="row">
                <div class="col-lg-6 grid-margin">
                    <div class="card support-pane-card">
                        <div class="card-body">
                            <div class="support-pane">
                                <label><b>Foto</b></label> <br>
                                <video style="height: auto; width: 100%;" id="cameraVideo" autoplay="true"></video>
                                <canvas id="cameraCanvas" style="display: none;"></canvas>
                                <img id="cameraPict" style="height: auto; width: 100%; display: none;">
                                <div class="w-100 text-center mt-2 mb-4">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-primary captureBtn text-wrap" disabled=""><i class="mdi mdi-camera"></i> Capture</button>
                                        <button type="button" class="btn btn-success playBtn text-wrap"><i class="mdi mdi-play"></i> Play</button>
                                        <button type="button" class="btn btn-danger stopBtn text-wrap" style="display: none;"><i class="mdi mdi-stop"></i> Stop</button>
                                        <a href="#" class="btn btn-success downloadBtn text-wrap" download="capture" target="_blank" style="display: none;"><i class="mdi mdi-download"></i> Download</a>
                                        <button type="button" class="btn btn-danger removeBtn text-wrap" style="display: none;"><i class="mdi mdi-trash-can"></i> Remove</button>
                                    </div>
                                </div>
                                <input type="hidden" name="blobImage">
                                <div class="form-group">
                                    <label><b>Tipe Kehadiran</b></label> <br>
                                    @if(isset($_GET['lembur']))
                                    <label>Lembur</label>
                                    @else
                                    <label>Hari Aktif</label>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label><b>Lokasi</b></label> <br>
                                    <p class="address m-0 text-wrap"></p>
                                    <a href="javascript:void();" class="mapsBtn" style="display: none;">Lihat Peta</a>
                                    
                                    <input type="hidden" name="latitude">
                                    <input type="hidden" name="longitude">
                                </div>
                                <button type="submit" class="btn btn-success btn-block mt-2">
                                    Jam Masuk <i class="mdi mdi-login"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection


@section('modal')
<div class="modal fade" id="mapModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog mt-4" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Lokasi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="map" style="width: 100%; height: 300px;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal"><i class="mdi mdi-close"></i>Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection


@section('script')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBPiW2EegvKpsoHZd5sXFzxhhuGYLIB7G4"></script>
<script type="text/javascript">
	$(function(){

        let cameraVideo = document.getElementById("cameraVideo"),
            mediaStream = null,
            canvas = document.getElementById("cameraCanvas"),
            canvasContext = canvas.getContext('2d'),
            camera = {
                width: null,
                height: null
            };

        const play = () => {
            if (navigator.mediaDevices.getUserMedia) {
                navigator.mediaDevices.getUserMedia({ video: true })
                .then(stream => {
                    cameraVideo.srcObject = stream;
                    mediaStream = stream;
                    let {height, width} = stream.getTracks()[0].getSettings();
                    camera.width = width;
                    camera.height = height;
                    $('.playBtn').hide();
                    $('.stopBtn').show();
                    enable($('.captureBtn'));
                })
                .catch(err => {
                    console.log("Something went wrong!");
                    console.log(err);
                });
            }
        }


        const capture = () => {
            let scale = 1;
            canvas.width = camera.width;
            canvas.height = camera.height;
            canvasContext.drawImage(cameraVideo, 0,0, canvas.width, canvas.height);
            canvas.toBlob(blob =>{
                let url = window.URL.createObjectURL(blob);
                // $('.cameraPict').attr('src', url);
                $('.downloadBtn').attr('href', url);
            });
            $('#cameraVideo').hide();
            $('#cameraPict').show();
            disable($('.captureBtn'));
            let dataUrl = canvas.toDataURL("image/jpeg");
            $('[name="blobImage"]').val(dataUrl);
            $('#cameraPict').attr('src', dataUrl);
        }


        const stop = () => {
            if(mediaStream != null) {
                if(mediaStream.active == true) {
                    mediaStream.getTracks()[0].stop();
                    $('.playBtn').show();
                    $('.stopBtn').hide();
                    disable($('.captureBtn'));
                }
            }
        }        
        

        const getLocation = () => {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition, showError);
                console.log("Geolocation get the position.");
            } else {
                console.log("Geolocation is not supported by this browser.");
            }
        }


        const showPosition = position => {
            let lat = position.coords.latitude,
                long = position.coords.longitude;
            showMap(lat, long)
            getAddress(lat, long)
            $('[name="latitude"]').val(lat);
            $('[name="longitude"]').val(long);
            $('.mapsBtn').show();
        }


        const showError = error => {
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    alert(`Mohon berikan izin akses lokasi anda. Cek link berikut : https://support.google.com/chrome/answer/142065?hl=en`);
                    $('.address').html(`Aplikasi tidak berjalan semesti nya. <br>Mohon berikan izin akses lokasi anda. Petunjuk <a target="_blank" href="https://support.google.com/chrome/answer/142065?hl=en">Klik disini</a>`)
                    $('.mapsBtn').hide();
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


        const showMap = (lat, long) => {
            $.each($('#map'), (i, elem) => {
                let property = {
                    center:new google.maps.LatLng(lat, long),
                    zoom: 18,
                    mapTypeId:google.maps.MapTypeId.ROADMAP,
                    animation: google.maps.Animation.BOUNCE
                };
                let map = new google.maps.Map(elem, property);
                // membuat Marker
                let marker=new google.maps.Marker({
                    position: new google.maps.LatLng(lat, long),
                    map: map
                });
                let infoWindow = new google.maps.InfoWindow({
                    content : `<img src="https://getbootstrap.com/docs/4.0/assets/img/favicons/favicon.ico">`,
                })
                google.maps.event.addListener(marker, 'click', function(){
                    infoWindow.open(map, marker);
                })
            })
        }


        const getAddress = (lat, long) => {
            $.get({
                url : `{{ url('geocode') }}/${lat}/${long}`,
                dataType : 'json',
                success : res => {
                    renderAddress(res.detailAddress);
                },
                error : res => {
                    console.log(res);
                }
            })
        }


        const renderAddress = address => {
            $('.address').html(`${address.village}, ${address.subdistrict}, ${address.city}, ${address.province} ${address.postalCode}`);
        }


        const buttonEvent = () => {
            $('.playBtn').on('click', function(){
                play();
            })

            $('.stopBtn').on('click', function(){
                stop();
            })

            $('.captureBtn').on('click', function(){
                capture();
                $(this).hide();
                $('.stopBtn').hide();
                $('.removeBtn').show();
                $('.downloadBtn').show();
            })

            $('.removeBtn').on('click', function(){
                $('#cameraPict').removeAttr('src');
                $('#cameraPict').hide();
                $('#cameraVideo').show();
                enable($('.captureBtn'));
                $('[name="blobImage"]').val('');
                $(this).hide();
                $('.downloadBtn').hide();
                $('.stopBtn').show();
                $('.captureBtn').show();
            })

            $('.mapsBtn').on('click', function(){
                $('#mapModal').modal('show');
            })
        }


        const validateInput = () => {
            let blobImage = $('[name="blobImage"]').val(),
                latitude = $('[name="latitude"]').val(),
                longitude = $('[name="longitude"]').val();

            if(isEmpty(blobImage)) {
                swal("Peringatan", "Wajib melakukan capture", "error");
                return false;
            } else if(isEmpty(latitude) || isEmpty(longitude)) {
                swal("Peringatan", "Lokasi anda tidak berfungsi", "error");
                return false;
            } else {
                return true;
            }
        }


        const attendanceSubmit = () => {
            $('#attendanceForm').on('submit', function(e){
                e.preventDefault();

                if(!validateInput()) {
                    return;
                }

                let formData = $(this).serialize(),
                    submitBtn = $(this).find('[type="submit"]');

                processingButton(submitBtn);
                ajaxSetup();
                $.ajax({
                    url : `{{ isset($_GET['lembur']) ? url("kehadiran/clockin?lembur=true") : url("kehadiran/clockin") }}`,
                    method : 'post',
                    data : formData,
                    dataType : 'json',
                    success : res => {
                        processingButtonContinue(submitBtn);
                        toastrAlert();
                        toastr.success(res.msg, 'Berhasil');
                        setTimeout(() => {
                            window.location.replace(`{{ url('dashboard') }}`);
                        }, 1000);
                    },
                    error : res => {
                        processingButtonDone(submitBtn, `Jam Masuk <i class="mdi mdi-login"></i>`);
                    }
                })
            })
        }


        const init = () => {
            buttonEvent();
            play();
            getLocation();
            attendanceSubmit();
        }

        init();
	});
</script>
@endsection
