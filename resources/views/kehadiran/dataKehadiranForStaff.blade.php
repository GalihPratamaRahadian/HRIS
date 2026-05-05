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

<div class="row show-on-lg-and-up">
    <div class="col-md-12">
        <div class="card support-pane-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0">
                        Kehadiran
                    </h4>
                    <div class="card-toolbar mb-0" role="toolbar" aria-label="Toolbar with button groups">

                    </div>
                </div>
                <div class="table-responsive support-pane no-wrap">
                    <table class="table table-hover table-striped" id="dataTable">
                        <thead>
                            <tr>
                                <th>Tgl Absensi</th>
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

<div class="row show-on-md-and-down">
    <div class="col-12">
        <div class="row">
            <div class="col-12 grid-margin">
                <button class="btn btn-primary filterBtn">
                    <i class="mdi mdi-filter"></i> Filter
                </button>
                <div class="w-100 show-on-md-and-down mt-3">
                    <div class="input-group">
                        <input type="text" class="form-control" name="kehadiranSearch" placeholder="Cari.." required>
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row show-on-md-and-down">
            <div class="col-lg-12" id="mobileData">

                <!-- Ajax -->

            </div>
            <div class="col-lg-12" id="mobilePagination">
                
            </div>
            <input type="hidden" name="pagination" value="1">
        </div>
    </div>
</div>
@endsection


