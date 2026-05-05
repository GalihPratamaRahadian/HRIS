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
        <form id="mainForm">
            <div class="row">
                <div class="col-lg-5 grid-margin">
                    <div class="card support-pane-card">
                        <div class="card-body">
                            <div class="support-pane">
                                <div class="form-group">
                                    <label><b>Tanggal</b></label> <br>
                                    <label>{{ App::make('day', [date('N')]) }}, {{ date('d') }} {{ App::make('month', [date('n')]) }} {{ date('Y') }}</label>
                                </div>
                                <div class="form-group">
                                    <label><b>Jam</b></label> <br>
                                    <label>{{ date('H:i:s') }}</label>
                                </div>
                                @if($potongan->pulangAwal > 0)
                                <div class="form-group">
                                    <label><b>Potongan (Belum jam keluar)</b></label> <br>
                                    <label>Rp {{ number_format($potongan->pulangAwal) }} ({{ $menitKekurangan }} menit)</label>
                                </div>
                                @endif
                                @if($potongan->telat > 0)
                                <div class="form-group">
                                    <label><b>Potongan (Terlambat)</b></label> <br>
                                    <label>Rp {{ number_format($potongan->telat) }} ({{ $hadir->terlambat }} menit)</label>
                                </div>
                                @endif
                                @if($hadir->lembur == 'N')
                                <div class="form-group">
                                    <label><b>Gaji Harian</b></label> <br>
                                    <label>Rp {{ number_format($gajiHarian - $potongan->pulangAwal - $potongan->telat) }} @if($potongan->pulangAwal > 0 || $potongan->telat) (Setelah dipotong) @endif</label>
                                </div>
                                @else
                                <div class="form-group">
                                    <label><b>Upah Lembur</b></label> <br>
                                    <label>Rp {{ number_format($gajiLembur) }}</label>
                                </div>
                                @endif
                                <div class="form-group">
                                    <button class="btn btn-danger btn-block" type="submit">
                                        Jam Keluar <i class="mdi mdi-logout"></i>
                                    </button>
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

@section('script')
<script type="text/javascript">
	$(function(){


        const formSubmit = () => {
            $('#mainForm').on('submit', function(e){
                e.preventDefault();

                let submitBtn = $(this).find('[type="submit"]');

                processingButton(submitBtn);
                ajaxSetup();
                $.ajax({
                    url : `{{ url("kehadiran/clockout") }}`,
                    method : 'post',
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
                        processingButtonDone(submitBtn, `Jam Keluar <i class="mdi mdi-logout"></i>`);
                    }
                })
            })
        }


        const init = () => {
            formSubmit();
        }

        init();
	});
</script>
@endsection
