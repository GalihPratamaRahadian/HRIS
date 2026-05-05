@extends('template.backLayout')

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
								<th> Tgl Shift </th>
								<th> Nama Karyawan </th>
								<th> Departemen </th>
								<th> Jenis </th>
								<th> Jam Masuk </th>
								<th> Jam Keluar </th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th> Tgl Shift </th>
								<th> Nama Karyawan </th>
								<th> Departemen </th>
								<th> Jenis </th>
								<th> Jam Masuk </th>
								<th> Jam Keluar </th>
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
				url : "{{ route('developer.daily_shift_resume') }}"
			},
			columns : [
				{
					data : "date",
					name : 'date'
				},
				{
					data : "employees.employee_name",
					name : 'employees.employee_name',
					visible: false
				},
				{
					data : "departments.department_name",
					name : 'departments.department_name'
				},
				{
					data : "date",
					name : 'date'
				},
				{
					data : "clock_start_at",
					name : 'clock_start_at'
				},
				{
					data : "clock_end_at",
					name : 'clock_end_at',
				},
			],
			order: [[ '0', 'desc' ]],
		})


		const reloadDT = () => {
			$('#dataTable').DataTable().ajax.reload();
		}


	});
</script>
@endsection