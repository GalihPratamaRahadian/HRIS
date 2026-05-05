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
                    <h5>Pengunjung aktif</h5>
                    <span id="jmlAktif">0 Orang</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success">
            <div class="card-body p-3 align-items-center text-white">
                <div class="card-icon" style="font-size: 48px;">
                    <i class="mdi mdi-chart-line"></i>
                </div>
                <div class="d-inline-block ml-5">
                    <h5>Pengunjung hari ini</h5>
                    <span id="jmlSehari">0 Orang</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning">
            <div class="card-body p-3 align-items-center text-white">
                <div class="card-icon" style="font-size: 48px;">
                    <i class="mdi mdi-chart-bar-stacked"></i>
                </div>
                <div class="d-inline-block ml-5">
                    <h5>Pengunjung bulan ini</h5>
                    <span id="jmlSebulan">0 Orang</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger">
            <div class="card-body p-3 align-items-center text-white">
                <div class="card-icon" style="font-size: 48px;">
                    <i class="mdi mdi-office-building"></i>
                </div>
                <div class="d-inline-block ml-5">
                    <h5>Site</h5>
                    <span id="jmlSite">0</span>
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
                    <h4 class="card-title mb-0"><i class="mdi mdi-account mr-2"></i>Pengunjung Diizinkan</h4>
                </div>
                <div class="table-responsive support-pane no-wrap" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-hover table-striped" id="habisKontrak">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Valid Hingga</th>
                            </tr>
                        </thead>
                        <tbody id="dataDiizinkan">
                            
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
                    <h4 class="card-title mb-0"><i class="mdi mdi-account-tie mr-2"></i>Karyawan di Site</h4>
                </div>
                <div class="table-responsive support-pane no-wrap" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-hover table-striped" id="habisKontrak">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Departemen</th>
                            </tr>
                        </thead>
                        <tbody id="dataKaryawan">
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function(){
        function getData() {
            $.ajax({
                url : "{{ route('dash.hse') }}",
                dataType : 'json',
                success : function (response) {
                   $('#jmlAktif').html(response.jmlAktif+' Orang');
                   $('#jmlSehari').html(response.jmlSehari+' Orang');
                   $('#jmlSebulan').html(response.jmlSebulan+' Orang');
                   $('#jmlSite').html(response.jmlSite);
                   $('#dataDiizinkan').html(response.dataDiizinkan);
                   $('#dataKaryawan').html(response.dataKaryawan);
                }
            })
        }

        getData();

        setInterval(getData, 10000);
    })
</script>
	
@endsection