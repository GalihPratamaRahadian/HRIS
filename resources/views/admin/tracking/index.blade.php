@extends('template.backLayout')


@section('action')
<button class="btn btn-primary" data-toggle="modal" data-target="#filter-modal">
	<i class="mdi mdi-filter"></i> Filter
</button>
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
								<th> Tgl Check In </th>
								<th> Karyawan </th>
								<th> Lokasi </th>
								<th> Foto Check In </th>
								<th> File Penerimaan Barang </th>
								<th width="80"> Aksi </th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th> Tgl Check In </th>
								<th> Karyawan </th>
								<th> Lokasi </th>
								<th> Foto Check In </th>
								<th> File Penerimaan Barang </th>
								<th width="80"> Aksi </th>
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

		$filterForm = $('#filter-form');


		$('#dataTable').DataTable({
			processing : true,
			serverSide : true,
			autoWidth : false,
			ajax : {
				url : "{{ route('admin.tracking') }}"
			},
			columns : [
				{
					data : 'check_in_at',
					name : 'check_in_at'
				},
				{
					data : 'employees.employee_name',
					name : 'employees.employee_name'
				},
				{
					data : 'tracking_locations.location_name',
					name : 'tracking_locations.location_name'
				},
				{
					data : 'photo',
					name : 'photo',
					orderable : false,
					searchable : false,
				},
				{
					data : 'file_good_receipt',
					name : 'file_good_receipt',
					orderable : false,
					searchable : false,
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
			},
			preDrawCallback: settings => {
				const query = $filterForm.serialize();
				settings.ajax.url = `{{ route('admin.tracking') }}?${query}`
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

		
		$filterForm.find(`[name="id_employee"]`).select2({
			'placeholder': '- Pilih Karyawan -'
		})
		$filterForm.find(`[name="id_employee"]`).val('all').trigger('change')

		$filterForm.find(`[name="id_tracking_location"]`).select2({
			'placeholder': '- Pilih Lokasi -'
		})
		$filterForm.find(`[name="id_tracking_location"]`).val('all').trigger('change')

		$filterForm.on('submit', function(e){
			e.preventDefault();

			reloadDT();
			$('#filter-modal').modal('hide')
		})

	});
</script>
@endsection



@section('modal')
<div class="modal fade" id="filter-modal" role="dialog" aria-hidden="true">
	<div class="modal-dialog mt-4" role="document">
		<div class="modal-content">
			<form id="filter-form">

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
							</div>
						</div>
							
						<div class="col-lg-6">
							<div class="form-group">
								<label> Tanggal Akhir </label>
								<input type="date" name="end_date" class="form-control">
							</div>
						</div>
					</div>

					<div class="form-group">
						<label> Karyawan </label>
						<select name="id_employee" style="width: 100%;">
							<option value="all"> - Semua Sales - </option>

							@foreach(\App\Models\TrackingEmployee::all() as $trackingEmployee)
							<option value="{{ $trackingEmployee->id_employee }}">
								{{ $trackingEmployee->employeeName() }}
							</option>
							@endforeach

						</select>
					</div>

					<div class="form-group">
						<label> Lokasi </label>
						<select name="id_tracking_location" style="width: 100%;">
							<option value="all"> - Semua Lokasi - </option>

							@foreach(\App\Models\TrackingLocation::all() as $trackingLocation)
							<option value="{{ $trackingLocation->id }}">
								{{ $trackingLocation->location_name }}
							</option>
							@endforeach

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
@endsection