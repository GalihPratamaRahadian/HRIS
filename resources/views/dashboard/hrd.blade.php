@extends('template.backEnd')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h4 class="page-title">Dashboard</h4>
        <div class="d-flex align-items-center">
        </div>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-3">
        <div class="card bg-primary">
            <div class="card-body p-3 align-items-center text-white">
                <div class="card-icon" style="font-size: 48px;">
                    <i class="mdi mdi-account"></i>
                </div>
                <div class="d-inline-block ml-5">
                    <h3>Karyawan</h3>
                    <span>{{ $karyawan }} Orang</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success">
            <div class="card-body p-3 align-items-center text-white">
                <div class="card-icon" style="font-size: 48px;">
                    <i class="mdi mdi-account-check"></i>
                </div>
                <div class="d-inline-block ml-5">
                    <h3>Telah Absen</h3>
                    <span>{{ $kehadiran->hadir }} Orang</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning">
            <div class="card-body p-3 align-items-center text-white">
                <div class="card-icon" style="font-size: 48px;">
                    <i class="mdi mdi-account-alert"></i>
                </div>
                <div class="d-inline-block ml-5">
                    <h3>Izin</h3>
                    <span>{{ $kehadiran->izin }} Orang</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger">
            <div class="card-body p-3 align-items-center text-white">
                <div class="card-icon" style="font-size: 48px;">
                    <i class="mdi mdi-account-plus"></i>
                </div>
                <div class="d-inline-block ml-5">
                    <h3>Sakit</h3>
                    <span>{{ $kehadiran->sakit }} Orang</span>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="card support-pane-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0"><i class="mdi mdi-account mr-2"></i>Karyawan Habis Kontrak</h4>
                </div>
                <div class="table-responsive support-pane no-wrap" style="max-height: 300px;">
                    <table class="table table-hover table-striped" id="habisKontrak">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Kontrak Akhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($habisKontrak as $d)
                            <tr>
                                <td>{{ $d->karyawan->nama }}</td>
                                <td>{{ date('d-m-Y', strtotime($d->akhir_kontrak)) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card support-pane-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0"><i class="mdi mdi-account mr-2"></i>Belum atur kontrak</h4>
                </div>
                <div class="table-responsive support-pane no-wrap" style="max-height: 300px;">
                    <table class="table table-hover table-striped" id="belumAtur">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Departemen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($belumAtur as $d)
                            <tr>
                                <td>{{ $d->nama }}</td>
                                <td>{{ $d->departemen }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function(){
        $('#habisKontrak').DataTable();
        $('#belumAtur').DataTable();
    })
</script>
	
@endsection