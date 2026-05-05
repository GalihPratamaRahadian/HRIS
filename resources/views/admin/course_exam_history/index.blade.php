@extends('template.backLayout')


@section('action')
@if(user()->isAdmin())
<!-- <a href="{{ route('course_exam.create') }}" class="btn btn-success">
	<i class="mdi mdi-plus-thick"></i> Buat
</a> -->
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
								<th> Tgl </th>
								<th> Karyawan </th>
								<th> Judul Course </th>
								<th> Jawaban Benar </th>
								<th> Jawaban Salah </th>
								<th> Hasil </th>
								<th width="100"> Aksi </th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th> Tgl </th>
								<th> Karyawan </th>
								<th> Judul Course </th>
								<th> Jawaban Benar </th>
								<th> Jawaban Salah </th>
								<th> Hasil </th>
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
				url : "{{ route('course_exam_history') }}"
			},
			columns : [
				{
					data : "started_at",
					name : "started_at"
				},
				{
					data : "employee.employee_name",
					name : "employees.employee_name"
				},
				{
					data : "course.course_title",
					name : "courses.course_title"
				},
				{
					data : 'correct_answer',
					name : 'correct_answer'
				},
				{
					data : 'incorrect_answer',
					name : 'incorrect_answer'
				},
				{
					data : 'result',
					name : 'result'
				},
				{
					data : 'action',
					name : 'action',
					orderable : false,
					searchable : false,
				}
			],
			order: [[ '0', 'asc' ]],
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