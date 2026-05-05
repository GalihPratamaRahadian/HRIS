@extends('template.backLayout')


@section('content')
<form id="mainForm">
	<div class="row">
		<div class="col-md-6">
			<div class="card support-pane-card">
				<div class="card-body">
					{!! Template::titleBanner($title) !!}

					{!! Template::requiredBanner() !!}
					
					<div class="form-group">
						<label> Nama {!! Template::required() !!} </label>
						<input type="text" name="name" class="form-control" placeholder="Nama" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Username {!! Template::required() !!} </label>
						<input type="text" name="username" class="form-control" placeholder="Username" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Password {!! Template::required() !!} </label>
						<input type="password" name="password" class="form-control" placeholder="Password" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Konfirmasi Password {!! Template::required() !!} </label>
						<input type="password" name="confirm_password" class="form-control" placeholder="Konfirmasi Password" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Role {!! Template::required() !!} </label>
						<select class="form-control" name="role" required>
							<option value=""> - Pilih Role - </option>
							<option value="admin"> Admin </option>
						</select>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Pembatasan Akses </label>
						<select class="form-control" name="is_restricted_access">
							<option value="no"> Tidak Dibatasi </option>
							<option value="yes"> Batasi Akses </option>
						</select>
						<span class="invalid-feedback"></span>
					</div>

					<hr>

					<div class="form-group">
						<button class="btn btn-success" type="submit">
							<i class="mdi mdi-check"></i> Simpan
						</button>
					</div>

				</div>
			</div>
		</div>

		<div class="col-md-6" id="user-permission">
		</div>
	</div>
</form>
@endsection


@section('script')
<script type="text/javascript">
	$(function(){

		let $form = $('#mainForm')
		let $submitBtn = $form.find('[type="submit"]').ladda();

		const resetForm = () => {
			$form[0].reset();
		}

		const init = () => {
			resetForm();
			$form.find('[name="name"]').focus();
		}

		const showUserPermission = () => {
			$('#user-permission').html($('#user-permission-template').text())
		}

		const hideUserPermission = () => {
			$('#user-permission').html('')
		}

		const setUserPermission = (menu, value) => {
			$('#user-permission').find(`[name="permissions[${menu}]"]`).val(value);
		}

		$form.find(`[name="is_restricted_access"]`).on('change', function(){
			const val = $(this).val()

			if(val == 'yes') {
				showUserPermission();
			} else {
				hideUserPermission();
			}
		})


		$form.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = $(this).serialize();

			$submitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url : `{{ route('user.store') }}`,
				method : 'post',
				data : formData,
				dataType : 'json',
			})
			.done(response => {
				init();
				$submitBtn.ladda('stop')
				ajaxSuccessHandling(response);
			})
			.fail(error => {
				$submitBtn.ladda('stop')
				ajaxErrorHandling(error, $form)
			})
		});

		init();
	});
</script>

<script type="text/html" id="user-permission-template">
	<div class="card support-pane-card">
		<div class="card-body">
			{!! Template::titleBanner('Hak Akses') !!}

			<div style="max-height: 600px; overflow-y: scroll;">
				<table class="table table-bordered table-hover">
					<thead>
						<tr>
							<th> Menu </th>
							<th> Akses </th>
						</tr>
					</thead>
					<tbody>
						@forelse(UserPermission::adminAccessList() as $menu)
						<tr>
							<td colspan="2"><b> # {{ $menu->title }} </b></td>
						</tr>
							@foreach($menu->submenus as $submenu)
							<tr>
								<td> {{ $submenu->title }} </td>
								<td>
									<select class="form-control" name="permissions[{{ $submenu->menu }}]" required>
										@foreach($submenu->access as $value => $label)
										<option value="{{ $value }}"> {{ $label }} </option>
										@endforeach
									</select>
								</td>
							</tr>
							@endforeach
						@empty
						<tr>
							<td colspan="2" align="center"> Belum Ada Akses </td>
						</tr>
						@endforelse
					</tbody>
				</table>
			</div>

		</div>
	</div>
</script>
@endsection