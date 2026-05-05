@extends('template.backLayout')


@section('action')
@if(user()->isAdmin())
@if(UserPermission::check('course_exam', 'c'))
<a href="{{ route('admin.course_exam.create') }}" class="btn btn-success">
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
								<th> Judul Course </th>
								<th> Durasi </th>
								<th> Jumlah Pertanyaan </th>
								<th width="100"> Aksi </th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th> Judul Course </th>
								<th> Durasi </th>
								<th> Jumlah Pertanyaan </th>
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
				url : "{{ route('admin.course_exam') }}"
			},
			columns : [
				{
					data : "course.course_title",
					name : "courses.course_title"
				},
				{
					data : "duration",
					name : "duration"
				},
				{
					data : 'amount_of_questions',
					name : 'amount_of_questions'
				},
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