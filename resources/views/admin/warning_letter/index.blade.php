@extends('template.backLayout')


@section('action')
@if(user()->isAdmin())
@if(UserPermission::check('warning_letter', 'c'))
<a href="{{ route('warning_letter.create') }}" class="btn btn-success">
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
								@if(user()->isAdmin())
								<th> Karyawan </th>
								@endif
								<th> Jenis </th>
								<th> Pesan </th>
								<th> Jangka Waktu Awal </th>
								<th> Jangka Waktu Akhir </th>
								@if(user()->isAdmin())
								<th width="100"> Aksi </th>
								@endif
							</tr>
						</thead>
						<tfoot>
							<tr>
								@if(user()->isAdmin())
								<th> Karyawan </th>
								@endif
								<th> Jenis </th>
								<th> Pesan </th>
								<th> Jangka Waktu Awal </th>
								<th> Jangka Waktu Akhir </th>
								@if(user()->isAdmin())
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
				url : "{{ route('warning_letter') }}"
			},
			columns : [
				@if(user()->isAdmin())
				{
					data : "employee.employee_name",
					name : 'employees.employee_name'
				},
				@endif
				{
					data : "type",
					name : 'type'
				},
				{
					data : "message",
					name : 'message'
				},
				{
					data : "start_date",
					name : 'start_date'
				},
				{
					data : "end_date",
					name : 'end_date'
				},
				@if(user()->isAdmin())
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