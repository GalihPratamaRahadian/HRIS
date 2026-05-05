@extends('template.backLayout')


@section('content')
<div class="row">
	<div class="col-lg-9">
		<div class="card support-pane-card">
			<div class="card-body">
				{!! Template::titleBanner($title) !!}

				<form id="mainForm">
					{!! Template::requiredBanner() !!}

					<div class="row">
						<div class="col-lg-12">
							<div class="form-group">
								<label> Judul {!! Template::required() !!} </label>
								<input type="text" name="title" class="form-control" placeholder="Contoh : Penggajian Januari 2023 / Apresiasi Januari 2023">
								<span class="invalid-feedback"></span>
							</div>
						</div>

						<div class="col-lg-6">
							<div class="form-group">
								<label> Tahun {!! Template::required() !!} </label>
								<select name="year" style="width: 100%">
									@for($i = date('Y'); $i >= 2022; $i--)
									<option value="{{ $i }}"> {{ $i }} </option>
									@endfor
								</select>
								<span class="invalid-feedback"></span>
							</div>
						</div>
							
						<div class="col-lg-6">
							<div class="form-group">
								<label> Bulan {!! Template::required() !!} </label>
								<select name="month" style="width: 100%">
									<option value="1"> Januari </option>
									<option value="2"> Februari </option>
									<option value="3"> Maret </option>
									<option value="4"> April </option>
									<option value="5"> Mei </option>
									<option value="6"> Juni </option>
									<option value="7"> Juli </option>
									<option value="8"> Agustus </option>
									<option value="9"> September </option>
									<option value="10"> Oktober </option>
									<option value="11"> November </option>
									<option value="12"> Desember </option>
								</select>
								<span class="invalid-feedback"></span>
							</div>
						</div>
					</div>

					<hr>
					
					<div class="table-responsive">
						<table class="table table-hover table-bordered" id="item-table">
							<thead>
								<tr>
									<th> Karyawan </th>
									<th style="width: 200px;"> Total Nominal </th>
									<th style="width: 200px;"> File Slip </th>
									<th style="width: 50px;"> Aksi </th>
								</tr>
							</thead>
							<tbody></tbody>
							<tfoot>
								<tr>
									<td colspan="4">
										<button class="btn btn-primary py-1 px-2 add-item" type="button">
											<i class="mdi mdi-plus"></i> Tambah
										</button>
									</td>
								</tr>
							</tfoot>
						</table>
					</div>

					<hr>

					<div class="form-group">
						<button class="btn btn-success" type="submit">
							<i class="mdi mdi-check"></i> Simpan
						</button>
					</div>
				</form>

			</div>
		</div>
	</div>
</div>
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
			$form.find('[name="title"]').focus();
		}

		const getEmployeeExcept = () => {
			const results = [];
			$.each($('#item-table').find('tbody').find('.employee'), (i, elem) => {
				const val = $(elem).val()
				if(val) {
					results.push(val)
				}
			})

			return results;
		}

		const renderEvent = () => {
			$('#item-table').find('tbody').find('.remove').off('click')
			$('#item-table').find('tbody').find('.remove').on('click', function(){
				$(this).parents('tr').remove()
			})

			$.each($('#item-table').find('tbody').find('.employee'), (i, elem) => {
				$(elem).select2({
					ajax: {
						url: `{{ route('employee.options_get') }}`,
						dataType: 'json',
						data: params => {
							let excepts = getEmployeeExcept();
							let value = $(elem).val()
							excepts = excepts.filter(item => item !== value)
							let query = {
								search: params.term ?? '',
								except: excepts
							}

							return query;
						}
					},
					placeholder: '- Pilih Karyawan -'
				});
			})
		}

		const addItem = () => {
			let html = $('#item-template').text();
			$('#item-table').find('tbody').append(html)
			$('#item-table').find('.item').last().find('.employee').select2({
				'placeholder': '- Pilih Karyawan -'
			})
			$('#item-table').find('.item').last().find('.employee').val('').trigger('change')
			renderEvent()
		}

		$form.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = new FormData(this);
			$submitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url : `{{ route('salary_slip.store') }}`,
				method : 'post',
				data : formData,
				dataType : 'json',
				contentType: false,
				processData: false
			})
			.done(response => {
				ajaxSuccessHandling(response);
				setTimeout(() => {
					window.location.href = `{{ route('salary_slip') }}`
				}, 1000)
			})
			.fail(error => {
				$submitBtn.ladda('stop')
				ajaxErrorHandling(error, $form)

				if(error.status == 422) {
					const { responseJSON } = error
					const { errors } = responseJSON
					$.each(errors, (key, value) => {
						if(key.includes('file')) {
							let index = key.split('.')[1];
							let message = value[0]
							$($('.file')[index]).addClass('is-invalid');
							$($('.file')[index]).siblings('.invalid-feedback').html(message)
						}
						if(key.includes('id_employee')) {
							let index = key.split('.')[1];
							let message = value[0]
							$($('.employee')[index]).addClass('is-invalid');
							$($('.employee')[index]).siblings('.invalid-feedback').html(message)
						}
					})
				}
			})
		});

		$form.find(`[name="year"]`).select2({
			'placeholder': '- Pilih Tahun',
		})
		$form.find(`[name="year"]`).val(`{{ date('Y') }}`).trigger('change')

		$form.find(`[name="month"]`).select2({
			'placeholder': '- Pilih Bulan',
		})
		$form.find(`[name="month"]`).val(`{{ date('n') }}`).trigger('change')

		$form.find(`.add-item`).on('click', function(){
			addItem()
		})



		addItem();

	});
</script>

<script type="text/html" id="item-template">
	<tr class="item">
		<td>
			<select class="employee" name="id_employee[]" style="width: 100%;" required></select>
			<span class="invalid-feedback"></span>
		</td>
		<td>
			<input type="number" name="total[]" class="form-control" placeholder="Total Nominal" required>
			<span class="invalid-feedback"></span>
		</td>
		<td>
			<input type="file" name="file[]" class="form-control file" accept=".pdf" required>
			<span class="invalid-feedback"></span>
		</td>
		<td>
			<button class="btn btn-danger remove p-2" type="button">
				<i class="mdi mdi-close mr-0"></i>
			</button>
		</td>
	</tr>
</script>
@endsection