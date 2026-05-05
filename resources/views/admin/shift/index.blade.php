@extends('template.backLayout')


@section('action')
@if(UserPermission::check('shift', 'c'))
<a href="{{ route('admin.shift.create') }}" class="btn btn-success">
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
								<th> Nama Jam Kerja </th>
								<th> Batas Jam Awal Kehadiran </th>
								<th> Jam Mulai </th>
								<th> Jam Selesai </th>
								<th> Libur </th>
								<th width="100px"> Aksi </th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th> Nama Jam Kerja </th>
								<th> Batas Jam Awal Kehadiran </th>
								<th> Jam Mulai </th>
								<th> Jam Selesai </th>
								<th> Libur </th>
								<th width="100px"> Aksi </th>
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
				url : "{{ route('admin.shift') }}"
			},
			columns : [
				{
					data : 'shift_name',
					name : 'shift_name'
				},
				{
					data : 'clock_start_limit',
					name : 'clock_start_limit'
				},
				{
					data : 'clock_start',
					name : 'clock_start'
				},
				{
					data : 'clock_end',
					name : 'clock_end'
				},
				{
					data : 'offday',
					name : 'offday'
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