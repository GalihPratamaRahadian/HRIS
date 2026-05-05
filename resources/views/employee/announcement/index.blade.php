@extends('template.backLayout')


@section('action')
@if(user()->isAdmin())
@if(UserPermission::check('announcement', 'c'))
<a href="{{ route('announcement.create') }}" class="btn btn-success">
	<i class="mdi mdi-plus-thick"></i> Buat
</a>
@endif
@endif
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
								<th> Tgl Pengumuman </th>
								<th> Judul </th>
								@if(auth()->user()->isAdmin())
								<th> Departemen </th>
								<th> Grup Karyawan </th>
								<th> Status Publikasi </th>
								@endif
								<th width="100"> Aksi </th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th> Tgl Dibuat </th>
								<th> Judul </th>
								@if(auth()->user()->isAdmin())
								<th> Departemen </th>
								<th> Grup Karyawan </th>
								<th> Status Publikasi </th>
								@endif
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


@section('script')
<script type="text/javascript">
	$(function(){

		$('#dataTable').DataTable({
			processing : true,
			serverSide : true,
			autoWidth : false,
			ajax : {
				url : "{{ route('announcement') }}"
			},
			columns : [
				{
					data : "created_at",
					name : 'created_at'
				},
				{
					data : "title",
					name : 'title'
				},
				@if(auth()->user()->isAdmin())
				{
					data : "department.department_name",
					name : 'departments.department_name'
				},
				{
					data : "employee_group.group_name",
					name : 'employee_groups.group_name'
				},
				{
					data : 'is_published',
					name : 'is_published'
				},
				@endif
				{
					data : 'action',
					name : 'action',
					orderable : false,
					searchable : false,
				}
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
						ajaxSuccessHandling(response);
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