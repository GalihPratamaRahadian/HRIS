@extends('template.backLayout')


@section('action')
@if(user()->isAdmin())
<button class="btn btn-danger filterBtn">
	<i class="mdi mdi-filter"></i> Filter
</button>

<button class="btn btn-success exportBtn">
	<i class="mdi mdi-download"></i> Export
</button>
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
								<th> Karyawan </th>
								<th> Departemen </th>
								<th> Judul Course </th>
								<th> Nilai </th>
								<th> Status </th>
								<th> Lulus Pada </th>
								<th width="100"> Aksi </th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th> Karyawan </th>
								<th> Departemen </th>
								<th> Judul Course </th>
								<th> Nilai </th>
								<th> Status </th>
								<th> Lulus Pada </th>
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
						<label> Course </label>
						<select name="id_course" style="width: 100%;" required>
							<option value="all"> - Semua - </option>

							@foreach(\App\Models\Course::all() as $course)
							<option value="{{ $course->id }}">
								{{ $course->course_title }}
							</option>
							@endforeach

						</select>
					</div>

					<div class="form-group">
						<label> Status Kelulusan </label>
						<select name="have_passed" style="width: 100%;" required>

							<option value="all"> - Semua - </option>
							<option value="yes"> Lulus </option>
							<option value="no"> Belum Lulus </option>

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

<div class="modal fade" id="exportModal" role="dialog" aria-hidden="true">
	<div class="modal-dialog mt-4" role="document">
		<div class="modal-content">
			<form id="exportForm">

				<div class="modal-header">
					<h5 class="modal-title">
						Export
					</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>

				<div class="modal-body">

					{!! Template::requiredBanner() !!}

					<div class="form-group">
						<label> Course {!! Template::required() !!} </label>
						<select name="id_course" style="width: 100%;" required>
							@foreach(\App\Models\Course::all() as $course)
							<option value="{{ $course->id }}">
								{{ $course->course_title }}
							</option>
							@endforeach

						</select>
					</div>

					<div class="form-group">
						<label> Status Kelulusan </label>
						<select name="have_passed" style="width: 100%;" required>

							<option value="all"> - Semua - </option>
							<option value="yes"> Lulus </option>
							<option value="no"> Belum Lulus </option>
							<option value="not_accessed"> Belum Mengakses </option>

						</select>
					</div>

					<div class="form-group">
						<label> Aksi </label>
						<select name="action" class="form-control" required>
							<option value="pdf_stream"> Preview PDF </option>
							<option value="pdf_download"> Download PDF </option>
							<option value="xlsx_download"> Download Excel </option>
						</select>
					</div>
					
				</div>

				<div class="modal-footer">
					<button type="submit" class="btn btn-success">
						<i class="mdi mdi-download"></i> Export
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


		/**
		 * 	Filter
		 * */
		let $filterModal = $('#filterModal');

		$filterModal.find('[name="id_course"]').select2({
			'placeholder' : '- Pilih Course -'
		})
		$filterModal.find('[name="id_course"]').val(`{{ $_GET['id_course'] ?? 'all' }}`).trigger('change')

		$filterModal.find('[name="have_passed"]').select2({
			'placeholder' : '- Pilih Status Kelulusan -'
		})
		$filterModal.find('[name="have_passed"]').val(`{{ $_GET['have_passed'] ?? 'all' }}`).trigger('change')

		$('.filterBtn').on('click', function(){
			$filterModal.modal('show')
		})

		$filterModal.find('#filterForm').on('submit', function(e){
			e.preventDefault();

			$filterModal.modal('hide')
			$('#dataTable').DataTable().ajax.reload();
		})


		/**
		 * 	Export
		 * */
		let $exportModal = $('#exportModal');

		$exportModal.find('[name="id_course"]').select2({
			'placeholder' : '- Pilih Course -'
		})
		$exportModal.find('[name="id_course"]').val(`{{ $_GET['id_course'] ?? '' }}`).trigger('change')

		$exportModal.find('[name="have_passed"]').select2({
			'placeholder' : '- Pilih Status Kelulusan -'
		})
		$exportModal.find('[name="have_passed"]').val(`{{ $_GET['have_passed'] ?? 'all' }}`).trigger('change')

		$('.exportBtn').on('click', function(){
			$exportModal.modal('show')
		})

		$exportModal.find('#exportForm').on('submit', function(e){
			e.preventDefault();
			let formData = $(this).serialize();
			window.open(`{{ route('admin.course_result.export') }}?${formData}`)
		})



		$('#dataTable').DataTable({
			processing : true,
			serverSide : true,
			autoWidth : false,
			ajax : {
				url : "{{ route('admin.course_result') }}"
			},
			columns : [
				{
					data : "employee.employee_name",
					name : "employees.employee_name"
				},
				{
					data : "employee.department.department_name",
					name : "departments.department_name"
				},
				{
					data : "course.course_title",
					name : "courses.course_title"
				},
				{
					data : 'exam_score',
					name : 'exam_score',
					orderable : false,
					searchable : false,
				},
				{
					data : 'have_passed',
					name : 'have_passed'
				},
				{
					data : 'passed_at',
					name : 'passed_at'
				},
				{
					data : 'action',
					name : 'action',
					orderable : false,
					searchable : false,
				}
			],
			order: [[ '5', 'desc' ]],
			drawCallback : settings => {
				renderedEvent();
			},
			preDrawCallback : settings => {
				let formData = $('#filterForm').serialize();

				settings.ajax.url = `{{ route('admin.course_result') }}?${formData}`;
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

