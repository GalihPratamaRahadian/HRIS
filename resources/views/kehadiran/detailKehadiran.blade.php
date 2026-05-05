@extends('template.backEnd')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h4 class="page-title">Detail Kehadiran</h4>
        <div class="d-flex align-items-center">
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <!-- <li class="breadcrumb-item"><a href="#">Home</a></li> -->
                <li class="breadcrumb-item">
                    <a href="{{ url('dashboard') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ url('kehadiran') }}">Kehadiran</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Detail</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    @if(!empty($kehadiran->foto) || !empty($kehadiran->lokasi))
    <div class="col-md-7">
    @else
    <div class="col-md-6">
    @endif
        <div class="row">
            <div class="col-12">
                <div class="card support-pane-card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title mb-0"><b>Detail Kehadiran</b></h4>
                        </div>
                        <div class="row table-responsive show-on-lg-and-up">
                            <table class="table table-striped">
                                <tr>
                                    <th>Karyawan</th>
                                    <td>{{ $kehadiran->karyawan->nama }}</td>
                                </tr>
                                <tr>
                                    <th>Tgl Kehadiran</th>
                                    <td>{{ date('d M Y', strtotime($kehadiran->tgl_absensi)) }}</td>
                                </tr>
                                <tr>
                                    <th>Jam Masuk</th>
                                    <td>{{ $kehadiran->jam_masuk }}</td>
                                </tr>
                                <tr>
                                    <th>Jam Keluar</th>
                                    @if(!empty($kehadiran->jam_keluar))
                                    <td>{{ $kehadiran->jam_keluar }}</td>
                                    @else
                                    <td><i>Kosong</i></td>
                                    @endif
                                </tr>
                                <tr>
                                    <th>Keterangan</th>
                                    <td>{{ ucfirst($kehadiran->keterangan) }}@if($kehadiran->lembur == 'Y') (Lembur) @endif</td>
                                </tr>
                                <tr>
                                    <th>Terlambat</th>
                                    @if($kehadiran->terlambat < 60)
                                    <td>{{ $kehadiran->terlambat }} menit</td>
                                    @else
                                    <td>{{ floor($kehadiran->terlambat/60) }} jam {{ $kehadiran->terlambat%60 }} menit</td>
                                    @endif
                                </tr>
                                <tr>
                                    <th>Lembur</th>
                                    @if($kehadiran->lembur == 'Y')
                                    <td>Ya</td>
                                    @else
                                    <td>Tidak</td>
                                    @endif
                                </tr>
                                @if(!empty($kehadiran->lokasi))
                                <tr>
                                    <th>Lokasi Kehadiran</th>
                                    <td class="address"></td>
                                </tr>
                                @endif
                            </table>
                        </div>

                        <!-- Mobile -->
                        <div class="row show-on-md-and-down">
                            <div class="col-12">
                                <div class="form-group">
                                    <label><b>Karyawan</b></label><br>
                                    <label>{{ $kehadiran->karyawan->nama }}</label>
                                </div>
                                <div class="form-group">
                                    <label><b>Tgl Kehadiran</b></label><br>
                                    <label>{{ date('d M Y', strtotime($kehadiran->tgl_absensi)) }}</label>
                                </div>
                                <div class="form-group">
                                    <label><b>Jam Masuk</b></label><br>
                                    <label>{{ $kehadiran->jam_masuk }}</label>
                                </div>
                                <div class="form-group">
                                    <label><b>Jam Keluar</b></label><br>
                                    @if(!empty($kehadiran->jam_keluar))
                                    <label>{{ $kehadiran->jam_keluar }}</label>
                                    @else
                                    <label><i>Kosong</i></label>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label><b>Keterangan</b></label><br>
                                    <label>{{ ucfirst($kehadiran->keterangan) }}@if($kehadiran->lembur == 'Y') (Lembur) @endif</label>
                                </div>
                                <div class="form-group">
                                    <label><b>Terlambat</b></label><br>
                                    @if($kehadiran->terlambat < 60)
                                    <label>{{ $kehadiran->terlambat }} menit</label>
                                    @else
                                    <label>{{ floor($kehadiran->terlambat/60) }} jam {{ $kehadiran->terlambat%60 }} menit</label>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label><b>Lembur</b></label><br>
                                    @if($kehadiran->lembur == 'Y')
                                    <label>Ya</label>
                                    @else
                                    <label>Tidak</label>
                                    @endif
                                </div>
                                @if(!empty($kehadiran->lokasi))
                                <div class="form-group">
                                    <label><b>Lokasi Kehadiran</b></label><br>
                                    <label class="address"></label>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>

        @if(!empty($kehadiran->pengajuanLembur))
        <div class="row">
            <div class="col-12">
                <div class="card support-pane-card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title mb-0"><b>Detail Lembur</b></h4>
                        </div>
                        <div class="row table-responsive show-on-lg-and-up">
                            <table class="table table-striped">
                                <tr>
                                    <th>Lama Lembur</th>
                                    <td>@if($kehadiran->pengajuanLembur->lama_lembur >= 60){{ floor($kehadiran->pengajuanLembur->lama_lembur/60) }} jam @endif</td>
                                </tr>
                                <tr>
                                    <th>Upah Lembur</th>
                                    <td>Rp {{ number_format($kehadiran->pengajuanLembur->upah_lembur) }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    @if($kehadiran->pengajuanLembur->status == 'W')
                                    <td><span class="badge badge-warning">Menunggu</span></td>
                                    @elseif($kehadiran->pengajuanLembur->status == 'N')
                                    <td><span class="badge badge-danger">Ditolak</span></td>
                                    @elseif($kehadiran->pengajuanLembur->status == 'Y')
                                    <td><span class="badge badge-success">Disetujui</span></td>
                                    @endif
                                </tr>
                            </table>
                        </div>

                        <!-- Mobile -->
                        <div class="row show-on-md-and-down">
                            <div class="col-12">
                                <div class="form-group">
                                    <label><b>Karyawan</b></label><br>
                                    <label>{{ $kehadiran->karyawan->nama }}</label>
                                </div>
                                <div class="form-group">
                                    <label><b>Tgl Kehadiran</b></label><br>
                                    <label>{{ date('d M Y', strtotime($kehadiran->tgl_absensi)) }}</label>
                                </div>
                                <div class="form-group">
                                    <label><b>Jam Masuk</b></label><br>
                                    <label>{{ $kehadiran->jam_masuk }}</label>
                                </div>
                                <div class="form-group">
                                    <label><b>Jam Keluar</b></label><br>
                                    @if(!empty($kehadiran->jam_keluar))
                                    <label>{{ $kehadiran->jam_keluar }}</label>
                                    @else
                                    <label><i>Kosong</i></label>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label><b>Keterangan</b></label><br>
                                    <label>{{ ucfirst($kehadiran->keterangan) }}@if($kehadiran->lembur == 'Y') (Lembur) @endif</label>
                                </div>
                                <div class="form-group">
                                    <label><b>Terlambat</b></label><br>
                                    @if($kehadiran->terlambat < 60)
                                    <label>{{ $kehadiran->terlambat }} menit</label>
                                    @else
                                    <label>{{ floor($kehadiran->terlambat/60) }} jam {{ $kehadiran->terlambat%60 }} menit</label>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label><b>Lembur</b></label><br>
                                    @if($kehadiran->lembur == 'Y')
                                    <label>Ya</label>
                                    @else
                                    <label>Tidak</label>
                                    @endif
                                </div>
                                @if(!empty($kehadiran->lokasi))
                                <div class="form-group">
                                    <label><b>Lokasi Kehadiran</b></label><br>
                                    <label class="address"></label>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    @if(!empty($kehadiran->foto) || !empty($kehadiran->lokasi))
    <div class="col-md-5">
        @if(!empty($kehadiran->foto))
        <div class="card support-pane-card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0"><b>Foto Kehadiran</b></h4>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <img src="{{ url('uploads/kehadiran/'.$kehadiran->foto->filename) }}" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
        @endif
        @if(!empty($kehadiran->lokasi))
        <div class="card support-pane-card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0"><b>Lokasi Kehadiran</b></h4>
                </div>
                <div class="row">
                    <div id="map" style="width: 100%; height: 250px;"></div>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif
</div>
@endsection


@section('script')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBPiW2EegvKpsoHZd5sXFzxhhuGYLIB7G4"></script>
<script>
    $(function(){
        const showMap = (lat, long) => {
            let property = {
                center:new google.maps.LatLng(lat, long),
                zoom: 18,
                mapTypeId:google.maps.MapTypeId.ROADMAP,
                animation: google.maps.Animation.BOUNCE
            };
            let map = new google.maps.Map(document.querySelector("#map"), property);
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

        const init = () => {
            @if(!empty($kehadiran->lokasi))
            showMap('{{ $kehadiran->lokasi->latitude }}', '{{ $kehadiran->lokasi->longitude }}');
            getAddress('{{ $kehadiran->lokasi->latitude }}', '{{ $kehadiran->lokasi->longitude }}');
            @endif
        }

        init();
    })
</script>
	
@endsection