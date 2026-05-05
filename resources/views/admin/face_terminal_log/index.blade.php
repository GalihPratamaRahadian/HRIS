@extends('template.backLayout')


@section('action')
<button class="btn btn-primary" id="filterBtn">
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
								<th> Waktu </th>
								<th> Nama </th>
								<th> Suhu </th>
								<th> Masker </th>
								<th> Foto </th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th> Waktu </th>
								<th> Nama </th>
								<th> Suhu </th>
								<th> Masker </th>
								<th> Foto </th>
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

					<div class="row">
						<div class="col-lg-6">
							<div class="form-group">
								<label> Tanggal Awal </label>
								<input type="date" name="start_date" class="form-control">
								<small class="invalid-feedback"></small>
							</div>
						</div>

						<div class="col-lg-6">
							<div class="form-group">
								<label> Tanggal Akhir </label>
								<input type="date" name="end_date" class="form-control">
								<small class="invalid-feedback"></small>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label> Tipe Orang </label>
						<select name="people_type" style="width: 100%;">
							
							<option value="all"> - Semua - </option>
							<option value="stranger"> Orang Asing </option>
							<option value="employee"> Karyawan </option>

						</select>
						<small class="invalid-feedback"></small>
					</div>

					<div class="form-group">
						<label> Penggunaan Masker </label>
						<select name="using_mask" style="width: 100%;">
							
							<option value="all"> - Semua - </option>
							<option value="yes"> Ya </option>
							<option value="no"> Tidak </option>

						</select>
						<small class="invalid-feedback"></small>
					</div>

					<div class="form-group">
						<label> Suhu </label>
						<select name="temperature_status" style="width: 100%;">
							
							<option value="all"> - Semua - </option>
							<option value="normal"> Normal </option>
							<option value="not_normal"> Tidak Normal </option>

						</select>
						<small class="invalid-feedback"></small>
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

		let filter = $('#filterForm');

		const reloadDT = () => {
			$('#dataTable').DataTable().ajax.reload();
		}


		const getQuery = () => {
			let startDate = filter.find('[name="start_date"]').val();
			let endDate = filter.find('[name="end_date"]').val();
			let peopleType = filter.find('[name="people_type"]').val();
			let usingMask = filter.find('[name="using_mask"]').val();
			let temperatureStatus = filter.find('[name="temperature_status"]').val();
			let query = '';

			if(!isEmpty(startDate)) query += `start_date=${startDate}&`;
			if(!isEmpty(endDate)) query += `end_date=${endDate}&`;
			query += `people_type=${peopleType}&using_mask=${usingMask}&temperature_status=${temperatureStatus}`;

			return query;
		}


		const setUrl = () => {
			window.history.pushState('attendance', $('title').text(), `{{ route('face_terminal_log') }}?${getQuery()}`);
		}


		const setDateToValid = () => {
			let start = filter.find(`[name="start_date"]`).val()
			let end = filter.find(`[name="end_date"]`).val()

			if(!isEmpty(start) || !isEmpty(end)) {
				if(isEmpty(start)) {
					filter.find(`[name="start_date"]`).val(end)
				} else if (isEmpty(end)) {
					filter.find(`[name="end_date"]`).val(start)
				}

				if(end < start) {
					filter.find(`[name="end_date"]`).val(start)
				}
			}
		}


		$('#filterForm').on('submit', function(e){
			e.preventDefault();

			setDateToValid();
			reloadDT();
			setUrl()

			$('#filterModal').modal('hide');
		})

		@if(isset($_GET['start_date']))
		$('[name="start_date"]').val(`{{ $_GET['start_date'] }}`).trigger('change')
		@endif

		@if(isset($_GET['end_date']))
		$('[name="end_date"]').val(`{{ $_GET['end_date'] }}`).trigger('change')
		@endif

		@if(isset($_GET['people_type']))
		$('[name="people_type"]').val(`{{ $_GET['people_type'] }}`).trigger('change')
		@endif

		@if(isset($_GET['using_mask']))
		$('[name="using_mask"]').val(`{{ $_GET['using_mask'] }}`).trigger('change')
		@endif

		@if(isset($_GET['temperature_status']))
		$('[name="temperature_status"]').val(`{{ $_GET['temperature_status'] }}`).trigger('change')
		@endif

		setDateToValid();
		setUrl();


		$('#dataTable').DataTable({
			processing : true,
			serverSide : true,
			autoWidth : false,
			ajax : {
				url : "{{ route('face_terminal_log') }}"
			},
			columns : [
				{
					data : "created_at",
					name : 'created_at'
				},
				{
					data : "name",
					name : 'name'
				},
				{
					data : "temperature",
					name : 'temperature'
				},
				{
					data : "mask",
					name : 'mask'
				},
				{
					data : "photo",
					name : 'photo',
					searchable : false,
					sortable : false
				}
			],
			order: [[ '0', 'desc' ]],
			preDrawCallback : settings => {
				settings.ajax.url = `{{ route('face_terminal_log') }}?${getQuery()}`;
			}
		})


		$('#filterBtn').on('click', function(){
			$('#filterModal').modal('show');
		})


		$('[name="people_type"]').select2()
		$('[name="people_type"]').val('all').trigger('change')

		$('[name="using_mask"]').select2()
		$('[name="using_mask"]').val('all').trigger('change')

		$('[name="temperature_status"]').select2()
		$('[name="temperature_status"]').val('all').trigger('change')


	});
</script>
@endsection