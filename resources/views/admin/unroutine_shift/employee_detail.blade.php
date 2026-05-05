@extends('template.backLayout')


@section('action')
@if(UserPermission::check('unroutine_shift', 'c'))
<button class="btn btn-primary" data-toggle="modal" data-target="#import-modal">
	<i class="mdi mdi-upload"></i> Import
</button>
@endif
@endsection


@section('content')
<div class="row">
	<div class="col-md-12">
		<div class="card support-pane-card">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">

					{!! Template::titleBanner($title.' <a href="'.route('employee.detail', $employee->id).'">['. $employee->employee_name .']</a>') !!}

					<div class="btn-toolbar mb-0 d-none d-sm-block" role="toolbar">

					</div>
				</div>

				<div class="table-responsive support-pane">
					<table class='table table-bordered table-hover' id="dataTable">
						<thead>
							<tr>
								<th width="100"> Tgl </th>
								<th> Jenis </th>
								<th> Batas Awal Isi Jam Masuk </th>
								<th> Jam Masuk </th>
								<th> Jam Keluar </th>
								<th> Toleransi Keterlambatan </th>
								<!-- <th width="100px"> Aksi </th> -->
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th> Tgl </th>
								<th> Jenis </th>
								<th> Batas Awal Isi Jam Masuk </th>
								<th> Jam Masuk </th>
								<th> Jam Keluar </th>
								<th> Toleransi Keterlambatan </th>
								<!-- <th width="100px"> Aksi </th> -->
							</tr>
						</tfoot>
					</table>
				</div>

			</div>
		</div>
	</div>
</div>
@endsection


@section('modal')
<div class="modal fade" id="import-modal" tabindex="-1"role="dialog" aria-hidden="true">
	<div class="modal-dialog mt-4" role="document">
		<div class="modal-content">
			<form id="import-form">

				<div class="modal-header">
					<h5 class="modal-title">
						<i class="mdi mdi-upload"></i> Import
					</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>

				<div class="modal-body">

					<p>
						Catatan : <br>
						<ul>
							<li> Import wajib menggunakan template yg kita sediakan </li>
							<li> Download template dengan <a href="{{ route('helper.import_templates', 'Template_Jam_Kerja_Harian.xlsx') }}" download> Klik Disini </a> </li>
							<li> Kolom dengan background merah wajib diisi </li>
							<li> Petunjuk pengisian template dapat anda lihat dengan <a href="{{ route('helper.import_templates', 'Petunjuk_Pengisian_Template_Jam_Kerja_Harian.xlsx') }}" download> Klik Disini </a> </li>
						</ul>
						{!! Template::requiredBanner() !!}

						<div class="form-group">
							<label> File {!! Template::required() !!} </label>
							<input type="file" name="file" class="form-control" accept=".xlsx" required>
						</div>
					</p>
					
				</div>

				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">
						<i class="mdi mdi-upload"></i> Import
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


@section('script')
<script type="text/javascript">
	$(function(){

		const $importModal = $('#import-modal');
		const $importForm = $('#import-form');
		const $importSubmitBtn = $importForm.find(`[type="submit"]`).ladda();

		$('#dataTable').DataTable({
			processing : true,
			serverSide : true,
			autoWidth : false,
			ajax : {
				url : "{{ route('unroutine_shift.employee_detail', $employee->id) }}"
			},
			columns : [
				{
					data : 'date',
					name : 'date'
				},
				{
					data : 'type',
					name : 'type'
				},
				{
					data : 'clock_start_limit',
					name : 'clock_start_limit'
				},
				{
					data : 'clock_start',
					name : 'clock_start'
				},
				{
					data : 'clock_end',
					name : 'clock_end'
				},
				{
					data : 'late_tolerance',
					name : 'late_tolerance'
				},
				// {
				// 	data : 'action',
				// 	name : 'action',
				// 	orderable : false,
				// 	searchable : false,
				// }
			],
			order: [[ '0', 'desc' ]],
			drawCallback : settings => {
				renderedEvent();
			}
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
						reloadDT();
						ajaxSuccessHandling(response)
					})
					.fail(error => {
						ajaxErrorHandling(error)
					})
				})
			});
		}


		$importForm.on('submit', function(e){
			e.preventDefault();

			let formData = new FormData(this);
			$importSubmitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url: `{{ route('unroutine_shift.import', $employee->id) }}`,
				method: 'post',
				data: formData,
				dataType: 'json',
				contentType: false,
				processData: false,
			})
			.done(response => {
				ajaxSuccessHandling(response);
				$importForm[0].reset();
				$('#dataTable').DataTable().ajax.reload();
				$importModal.modal('hide')
				$importSubmitBtn.ladda('stop')
			})
			.fail(error => {
				ajaxErrorHandling(error, $importForm)
				$importSubmitBtn.ladda('stop')
			})
		})


	});
</script>
@endsection