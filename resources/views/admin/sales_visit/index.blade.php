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
					{!! Template::titleBanner('<span id="sales-visit-title">'.$title.' - '.date('d/m/Y').'</span>') !!}
				</div>

				<div class="table-responsive support-pane">
					<table class='table table-bordered table-hover' id="dataTable">
						<thead>
							<tr>
								<th> No Induk </th>
								<th> Nama Karyawan </th>
								<th> Total Toko Ditangani </th>
								<th width="80"> Aksi </th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th> No Induk </th>
								<th> Nama Karyawan </th>
								<th> Total Toko Ditangani </th>
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

		$filterForm = $('#filter-form')


		$('#dataTable').DataTable({
			processing : true,
			serverSide : true,
			autoWidth : false,
			ajax : {
				url : "{{ route('sales_visit') }}"
			},
			columns : [
				{
					data : 'employee.employee_number',
					name : 'employees.employee_number'
				},
				{
					data : 'employee.employee_name',
					name : 'employees.employee_name'
				},
				{
					data : 'amount_of_visited_stores',
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
			},
			preDrawCallback: settings => {
				const query = $filterForm.serialize();
				settings.ajax.url = `{{ route('sales_visit') }}?${query}`
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

		$filterForm.on('submit', function(e){
			e.preventDefault();
			reloadDT();
			const date = $(this).find(`[name="date"]`).val()
			const newDate = new Date(date)
			$('#sales-visit-title').text(`Kunjungan Sales - ${newDate.getDate()}/${newDate.getMonth()}/${newDate.getFullYear()}`)
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

					<div class="form-group">
						<div class="form-group">
							<label> Tanggal </label>
							<input type="date" name="date" value="{{ date('Y-m-d') }}" class="form-control">
						</div>
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