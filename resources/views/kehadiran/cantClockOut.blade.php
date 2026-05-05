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
                <li class="breadcrumb-item active" aria-current="page">Jam Keluar</li>
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
                                <div class="row">
                                    <div class="col text-center" style="font-size: 100px;">
                                        @if($data->tolerance == true)
                                        <i class="mdi mdi-alert-circle-outline text-warning"></i>
                                        @else
                                        <i class="mdi mdi-close-circle-outline text-danger"></i>
                                        @endif
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col text-center">
                                        <span>Hai, <b>{{ App::make('karyawan')->nama }}</b></span><br>
                                        <span>{{ $data->msg }}</span>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col text-center">
                                        <a href="{{ url('dashboard') }}"><i class="mdi mdi-chevron-left"></i> Kembali ke Dashboard</a>
                                        @if($data->tolerance == true)
                                        <br>
                                        <a href="{{ url('kehadiran/clockout?allow=true') }}">Lanjutkan Isi Jam Keluar <i class="mdi mdi-chevron-right"></i></a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
