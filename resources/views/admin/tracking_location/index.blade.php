@extends('template.backLayout')


@section('action')
@if(UserPermission::check('tracking_location', 'c'))
<button class="btn btn-primary" data-toggle="modal" data-target="#import-modal">
	<i class="mdi mdi-upload"></i> Import
</button>
<a href="{{ route('admin.tracking_location.create') }}" class="btn btn-success">
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
								<th> Foto </th>
								<th> Nama Lokasi </th>
								<th> Alamat </th>
								<th> Deskripsi </th>
								<th> Peta </th>
								<th width="90px"> Aksi </th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th> Foto </th>
								<th> Nama Lokasi </th>
								<th> Alamat </th>
								<th> Deskripsi </th>
								<th> Peta </th>
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

		const $importModal = $('#import-modal');
		const $importForm = $('#import-form');
		const $importSubmitBtn = $importForm.find(`[type="submit"]`).ladda();

		$('#dataTable').DataTable({
			processing : true,
			serverSide : true,
			autoWidth : false,
			ajax : {
				url : "{{ route('admin.tracking_location') }}"
			},
			columns : [
				{
					data : "photo_image",
					name : 'photo_image'
				},
				{
					data : "location_name",
					name : 'location_name'
				},
				{
					data : "address",
					name : 'address'
				},
				{
					data : "description",
					name : 'description'
				},
				{
					data : "map",
					name : 'map',
					rderable : false,
					searchable : false,
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

		$importForm.on('submit', function(e){
			e.preventDefault();

			let formData = new FormData(this);
			$importSubmitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url: `{{ route('admin.tracking_location.import') }}`,
				method: 'post',
				data: formData,
				dataType: 'json',
				contentType: false,
				processData: false,
			})
			.done(response => {
				ajaxSuccessHandling(response);
				$importForm[0].reset();
				$('#dataTable').DataTable().ajax.reload();
				$importModal.modal('hide')
				$importSubmitBtn.ladda('stop')
			})
			.fail(error => {
				ajaxErrorHandling(error, $importForm)
				$importSubmitBtn.ladda('stop')
			})
		})


	});
</script>
@endsection


@section('modal')
<div class="modal fade" id="import-modal" tabindex="-1"role="dialog" aria-hidden="true">
	<div class="modal-dialog mt-4" role="document">
		<div class="modal-content">
			<form id="import-form">

				<div class="modal-header">
					<h5 class="modal-title">
						<i class="mdi mdi-upload"></i> Import
					</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>

				<div class="modal-body">

					<p>
						Catatan : <br>
						<ul>
							<li> Import wajib menggunakan template yg kami sediakan </li>
							<li> Download template dengan <a href="{{ route('helper.import_templates', 'Template_Lokasi_Tracking.xlsx') }}" download> Klik Disini </a> </li>
							<li> Kolom dengan background merah wajib diisi </li>
							<li> Petunjuk pengisian template dapat anda lihat dengan <a href="{{ route('helper.import_templates', 'Petunjuk_Pengisian_Template_Lokasi_Tracking.xlsx') }}" download> Klik Disini </a> </li>
						</ul>
						{!! Template::requiredBanner() !!}

						<div class="form-group">
							<label> File {!! Template::required() !!} </label>
							<input type="file" name="file" class="form-control" accept=".xlsx" required>
						</div>
					</p>
					
				</div>

				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">
						<i class="mdi mdi-upload"></i> Import
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