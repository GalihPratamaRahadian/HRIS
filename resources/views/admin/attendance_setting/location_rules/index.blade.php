@extends('template.backLayout')


@section('action')
@if(UserPermission::check('attendance_location_rules', 'c'))
<a href="{{ route('attendance_location_rules.create') }}" class="btn btn-success">
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
					<div class="btn-toolbar mb-0 d-none d-sm-block" role="toolbar">

						

					</div>
				</div>

				<div class="table-responsive support-pane">
					<table class='table table-bordered table-hover' id="dataTable">
						<thead>
							<tr>
								<th> Nama Lokasi </th>
								<th> Lokasi </th>
								<th> Jarak Radius </th>
								<th> Satuan Radius </th>
								<th width="90px"> Aksi </th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th> Nama Lokasi </th>
								<th> Lokasi </th>
								<th> Jarak Radius </th>
								<th> Satuan Radius </th>
								<th width="90px"> Aksi </th>
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
				url : "{{ route('attendance_location_rules') }}"
			},
			columns : [
				{
					data : "location_name",
					name : 'location_name'
				},
				{
					data : "map",
					name : 'map',
					rderable : false,
					searchable : false,
				},
				{
					data : "radius_distance",
					name : 'radius_distance'
				},
				{
					data : "radius_unit",
					name : 'radius_unit'
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