@section('modal')
<div class="modal fade" id="filterModal" role="dialog" aria-hidden="true">
    <div class="modal-dialog mt-4" role="document">
        <div class="modal-content">
            <form id="filterForm">
                <div class="modal-header">
                    <h5 class="modal-title">Filter</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label><b>Keterangan</b></label>
                        <select class="sel2" name="keterangan" style="width: 100%;" required>
                            <option value="all">Semua</option>
                            <option value="hadir">Hadir</option>
                            <option value="sakit">Sakit</option>
                            <option value="izin">Izin</option>
                            <option value="cuti">Cuti</option>
                            <option value="libur">Libur</option>
                            <option value="alpa">Alpa</option>
                        </select>
                    </div>
                    <div class="form-group hadirFilter" style="display: none;">
                        <label><b>Tampilkan Kehadiran</b></label>
                        <select class="sel2" name="hadirFilter" style="width: 100%;">
                            <option value="all">Semua</option>
                            <option value="lembur">Hanya Lembur</option>
                            <option value="aktif">Hari Aktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"><i class="mdi mdi-filter"></i> Terapkan</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal"><i class="mdi mdi-close"></i> Tutup</button>
                </div>
            </form>
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


        const mainButton = () => {
            $('.filterBtn').on('click', function(){
                $('#filterModal').modal('show');
            })

            $(document).on('click', '.page-link', function(){
                let page = $(this).attr('data-page');
                if(page !== '#') {
                    $('[name="pagination"]').val(page);
                    getForMobileData();
                    getForMobilePagination();
                }
            })
        }


        const get = async () => {
            let result = null,
                keterangan = $('#filterForm').find('[name="keterangan"]').val(),
                hadirFilter = $('#filterForm').find('[name="hadirFilter"]').val(),
                page = $('[name="pagination"]').val(),
                search = $('[name="kehadiranSearch"]').val();
            await $.get({
                url : `{{ url('kehadiran/get') }}?keterangan=${keterangan}&hadir=${hadirFilter}&page=${page}&search=${search}`,
                dataType : 'json',
                success : res => {
                    result = res;
                }
            })

            return result;
        }


        const getForMobileData = () => {
            get()
            .then(res => {
                let data = res.data;
                let html = '';
                if(data.length > 0) {
                    $.each(data, (i , d) => {
                        html += `
                        <div class="row">
                            <div class="col-lg-12 grid-margin">
                                <div class="card support-pane-card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h4 class="card-title mb-0">Kehadiran ${d.tgl_absensi}</h4>
                                        </div>
                                        <div class="support-pane">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label><b>Keterangan</b></label><br>`;
                                            if(d.keterangan == 'hadir') {
                                                html += `<span class="badge badge-success">Hadir</span>`;
                                            } else if(d.keterangan == 'libur') {
                                                html += `<span class="badge badge-primary">Libur</span>`;
                                            } else if(d.keterangan == 'cuti') {
                                                html += `<span class="badge badge-primary">Cuti</span>`;
                                            } else if(d.keterangan == 'sakit') {
                                                html += `<span class="badge badge-warning">Sakit</span>`;
                                            } else if(d.keterangan == 'izin') {
                                                html += `<span class="badge badge-primary">Izin</span>`;
                                            } else if(d.keterangan == 'alpa') {
                                                html += `<span class="badge badge-danger">Alpa</span>`;
                                            }

                                            html +=
                                                    `</div>
                                                </div>`;

                                            if(!isEmpty(d.pengajuan_lembur)) {
                                                html += `
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label><b>Status Lembur</b></label><br>`
                                                if(d.pengajuan_lembur.status == 'W') {
                                                    html += `<span class="badge badge-warning">Menunggu</span>`
                                                } else if(d.pengajuan_lembur.status == 'Y') {
                                                    html += `<span class="badge badge-success">Disetujui</span>`
                                                } else if(d.pengajuan_lembur.status == 'N') {
                                                    html += `<span class="badge badge-danger">Ditolak</span>`
                                                }
                                                html += `
                                                    </div>
                                                </div>`;
                                            }

                                            html += `
                                            </div>`;

                                            if(d.keterangan == 'hadir') {
                                            html +=
                                            `<div class="row">
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label><b>Jam Masuk</b></label><br>
                                                        ${d.jam_masuk}
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label><b>Jam Keluar</b></label><br>`;
                                            if(!isEmpty(d.jam_keluar)) {
                                                html += `${d.jam_keluar}`;
                                            } else {
                                                html += `Belum Clock-Out`;
                                            }
                                            html +=
                                                    `</div>
                                                </div>
                                            </div>`;
                                            }

                                            if(d.terlambat > 0 || d.kekurangan > 0) {
                                            html += `
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label><b>Terlambat</b></label><br>`
                                            if(Math.floor(d.terlambat/60) > 0) {
                                                html += `${Math.floor(d.terlambat/60)} jam`;
                                            }
                                            if(d.terlambat%60 > 0) {
                                                if(Math.floor(d.terlambat/60) > 0){html += " ";}
                                                html += `${d.terlambat%60} menit`;
                                            }
                                            html += 
                                                    `</div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label><b>Pulang Awal</b></label><br>`;
                                            if(Math.floor(d.kekurangan/60) > 0) {
                                                html += `${Math.floor(d.kekurangan/60)} jam`;
                                            }
                                            if(d.kekurangan%60 > 0) {
                                                if(Math.floor(d.kekurangan/60) > 0){html += " ";}
                                                html += `${d.kekurangan%60} menit`;
                                            }
                                            if(d.kekurangan == 0) {
                                                html += `0 menit`;
                                            }
                                            html += 
                                                    `</div>
                                                </div>
                                            </div>`;
                                            }

                                            if(!isEmpty(d.pengajuan_lembur)) {
                                            html += `
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label><b>Lama Lembur</b></label><br>`;
                                            if(Math.floor(d.pengajuan_lembur.lama_lembur/60) > 0) {
                                                html += `${Math.floor(d.pengajuan_lembur.lama_lembur/60)} jam`;
                                            }
                                            if(d.pengajuan_lembur.lama_lembur%60 > 0) {
                                                if(Math.floor(d.pengajuan_lembur.lama_lembur/60) > 0){html += " ";}
                                                html += `${d.pengajuan_lembur.lama_lembur%60} menit`;
                                            }
                                            if(d.pengajuan_lembur.lama_lembur == 0) {
                                                html += `0 menit`;
                                            }
                                            html += 
                                                    `</div>
                                                </div>
                                            </div>`;
                                            }

                                            if(d.keterangan == 'cuti' || d.keterangan == 'sakit' || d.keterangan == 'izin' || d.keterangan == 'libur') {
                                            html += `
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label><b>Deskripsi</b></label><br>
                                                        <label>${d.deskripsi}</label>
                                                    </div>
                                                </div>
                                            </div>`;
                                            }

                                            html += `
                                            <div class="row">
                                                <div class="col-12">
                                                    <a href="{{ url('kehadiran/detail') }}/${d.id}" class="btn btn-primary btn-sm btn-block">
                                                        <i class="mdi mdi-eye"></i> Lihat Detail
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                    })
                } else {
                    html += `
                    <div class="row">
                        <div class="col-lg-12 grid-margin">
                            <div class="card support-pane-card">
                                <div class="card-body">
                                    <div class="support-pane">
                                        <p align="center" class="m-0"><i>Tidak ada kehadiran</i></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`;
                }

                $('#mobileData').html(html);
            })
        }



        const getForMobilePagination = () => {
            get()
            .then(res => {
                let html = '',
                    pagination = res.pagination,
                    active = parseInt(pagination.active),
                    start = parseInt(pagination.start),
                    end = parseInt(pagination.end),
                    total = parseInt(pagination.total),
                    limit = parseInt(pagination.limit);

                html = `
                <nav>
                    <ul class="pagination rounded d-flex justify-content-center">`;

                if(active == 1) {
                    html+= `
                        <li class="page-item disabled"><a class="page-link" href="javascript:void(0);" data-page="#"><i class="mdi mdi-chevron-left"></i></a></li>`;
                } else {
                    html+= `
                        <li class="page-item"><a class="page-link" href="javascript:void(0);" data-page="${active - 1}"><i class="mdi mdi-chevron-left"></i></a></li>`;
                }

                if(active > 3) {
                    html += `
                        <li class="page-item disabled"><a class="page-link" href="javascript:void(0);" data-page="#">..</a></li>`;
                }

                if(start < active) {
                    for (let i = start; i < active; i++) {
                        html += `
                        <li class="page-item"><a class="page-link" href="javascript:void(0);" data-page="${i}">${i}</a></li>`;
                    }
                }

                html += `
                        <li class="page-item active"><a class="page-link" href="javascript:void(0);" data-page="#">${active}</a></li>`;


                if(active < end) {
                    for (let i = active + 1; i <= end; i++) {
                        html += `
                        <li class="page-item"><a class="page-link" href="javascript:void(0);" data-page="${i}">${i}</a></li>`;
                    }
                }

                if(end < total) {
                    html += `
                        <li class="page-item disabled"><a class="page-link" href="javascript:void(0);" data-page="#">..</a></li>`;
                }

                if(active == end) {
                    html += `
                        <li class="page-item disabled"><a class="page-link" href="javascript:void(0);" data-page="#"><i class="mdi mdi-chevron-right"></i></a></li>`;
                } else {
                    html += `
                        <li class="page-item"><a class="page-link" href="javascript:void(0);" data-page="${active + 1}"><i class="mdi mdi-chevron-right"></i></a></li>`;
                }

                html += `
                    </ul>
                </nav>`;

                $('#mobilePagination').html(html);
            })
        }


        const initValue = () => {

        }

        const formSubmit = () => {
            $('#filterForm').on('submit', function(e){
                e.preventDefault();

                let keterangan = $(this).find('[name="keterangan"]').val(),
                    hadirFilter = $(this).find('[name="hadirFilter"]').val();

                $('#dataTable').DataTable().ajax.url(`{{ url('kehadiran/getdt') }}?keterangan=${keterangan}&hadir=${hadirFilter}`);
                reload();
                getForMobileData();

                $('#filterModal').modal('hide');
            })
        }


        const formChange = () => {
            $('#filterForm').find('[name="keterangan"]').on('change', function(){
                let keterangan = $(this).val();

                if(keterangan == 'hadir') {
                    $('#filterForm').find('.hadirFilter').show();
                } else {
                    $('#filterForm').find('.hadirFilter').hide();
                }
            })

            $('[name="kehadiranSearch"]').on('keyup', function(){
                getForMobileData();
                getForMobilePagination();
            })
        }

        const init = () => {
            initValue();
            dataTable();
            formSubmit();
            formChange();
            getForMobileData();
            getForMobilePagination();
            mainButton();
        }

        init();
    });
</script>
@endsection