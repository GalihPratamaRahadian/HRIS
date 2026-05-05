@extends('template.backLayout')


@section('action')
<button class="btn btn-danger filterBtn">
	<i class="mdi mdi-filter"></i> Filter
</button>
@endsection


@section('content')
<div class="row">
	<div class="col-md-12">
		<div class="card support-pane-card">
			<div class="card-body">
				{!! Template::titleBanner($title) !!}

				<div class="table-responsive support-pane">
					<table class='table table-bordered table-hover' id="dataTable">
						<thead>
							<tr>
								<th> Nama Karyawan </th>
								<th> Departemen </th>
								<th> Foto </th>
								<th> Status </th>
								<th> Tanggal Mendaftar </th>
								<th width="80"> Aksi </th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th> Nama Karyawan </th>
								<th> Departemen </th>
								<th> Foto </th>
								<th> Status </th>
								<th> Tanggal Mendaftar </th>
								<th width="80"> Aksi </th>
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
						<label> Status </label>
						<select name="status" style="width: 100%;" required>
							<option value="all"> - Semua - </option>
							@foreach(\App\Models\Registrant::availableStatus() as $value => $label)
							<option value="{{ $value }}"> {{ $label }} </option>
							@endforeach
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
				url : "{{ route('registration') }}"
			},
			columns : [
				{
					data : "employee_name",
					name : 'employee_name'
				},
				{
					data : "department_name",
					name : 'department_name'
				},
				{
					data : "photo",
					name : 'photo',
					orderable : false,
					searchable : false,
				},
				{
					data : "status",
					name : 'status'
				},
				{
					data : "created_at",
					name : 'created_at'
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
			preDrawCallback : settings => {
				let status = filterModal.find('[name="status"]').val();

				settings.ajax.url = `{{ route('registration') }}?status=${status}`;
			},
			order: [[ "4", "desc" ]]
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


	});
</script>
@endsection