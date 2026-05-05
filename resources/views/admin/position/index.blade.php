@extends('template.backLayout')


@section('action')
@if(UserPermission::check('position', 'c'))
<a href="{{ route('admin.position.create') }}" class="btn btn-success">
	<i class="mdi mdi-plus-thick"></i> Tambah
</a>
@endif

<button class="btn btn-danger filterBtn">
	<i class="mdi mdi-filter"></i> Filter
</button>
@endsection


@section('content')
<div class="row">
	<div class="col-md-12">
		<div class="card support-pane-card">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					{!! Template::titleBanner($title) !!}
				</div>

				<div class="table-responsive support-pane">
					<table class='table table-bordered table-hover' id="dataTable">
						<thead>
							<tr>
								<th> Nama Jabatan </th>
								<th> Departemen </th>
								<th> Wajib Absensi </th>
								<th width="100"> Aksi </th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th> Nama Jabatan </th>
								<th> Departemen </th>
								<th> Wajib Absensi </th>
								<th width="100"> Aksi </th>
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
						<label> Departemen </label>
						<select name="id_department" style="width: 100%;" required>
							<option value="all"> - Semua - </option>
							@foreach(\App\Models\Department::all() as $department)
							<option value="{{ $department->id }}">
								{{ $department->department_name }}
							</option>
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

		let $filterModal = $('#filterModal');
		let $filterForm = $('#filterForm');

		$filterModal.find('[name="id_department"]').select2({
			'placeholder' : '- Pilih Departemen -'
		})
		$filterModal.find('[name="id_department"]').val(`{{ $_GET['id_department'] ?? 'all' }}`).trigger('change')

		$('.filterBtn').on('click', function(){
			$filterModal.modal('show')
		})

		$filterModal.find('#filterForm').on('submit', function(e){
			e.preventDefault();

			$filterModal.modal('hide')
			$('#dataTable').DataTable().ajax.reload();
		})

		$('#dataTable').DataTable({
			processing : true,
			serverSide : true,
			autoWidth : false,
			ajax : {
				url : "{{ route('admin.position') }}"
			},
			columns : [
				{
					data : "position_name",
					name : 'position_name'
				},
				{
					data : "department.department_name",
					name : 'departments.department_name'
				},
				{
					data : "is_must_attend",
					name : 'is_must_attend'
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
				const formData = $filterForm.serialize()
				settings.ajax.url = `{{ route('admin.position') }}?${formData}`;
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


	});
</script>
@endsection