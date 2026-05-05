@extends('template/backEnd')

@section('style')
<style>
    .mobile-button {
        display: none;
    }

    @media screen and (max-width: 575px) {
        .mobile-button {
            display: flex;
        }
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <h4 class="page-title">Kehadiran</h4>
        <div class="d-flex align-items-center">
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Kehadiran</li>
            </ol>
        </nav>
    </div>
</div>


<div class="row">
    <div class="col-md-12">
        <div class="card support-pane-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0">Kehadiran</h4>
                </div>
                <div class="row my-4">
                    <div style="width: auto;">
                        <div id="dateRange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                            <i class="mdi mdi-calendar"></i>&nbsp;
                            <span></span> <i class="mdi mdi-chevron-down"></i>
                        </div>
                    </div>
                </div>
                <div class="table-responsive support-pane no-wrap">
                    <table class="table table-hover table-striped" id="dataTable">
                        <thead>
                            <tr>
                                <th>Tgl Absensi</th>
                                <th>Karyawan</th>
                                <th>Jam Masuk</th>
                                <th>Jam Keluar</th>
                                <th width="100px"><i class="mdi mdi-cogs"></i></th>
                            </tr>
                        </thead>
                        <!-- ServerSide -->
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('script')
<script type="text/javascript">
    $(function(){
        
        const dataTable = () => {
            $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax : {
                    url : "{{ url('kehadiran/getdt') }}"
                },
                columns : [
                    {
                        data : 'tgl_absensi',
                        name : 'tgl_absensi'
                    },
                    {
                        data : 'karyawan',
                        name : 'karyawan.nama'
                    },
                    {
                        data : 'jam_masuk',
                        name : 'jam_masuk'
                    },
                    {
                        data : 'jam_keluar',
                        name : 'jam_keluar'
                    },
                    {
                        data : 'action',
                        name : 'action',
                        orderable : false,
                        searchable : false,
                    },
                ],
                autoWidth : false,
            })
        }

        const reload = () => {
            $('#dataTable').DataTable().ajax.reload();
        }

        const range = (start, end) => {
            let month = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                startHtml = start.format('D') + " " + month[start.format('M') - 1] + " " + start.format('YYYY'),
                endHtml = end.format('D') + " " + month[end.format('M') - 1] + " " + end.format('YYYY');
            $('#dateRange span').html(startHtml + ' - ' + endHtml);
            // $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            // $('#')
            $('#dataTable').DataTable().ajax.url(`{{ url('kehadiran/getdt') }}?startDate=${start.format("YYYY-MM-DD")}&endDate=${end.format("YYYY-MM-DD")}`);
            reload();
        }

        const kehadiranRange = () => {
            $('#dateRange').daterangepicker({
                startDate: moment(),
                endDate: moment(),
                ranges: {
                   'Hari ini': [moment(), moment()],
                   'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                   '7 hari terakhir': [moment().subtract(6, 'days'), moment()],
                   '30 hari terakhir': [moment().subtract(29, 'days'), moment()],
                   'Bulan ini': [moment().startOf('month'), moment().endOf('month')],
                   'Bulan kemarin': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }, range);
        }




        $('.workEnd').on('click', function(){
            swal({
                title: "Absen pulang?",
                // text: "You will not be able to recover this imaginary file!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Ya!",
                cancelButtonText: "Batal",
                closeOnConfirm: false
            }, () => {
                ajaxSetup();
                $.ajax({
                    url : "{{ url('kehadiran/selesai') }}",
                    method : 'post',
                    success : res => {
                        swal("Berhasil!", "Berhasil absen pulang.", "success");
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    }
                })
            });
        });


        const init = () => {
            dataTable();
            kehadiranRange();
            range(moment(), moment());
        }

        init();
    });
</script>
@endsection