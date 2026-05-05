@extends('template.backLayout')


@section('action')
@if(user()->isAdmin())
@if(UserPermission::check('course', 'c'))
<a href="{{ route('admin.course.create') }}" class="btn btn-success">
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
								<th width="120"> Tgl Dibuat </th>
								<th> Judul </th>
								<th> Tenggat Waktu </th>
								<th> Status Publikasi </th>
								<th width="100"> Aksi </th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th> Tgl Dibuat </th>
								<th> Judul </th>
								<th> Tenggat Waktu </th>
								<th> Status Publikasi </th>
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
				url : "{{ route('admin.course') }}"
			},
			columns : [
				{
					data : "created_at",
					name : 'created_at'
				},
				{
					data : "admin_course_title",
					name : 'course_title'
				},
				{
					data : "deadline",
					name : 'deadline'
				},
				{
					data : 'is_published',
					name : 'is_published'
				},
				{
					data : 'admin_action',
					name : 'admin_action',
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