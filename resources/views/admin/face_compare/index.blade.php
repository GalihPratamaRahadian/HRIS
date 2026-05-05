@extends('template.backLayout')


@section('content')
<div class="row">
	<div class="col-md-5">
		<div class="card support-pane-card">
			<div class="card-body">
				{!! Template::titleBanner($title) !!}

				<form id="mainForm">
					{!! Template::requiredBanner() !!}
					
					<div class="form-group">
						<label> Foto Karyawan {!! Template::required() !!} </label>
						<img src="" id="preview-image" class="img-fluid mb-2" style="display: none;">
						<input type="file" name="photo" class="form-control" accept=".jpg, .jpeg, .png" required>
						<span class="invalid-feedback"></span>
					</div>

					<hr>

					<div class="form-group">
						<button class="btn btn-success" type="submit">
							<i class="mdi mdi-magnify"></i> Cek
						</button>
					</div>
				</form>

			</div>
		</div>
	</div>

	<div class="col-md-7">
		<div class="card support-pane-card">
			<div class="card-body">
				{!! Template::titleBanner('Hasil') !!}

				{!! Template::infoBanner('Yang ditampilkan hanya yg memiliki kemiripan diatas 50%') !!}

				<table class="table table-striped table-hover table-bordered" id="result-table">
					<thead>
						<tr>
							<th> Foto </th>
							<th> Nama Karyawan </th>
							<th> Kemiripan </th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>

			</div>
		</div>
	</div>
</div>
@endsection


@section('script')
<script type="text/javascript">
	$(function(){

		const renderResult = results => {
			let html = ''

			if(results.length == 0) {
				html = $('#empty-item-template').text();
			} else {
				results.forEach(employee => {
					let res = $('#result-item-template').text()
								.replaceAll('{photo_link}', employee.photo_link)
								.replaceAll('{employee_name}', employee.employee_name)
								.replaceAll('{similarity}', employee.similarity)
					html += res;
				})
			}

			$('#result-table').find('tbody').html(html);
		}

		let $form = $('#mainForm')
		let $submitBtn = $form.find('[type="submit"]').ladda();

		$form.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = new FormData(this);
			$submitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url : `{{ route('face_compare.compare') }}`,
				method : 'post',
				data : formData,
				dataType : 'json',
				contentType: false,
				processData: false,
			})
			.done(response => {
				const { results } = response
				$submitBtn.ladda('stop')
				renderResult(results);
				// ajaxSuccessHandling(response);
			})
			.fail(error => {
				$submitBtn.ladda('stop')
				ajaxErrorHandling(error, $form)
			})
		});

		$form.find('[name="photo"]').on('change', function(){
			let file = $(this).val();
			
			if(!isEmpty(file)) {
				let fileType = this.files[0].type;

				if(fileType.substring(0, 5) != "image") {
					toastrAlert();
					toastr.warning('File harus berupa foto', 'Peringatan')
					$(this).val('');
				} else {
					let reader = new FileReader();

					reader.onload = function(e) {
						$('#preview-image').attr('src', e.target.result);
					}

					reader.readAsDataURL(this.files[0]);

					$('#preview-image').show();
				}
			} else {
				$('#preview-image').hide();
			}
		});

		renderResult([]);
	});
</script>

<script type="text/html" id="result-item-template">
	<tr>
		<td>
			<a href="{photo_link}" target="_blank">
				<img src="{photo_link}">
			</a>
		</td>
		<td> {employee_name} </td>
		<td> {similarity}% </td>
	</tr>
</script>

<script type="text/html" id="empty-item-template">
	<tr class="empty">
		<td colspan="3" align="center"> Tidak Ada Hasil </td>
	</tr>
</script>
@endsection