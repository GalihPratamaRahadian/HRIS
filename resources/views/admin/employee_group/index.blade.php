@extends('template.backLayout')


@section('action')
@if(UserPermission::check('employee_group', 'c'))
<button class="btn btn-success" data-toggle="modal" data-target="#create-modal">
	<i class="mdi mdi-plus-thick"></i> Buat
</button>
@endif
@endsection


@section('content')
<div class="row">
	<div class="col-md-12 grid-margin stretch-card">
		<div class="card">
			<div class="card-body">
				<h6 class="card-title"> {{ $title }} </h6>
				
				<div class="table-responsive">
					
					<table class="table table-bordered table-hover" id="dataTable">
						<thead>
							<tr>
								<th> Grup Karyawan </th>
								<th width="70"> Aksi </th>
							</tr>
						</thead>
					</table>

				</div>
			</div>
		</div>
	</div>
</div>
@endsection


@section('modal')
<div class="modal fade" id="create-modal" tabindex="-1"role="dialog" aria-hidden="true">
	<div class="modal-dialog mt-4" role="document">
		<div class="modal-content">
			<form id="create-form">

				<div class="modal-header">
					<h5 class="modal-title">
						<i class="mdi mdi-plus-thick"></i> Tambah
					</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>

				<div class="modal-body">

					{!! Template::requiredBanner() !!}

					<div class="form-group">
						<label> Nama Grup Karyawan {!! Template::required() !!} </label>
						<input type="text" name="group_name" class="form-control" placeholder="Nama Grup Karyawan" required>
						<span class="invalid-feedback"></span>
					</div>
					
				</div>

				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">
						<i class="mdi mdi-check"></i> Simpan
					</button>
					<button type="button" class="btn btn-light" data-dismiss="modal">
						<i class="mdi mdi-close"></i> Tutup
					</button>
				</div>

			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="edit-modal" tabindex="-1"role="dialog" aria-hidden="true">
	<div class="modal-dialog mt-4" role="document">
		<div class="modal-content">
			<form id="edit-form">

				<div class="modal-header">
					<h5 class="modal-title">
						<i class="mdi mdi-pencil"></i> Edit
					</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>

				<div class="modal-body">

					{!! Template::requiredBanner() !!}

					<div class="form-group">
						<label> Nama Grup Karyawan {!! Template::required() !!} </label>
						<input type="text" name="group_name" class="form-control" placeholder="Nama Grup Karyawan" required>
						<span class="invalid-feedback"></span>
					</div>
					
				</div>

				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">
						<i class="mdi mdi-check"></i> Simpan
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

		$createModal = $('#create-modal');
		$createForm = $('#create-form');
		$createSubmitBtn = $createForm.find(`[type="submit"]`).ladda();

		$editModal = $('#edit-modal');
		$editForm = $('#edit-form');
		$editSubmitBtn = $editForm.find(`[type="submit"]`).ladda();

		$('#dataTable').DataTable({
			processing : true,
			serverSide : true,
			autoWidth : false,
			ajax : {
				url : "{{ route('admin.employee_group') }}"
			},
			columns : [
				{
					data : 'group_name',
					name : 'group_name'
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
		});


		const dtReload = (pageReset = true) => {
			if(pageReset) {
				$('#dataTable').DataTable().ajax.reload();
			} else {
				$('#dataTable').DataTable().ajax.reload(null, false);
			}
		}


		const renderedEvent = () => {
			$('.delete').off('click')
			$('.delete').on('click', function(){
				let { href } = $(this).data();
				confirmation('Yakin ingin dihapus?', () => {
					ajaxSetup();
					$.ajax({
						url: href,
						method: 'delete',
						dataType: 'json'
					})
					.done(response => {
						ajaxSuccessHandling(response);
						dtReload();
					})
					.fail(error => {
						ajaxErrorHandling(error);
					})
				})
			})

			$('.edit').off('click')
			$('.edit').on('click', function(){
				let { getHref, editHref } = $(this).data();

				$.get({
					url: getHref,
					dataType: 'json'
				})
				.done(response => {
					const { employeeGroup } = response
					Object.keys(employeeGroup).forEach(key => {
						try {
							$editForm.find(`[name="${key}"]`).val(employeeGroup[key]).trigger('change');
						} catch (e) {}
					})

					$editForm.attr('action', editHref)
					$editModal.modal('show')
				})
			})
		}


		$(`#create-modal, #edit-modal`).on('shown.bs.modal', function(){
			$(this).find(`[name="group_name"]`).focus()
		})


		$createForm.on('submit', function(e){
			e.preventDefault()
			clearInvalid();

			let formData = $(this).serialize();
			$createSubmitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url: `{{ route('admin.employee_group.store') }}`,
				method: 'post',
				data: formData,
				dataType: 'json'
			})
			.done(response => {
				ajaxSuccessHandling(response);
				dtReload()
				$createForm[0].reset()
				$createModal.modal('hide')
				$createSubmitBtn.ladda('stop')
			})
			.fail(error => {
				ajaxErrorHandling(error, $createForm);
				$createSubmitBtn.ladda('stop')
			})
		})


		$editForm.on('submit', function(e){
			e.preventDefault()
			clearInvalid();

			let formData = $(this).serialize();
			let editHref = $(this).attr('action')
			$editSubmitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url: editHref,
				method: 'put',
				data: formData,
				dataType: 'json'
			})
			.done(response => {
				ajaxSuccessHandling(response);
				dtReload(false)
				$editForm[0].reset()
				$editModal.modal('hide')
				$editSubmitBtn.ladda('stop')
			})
			.fail(error => {
				ajaxErrorHandling(error, $editForm);
				$editSubmitBtn.ladda('stop')
			})
		})

	})

</script>
@endsection