@extends('template.backLayout')


@section('content')
<div class="row">
	<div class="col-lg-12">
		<div class="grid-margin">
			<a class="btn btn-primary" href="{{ route('developer.set_manually') }}" target="_blank">
				<i class="mdi mdi-reload"></i> Set Manually
			</a>
		</div>
	</div>

	<div class="col-lg-6">
		<div class="card support-pane-card">
			<div class="card-body">
				{!! Template::titleBanner($title) !!}

				<form id="mainForm">
					{!! Template::requiredBanner() !!}
					
					<div class="form-group">
						<label> Karyawan {!! Template::required() !!} </label>
						<select name="id_employee" style="width: 100%">
							@foreach(\App\Models\Employee::getActiveEmployees() as $employee)
							<option value="{{ $employee->id }}"> {{ $employee->employee_name }} | {{ $employee->departmentName() }} </option>
							@endforeach
						</select>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Tanggal Awal {!! Template::required() !!} </label>
						<input type="date" name="start_date" value="{{ date('Y-m-d') }}" class="form-control" id="start-date" required>
						<span class="invalid-feedback"></span>
						<div class="text-small mt-1">
							<a href="javascript:void(0);" class="set-date" data-target="#start-date" data-value="{{ today()->addDays(-1)->format('Y-m-d') }}"> Kemarin </a> |
							<a href="javascript:void(0);" class="set-date" data-target="#start-date" data-value="{{ today()->format('Y-m-d') }}"> Hari Ini </a>
						</div>
					</div>

					<div class="form-group">
						<label> Tanggal Akhir {!! Template::required() !!} </label>
						<input type="date" name="end_date" value="{{ date('Y-m-d') }}" class="form-control" id="end-date" required>
						<span class="invalid-feedback"></span>
						<div class="text-small mt-1">
							<a href="javascript:void(0);" class="set-date" data-target="#end-date" data-value="{{ today()->addDays(-1)->format('Y-m-d') }}"> Kemarin </a> |
							<a href="javascript:void(0);" class="set-date" data-target="#end-date" data-value="{{ today()->format('Y-m-d') }}"> Hari Ini </a>
						</div>
					</div>

					<hr>

					<div class="form-group">
						<label> Clock In Log </label>
						<select name="clock_in_log" style="width: 100%" required>
							@foreach($logs as $log)
							<option value="{{ $log->id }}"> {{ $log->createdAtTextSortable() }} | {{ $log->name }} </option>
							@endforeach
						</select>
						<span class="invalid-feedback"></span>
						@if(!empty($_GET['id_employee']) && !empty($_GET['start_date']) && !empty($_GET['end_date']))
						<div class="text-small mt-1 text-primary">
							Ditemukan {{ count($logs) }} data
						</div>
						@endif
					</div>

					<div class="form-group">
						<label> Clock Out Log </label>
						<select name="clock_out_log" style="width: 100%">
							@foreach($logs as $log)
							<option value="{{ $log->id }}"> {{ $log->createdAtTextSortable() }} | {{ $log->name }} </option>
							@endforeach
						</select>
						<span class="invalid-feedback"></span>
						@if(!empty($_GET['id_employee']) && !empty($_GET['start_date']) && !empty($_GET['end_date']))
						<div class="text-small mt-1 text-primary">
							Ditemukan {{ count($logs) }} data
						</div>
						@endif
					</div>

					<hr>

					<div class="form-group">
						<button class="btn btn-primary search-btn" type="button">
							<i class="mdi mdi-magnify"></i> Cari Log
						</button>
						<button class="btn btn-info work-time-btn" type="button">
							<i class="mdi mdi-clock"></i> Get Work Time
						</button>
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

		$form.find(`[name="id_employee"]`).select2({
			'placeholder': '- Pilih Karyawan -'
		})
		$form.find(`[name="id_employee"]`).val('').trigger('change')

		$form.find(`[name="clock_in_log"]`).select2({
			'placeholder': '- Pilih Log -',
			'allowClear': true
		})
		$form.find(`[name="clock_in_log"]`).val('').trigger('change')

		$form.find(`[name="clock_out_log"]`).select2({
			'placeholder': '- Pilih Log -',
			'allowClear': true
		})
		$form.find(`[name="clock_out_log"]`).val('').trigger('change')

		@if(!empty($_GET['id_employee']))
		$form.find(`[name="id_employee"]`).val(`{{ $_GET['id_employee'] }}`).trigger('change')
		@endif

		@if(!empty($_GET['start_date']))
		$form.find(`[name="start_date"]`).val(`{{ $_GET['start_date'] }}`)
		@endif

		@if(!empty($_GET['end_date']))
		$form.find(`[name="end_date"]`).val(`{{ $_GET['end_date'] }}`)
		@endif

		$form.find('.search-btn').on('click', function(){
			const employeeId = $form.find(`[name="id_employee"]`).val()
			const startDate = $form.find(`[name="start_date"]`).val()
			const endDate = $form.find(`[name="end_date"]`).val()

			if(employeeId && startDate && endDate) {
				window.location.replace(`{{ route('developer.face_terminal_log') }}?id_employee=${employeeId}&start_date=${startDate}&end_date=${endDate}`)
			}
		})

		$form.find('.work-time-btn').on('click', function(){
			const employeeId = $form.find(`[name="id_employee"]`).val()
			const startDate = $form.find(`[name="start_date"]`).val()
			const endDate = $form.find(`[name="end_date"]`).val()

			if(employeeId && startDate && endDate) {
				window.open(`{{ url('developer/employee') }}/${employeeId}/work-time/${startDate}/${endDate}`, '_blank')
			}
		})

		$form.on('submit', function(e){
			e.preventDefault();

			const employeeId = $form.find(`[name="id_employee"]`).val()
			const clockInLog = $form.find(`[name="clock_in_log"]`).val()
			const clockOutLog = $form.find(`[name="clock_out_log"]`).val() ?? 0

			const url = `{{ url('developer/face-terminal-log') }}/${employeeId}/set-log-to-attendance/${clockInLog}/${clockOutLog}`
			window.open(url, '_blank')

		})

		$('.set-date').on('click', function(){
			const { target, value } = $(this).data()
			$(target).val(value).trigger('change')
		})
	});
</script>
@endsection