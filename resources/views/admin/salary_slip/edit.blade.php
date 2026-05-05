@extends('template.backLayout')


@section('content')
<div class="row">
	<div class="col-lg-6">
		<div class="card support-pane-card">
			<div class="card-body">
				{!! Template::titleBanner($title) !!}

				<form id="mainForm">
					{!! Template::requiredBanner() !!}

					@method('PUT')

					<div class="form-group">
						<label> Judul {!! Template::required() !!} </label>
						<input type="text" name="title" class="form-control" placeholder="Contoh : Penggajian Januari 2023 / Apresiasi Januari 2023" value="{{ $salarySlip->title }}">
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Tahun {!! Template::required() !!} </label>
						<select name="year" style="width: 100%">
							@for($i = date('Y'); $i >= 2022; $i--)
							<option value="{{ $i }}"> {{ $i }} </option>
							@endfor
						</select>
						<span class="invalid-feedback"></span>
					</div>

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

					<div class="form-group">
						<label> Karyawan {!! Template::required() !!} </label>
						<select name="id_employee" style="width: 100%">
							@foreach(App\Models\Employee::getActiveEmployees() as $employee)
							<option value="{{ $employee->id }}"> {{ $employee->employee_name }} - {{ $employee->departmentName() }} </option>
							@endforeach
						</select>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Total Nominal {!! Template::required() !!} </label>
						<input type="number" name="total" class="form-control" placeholder="Total Nominal" value="{{ $salarySlip->total }}" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> File Slip (Isi Jika Ingin Ganti File) </label>
						<input type="file" name="file" class="form-control" accept=".pdf">
						<div class="small mt-2">
							Download File Lama <a href="{{ $salarySlip->fileLink() }}" download=""> Klik Disini </a>
						</div>
						<span class="invalid-feedback"></span>
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
			// $form[0].reset();
		}

		const init = () => {
			resetForm();
			$form.find('[name="title"]').focus();
		}

		$form.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = new FormData(this);
			$submitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url : `{{ route('salary_slip.update', $salarySlip->id) }}`,
				method : 'post',
				data : formData,
				dataType : 'json',
				contentType: false,
				processData: false
			})
			.done(response => {
				ajaxSuccessHandling(response);
				$submitBtn.ladda('stop')
			})
			.fail(error => {
				$submitBtn.ladda('stop')
				ajaxErrorHandling(error, $form)
			})
		});

		$form.find(`[name="year"]`).select2({
			'placeholder': '- Pilih Tahun',
		})
		$form.find(`[name="year"]`).val(`{{ $salarySlip->year }}`).trigger('change')

		$form.find(`[name="month"]`).select2({
			'placeholder': '- Pilih Bulan',
		})
		$form.find(`[name="month"]`).val(`{{ $salarySlip->month }}`).trigger('change')

		$form.find(`[name="id_employee"]`).select2({
			'placeholder': '- Pilih Karyawan',
		})
		$form.find(`[name="id_employee"]`).val(`{{ $salarySlip->id_employee }}`).trigger('change')

	});
</script>
@endsection