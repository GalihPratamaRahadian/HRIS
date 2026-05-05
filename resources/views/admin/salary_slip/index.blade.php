@extends('template.backLayout')


@section('action')
@if(UserPermission::check('salary_slip', 'c'))
<a href="{{ route('salary_slip.create') }}" class="btn btn-success">
	<i class="mdi mdi-plus-thick"></i> Tambah
</a>
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
								<th> Tahun </th>
								<th> Bulan </th>
								<th> Karyawan </th>
								<th> Total Nominal </th>
								<th> Slip </th>
								@if(auth()->user()->isAdmin())
								<th width="100"> Aksi </th>
								@endif
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th> Tahun </th>
								<th> Bulan </th>
								<th> Karyawan </th>
								<th> Total Nominal </th>
								<th> Slip </th>
								@if(auth()->user()->isAdmin())
								<th width="100"> Aksi </th>
								@endif
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
				url : "{{ route('salary_slip') }}"
			},
			columns : [
				{
					data : "year",
					name : "year"
				},
				{
					data : "month_name",
					name : "month"
				},
				{
					data : "employees.employee_name",
					name : 'employees.employee_name'
				},
				{
					data : "total",
					name : 'total'
				},
				{
					data : "filename",
					name : 'filename',
					orderable : false,
					searchable : false,
				},
				@if(auth()->user()->isAdmin())
				{
					data : 'action',
					name : 'action',
					orderable : false,
					searchable : false,
				}
				@endif
			],
			drawCallback : settings => {
				renderedEvent();
			},
			order: [['0', 'desc'], ['1', 'desc']]
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