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
								<th> Nama Toko </th>
								<th> Alamat </th>
								<th> Ditangani Oleh </th>
								<th> Terakhir Dikunjungi </th>
								<th> Status Mitra </th>
								<th width="80"> Aksi </th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th> Nama Toko </th>
								<th> Alamat </th>
								<th> Ditangani Oleh </th>
								<th> Terakhir Dikunjungi </th>
								<th> Status Mitra </th>
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
				url : "{{ route('store') }}"
			},
			columns : [
				{
					data : 'store_name',
					name : 'store_name'
				},
				{
					data : 'address',
					name : 'address'
				},
				{
					data : 'handle_by.employee_name',
					name : 'handle_by_name'
				},
				{
					data : 'last_visited_at',
					name : 'last_visited_at'
				},
				{
					data : 'partner_status',
					name : 'partner_status'
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
				settings.ajax.url = `{{ route('store') }}?${query}`
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

			$('.set-active').off('click')
			$('.set-active').on('click', function(){
				let href = $(this).data('href')
				confirmation('Yakin ingin aktifkan mitra toko?', () => {
					ajaxSetup();
					$.ajax({
						url : href,
						method : 'post',
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

			$('.set-inactive').off('click')
			$('.set-inactive').on('click', function(){
				let href = $(this).data('href')
				confirmation('Yakin ingin nonaktifkan mitra toko?', () => {
					ajaxSetup();
					$.ajax({
						url : href,
						method : 'post',
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

		
		$filterForm.find(`[name="handle_by"]`).select2({
			'placeholder': '- Pilih Sales Yg Menangani -'
		})
		$filterForm.find(`[name="handle_by"]`).val('all').trigger('change')

		$filterForm.on('submit', function(e){
			e.preventDefault();

			reloadDT();
			$('#filter-modal').modal('hide')
		})

		$filterForm.find(`[name="partner_status"]`).val('active');

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

					<div class="form-group">
						<label> Sales Yang Menangani </label>
						<select name="handle_by" style="width: 100%;">
							<option value="all"> - Semua Sales - </option>

							@foreach(\App\Models\SalesEmployee::all() as $sales)
							<option value="{{ $sales->id_employee }}">
								{{ $sales->employeeName() }}
							</option>
							@endforeach

						</select>
					</div>

					<div class="form-group">
						<label> Status Mitra </label>
						<select name="partner_status" class="form-control">
							<option value="all"> - Semua - </option>
							<option value="active"> Aktif </option>
							<option value="inactive"> Nonaktif </option>
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