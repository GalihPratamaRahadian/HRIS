@extends('template.backLayout')


@section('action')
<button class="btn btn-danger filterBtn">
	<i class="mdi mdi-filter"></i> Filter
</button>
@if(UserPermission::check('attendance_permission_submission', 'c'))
<a class="btn btn-primary" href="{{ route('admin.attendance_permission_submission.create') }}">
	<i class="mdi mdi-plus-thick"></i> Tambah
</a>
@endif
@endsection


@section('content')

@if(($pending = \App\Models\AttendancePermissionSubmission::amountOfAttendancePermissionSubmissionsWithStatusPending()) > 0)
<div class="alert alert-primary">
	Ada {{ $pending }} pengajuan menunggu persetujuan. <a href="{{ route('admin.attendance_permission_submission') }}?status=wait"> Klik disini </a> untuk melihat.
</div>
@endif

<div class="row">
	<div class="col-md-12">
		<div class="card support-pane-card">
			<div class="card-body">

				{!! Template::titleBanner($title) !!}


				<div class="table-responsive support-pane">
					<table class='table table-bordered table-hover' id="dataTable">
						<thead>
							<tr>
								<th> Diajukan Pada </th>
								<th> Karyawan </th>
								<th> Jenis </th>
								<th> Alasan </th>
								<th> Tanggal </th>
								<th> Jam </th>
								<th> Status </th>
								<th width="100px"> Aksi </th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th> Diajukan Pada </th>
								<th> Karyawan </th>
								<th> Jenis </th>
								<th> Alasan </th>
								<th> Tanggal </th>
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

					<div class="form-group">
						<label> Status Pengajuan </label>
						<select name="status" style="width: 100%;" required>
							<option value="all"> - Semua - </option>
							<option value="wait"> Menunggu </option>
							<option value="approved"> Disetujui </option>
							<option value="rejected"> Ditolak </option>
							<option value="canceled"> Dibatalkan </option>
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


@section('script')
<script type="text/javascript">
	$(function(){

		let filterModal = $('#filterModal');

		filterModal.find('[name="status"]').select2({
			'placeholder' : '- Pilih Status -'
		})
		filterModal.find('[name="status"]').val(`{{ $_GET['status'] ?? 'all' }}`).trigger('change')

		$('.filterBtn').on('click', function(){
			filterModal.modal('show')
		})

		filterModal.find('#filterForm').on('submit', function(e){
			e.preventDefault();

			filterModal.modal('hide')
			$('#dataTable').DataTable().ajax.reload();
		})

		$('#dataTable').DataTable({
			processing : true,
			serverSide : true,
			autoWidth : false,
			ajax : {
				url : "{{ route('admin.attendance_permission_submission') }}"
			},
			columns : [
				{
					data : 'created_at',
					name : 'created_at'
				},
				{
					data : 'employee.employee_name',
					name : 'employees.employee_name'
				},
				{
					data : 'type',
					name : 'type'
				},
				{
					data : 'reason',
					name : 'reason'
				},
				{
					data : 'date',
					name : 'date'
				},
				{
					data : 'time',
					name : 'time'
				},
				{
					data : 'status',
					name : 'status'
				},
				{
					data : 'admin_action',
					name : 'admin_action',
					orderable : false,
					searchable : false,
				}
			],

			drawCallback : () => {
				renderedEvent();
			},
			preDrawCallback : settings => {
				let status = filterModal.find('[name="status"]').val();

				settings.ajax.url = `{{ route('admin.attendance_permission_submission') }}?status=${status}`;
			},
			order: [[ '0','desc']]
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

			$('.approve').off('click')
			$('.approve').on('click', function(){
				let href = $(this).data('href')

				
				confirmation('Yakin ingin Disetujui?', () => {
					ajaxSetup();
					$.ajax({
						url: href,
						method: 'post',
						dataType: 'json',
					})
					.done(response => {
						ajaxSuccessHandling(response)
						reloadDT();
					})
					.fail(error => {
						ajaxErrorHandling(error)
					})
				})
			})

			$('.reject').off('click')
			$('.reject').on('click', function(){

				let href = $(this).data('href')
				confirmation('Yakin ingin Ditolak?', () => {
					ajaxSetup();
					$.ajax({
						url: href,
						method: 'post',
						dataType: 'json',
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

			$('.cancel').off('click')
			$('.cancel').on('click', function(){

				let href = $(this).data('href')
				confirmation('Yakin ingin Dibatalkan?', () => {
					ajaxSetup();
					$.ajax({
						url: href,
						method: 'post',
						dataType: 'json',
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


	});
</script>
@endsection