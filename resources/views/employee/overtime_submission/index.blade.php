@extends('template.backLayout')


@section('action')
<button class="btn btn-primary" data-toggle="modal" data-target="#filterModal">
	<i class="mdi mdi-filter"></i> Filter
</button>
<a href="{{ route('employee.overtime_submission.create') }}" class="btn btn-success">
	<i class="mdi mdi-plus-thick"></i> Buat
</a>
@endsection


@section('content')
<div class="row">
	<div class="col-md-12">
		<div class="card support-pane-card">
			<div class="card-body">

				<div class="table-responsive support-pane">
					<table class='table table-bordered table-hover' id="dataTable">
						<thead>
							<tr>
								<th> Diajukan Pada </th>
								<th> Alasan </th>
								<th> Mulai Lembur </th>
								<th> Selesai Lembur </th>
								<th> Jam Awal </th>
								<th> Jam Akhir </th>
								<th> Deskripsi </th>
								<th> Status </th>
								<th width="100px"> Aksi </th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th> Diajukan Pada </th>
								<th> Alasan </th>
								<th> Mulai Lembur </th>
								<th> Selesai Lembur </th>
								<th> Jam Awal </th>
								<th> Jam Akhir </th>
								<th> Deskripsi </th>
								<th> Status </th>
								<th width="100px"> Aksi </th>
							</tr>
						</tfoot>
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

		const $filterForm = $('#filterForm');

		$filterForm.find(`[name="id_overtime_reason"]`).select2({
			'placeholder': '- Pilih Alasan -'
		})

		$filterForm.find(`[name="status"]`).select2({
			'placeholder': '- Pilih Status -'
		})

		$('#dataTable').DataTable({
			processing : true,
			serverSide : true,
			autoWidth : false,
			ajax : {
				url : "{{ route('employee.overtime_submission') }}"
			},
			columns : [
				{
					data : 'created_at',
					name : 'created_at'
				},
				{
					data : 'overtime_reason.reason',
					name : 'overtime_reasons.reason'
				},
				{
					data : 'start_date',
					name : 'start_date'
				},
				{
					data : 'end_date',
					name : 'end_date'
				},
				{
					data : 'clock_start',
					name : 'clock_start',
					visible : false
				},
				{
					data : 'clock_end',
					name : 'clock_end',
					visible : false
				},
				{
					data : 'description',
					name : 'description'
				},
				{
					data : 'status',
					name : 'status'
				},
				{
					data : 'action',
					name : 'action',
					orderable : false,
					searchable : false,
				}
			],
			drawCallback : settings => {
				renderedEvent();
			},
			preDrawCallback: settings => {
				let formData = $filterForm.serialize()
				settings.ajax.url = `{{ route('employee.overtime_submission') }}?${formData}`;
			},
			order : [ ['0', 'desc'] ]
		})


		const reloadDT = () => {
			$('#dataTable').DataTable().ajax.reload();
		}


		const renderedEvent = () => {
			$('.delete').off('click')
			$('.delete').on('click', function(){
				let href = $(this).data('href')

				confirmation('Yakin ingin dihapus?', () => {
					ajaxSetup();
					$.ajax({
						url : href,
						method : 'delete',
					})
					.done(response => {
						ajaxSuccessHandling(response)
						reloadDT();
					})
					.fail(error => {
						ajaxErrorHandling(error)
					})
				})

			});
		}

		$filterForm.on('submit', function(e){
			e.preventDefault();
			reloadDT();
			$('#filterModal').modal('hide');
		})

		$('.set-date').on('click', function(){
			const { target, value } = $(this).data()
			$(target).val(value).trigger('change')
		})

		@if(isset($_GET['status']))
		$filterForm.find(`[name="status"]`).val(`{{ $_GET['status'] }}`).trigger('change')
		@endif

	});
</script>
@endsection


@section('modal')
<div class="modal fade" id="filterModal" role="dialog" aria-hidden="true">
	<div class="modal-dialog mt-4" role="document">
		<div class="modal-content">
			<form id="filterForm">

				<div class="modal-header">
					<h5 class="modal-title">
						Filter
					</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>

				<div class="modal-body">

					<div class="row">
						<div class="col-lg-6">
							<div class="form-group">
								<label> Awal Mulai Lembur </label>
								<input type="date" name="start_date" class="form-control" id="start-date">
								<div class="text-small mt-1">
									<a href="javascript:void(0);" class="set-date" data-target="#start-date" data-value="{{ today()->addDays(-1)->format('Y-m-d') }}"> Kemarin </a> |
									<a href="javascript:void(0);" class="set-date" data-target="#start-date" data-value="{{ today()->format('Y-m-d') }}"> Hari Ini </a> |
									<a href="javascript:void(0);" class="set-date" data-target="#start-date" data-value="{{ today()->format('Y-m-01') }}"> Awal Bulan </a> |
									<a href="javascript:void(0);" class="set-date" data-target="#start-date" data-value="{{ today()->format('Y-m-t') }}"> Akhir Bulan </a> |
									<a href="javascript:void(0);" class="set-date" data-target="#start-date" data-value=""> Kosongkan </a>
								</div>
							</div>
						</div>

						<div class="col-lg-6">
							<div class="form-group">
								<label> Akhir Selesai Lembur </label>
								<input type="date" name="end_date" class="form-control" id="end-date">
								<div class="text-small mt-1">
									<a href="javascript:void(0);" class="set-date" data-target="#end-date" data-value="{{ today()->addDays(-1)->format('Y-m-d') }}"> Kemarin </a> |
									<a href="javascript:void(0);" class="set-date" data-target="#end-date" data-value="{{ today()->format('Y-m-d') }}"> Hari Ini </a> |
									<a href="javascript:void(0);" class="set-date" data-target="#end-date" data-value="{{ today()->format('Y-m-01') }}"> Awal Bulan </a> |
									<a href="javascript:void(0);" class="set-date" data-target="#end-date" data-value="{{ today()->format('Y-m-t') }}"> Akhir Bulan </a> |
									<a href="javascript:void(0);" class="set-date" data-target="#end-date" data-value=""> Kosongkan </a>
								</div>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label> Alasan Lembur </label>
						<select name="id_overtime_reason" style="width: 100%;" required>
							<option value="all"> - Semua Alasan - </option>

							@foreach(\App\Models\OvertimeReason::all() as $reason)
							<option value="{{ $reason->id }}">
								{{ $reason->reason }}
							</option>
							@endforeach

						</select>
					</div>

					<div class="form-group">
						<label> Status Pengajuan </label>
						<select name="status" style="width: 100%;" required>
							<option value="all"> - Semua Status - </option>
							<option value="wait"> Menunggu </option>
							<option value="approved"> Disetujui </option>
							<option value="rejected"> Ditolak </option>
						</select>
					</div>
					
				</div>

				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">
						<i class="mdi mdi-filter"></i> Filter
					</button>
					<button type="button" class="btn btn-light" data-dismiss="modal">
						<i class="mdi mdi-close"></i> Tutup
					</button>
				</div>

			</form>
		</div>
	</div>
</div>
@endsection