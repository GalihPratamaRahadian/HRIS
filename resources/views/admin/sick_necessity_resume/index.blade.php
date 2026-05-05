{{-- @extends('template.backLayout')


@section('content')
<div class="row">
    <div class="col-lg-6">
    <div class="card support-pane-card">
        <div class="card-body">
            {!! Template::titleBanner($title) !!}
            <form id="mainForm">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label> Tanggal Awal {!! \Setting::required() !!} </label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group">
                            <label> Tanggal Akhir {!! \Setting::required() !!} </label>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>
                    </div>
                </div>
                <hr>

					<div class="row">
						<div class="col-lg-6">
							<div class="form-group">
								<label> Departemen </label>
								<select class="form-control" name="id_department" required>
									<option value="all"> - Semua Departemen - </option>

									@foreach(\App\Models\Department::all() as $department)
									<option value="{{ $department->id }}"> {{ $department->department_name }} </option>
									@endforeach

								</select>
							</div>
						</div>

						<div class="col-lg-6">
							<div class="form-group">
								<label> Jabatan </label>
								<select class="form-control" name="id_position" required>
									<option value="all"> - Semua Jabatan - </option>
                                    @foreach (\App\Models\Position::all() as $position)
                                        <option value="{{$position->id}}">{{$position->position_name}}</option>
                                    @endforeach
								</select>
							</div>
						</div>

						<div class="col-lg-6">
							<div class="form-group">
								<label> Grup Karyawan </label>
								<select class="form-control" name="id_employee_group" required>
									<option value="all"> - Semua Grup Karyawan - </option>

									@foreach(\App\Models\EmployeeGroup::all() as $employeeGroup)
									<option value="{{ $employeeGroup->id }}"> {{ $employeeGroup->group_name }} </option>
									@endforeach

								</select>
							</div>
						</div>

						<div class="col-lg-6">
							<div class="form-group">
								<label> Karyawan </label>
								<select class="form-control" name="id_employee" required>
									<option value="all"> - Semua Karyawan - </option>
                                    @foreach (\App\Models\Employee::all() as $employee)
                                        <option value="{{$employee->id}}">{{$employee->employee_name}}</option>
                                    @endforeach
								</select>
							</div>
						</div>


					</div>
					<hr>
					<div class="form-group">
						<label> Aksi </label>
						<select class="form-control" name="action">
							<option value="pdf_stream"> Preview Report PDF </option>
							<option value="pdf_download"> Download Report PDF </option>
							<option value="xlsx_download"> Download Report Excel </option>
						</select>
					</div>
					<hr>
					<div class="form-group">
						<button class="btn btn-success" type="submit">
							<i class="mdi mdi-check"></i> Buat Rekapan
						</button>
					</div>
            </form>
        </div>
    </div>
</div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    $(function(){

		let $form = $('#mainForm');
		let $submitBtn = $form.find(`[type="submit"]`).ladda();

		const generatePositionOptions = () => {
			let departmentId = $form.find(`[name="id_department"]`).val()
			let html = '';

			html += `<option value="all"> - Semua Jabatan - </option>`;
			if(departmentId != 'all') {
				$.get({
					url: `{{ route('helper.get_positions') }}?id_department=${departmentId}`,
					dataType: 'json'
				})
				.done(response => {
					const { positions } = response
					positions.forEach(position => {
						html += `<option value="${position.id}"> ${position.position_name} </option>`
					})

					$form.find(`[name="id_position"]`).html(html)
				})
			} else {
				$form.find(`[name="id_position"]`).html(html)
			}
		}

		$form.find(`[name="id_department"]`).select2({
			'placeholder': '- Pilih Departemen -'
		})

		$form.find(`[name="id_position"]`).select2({
			'placeholder': '- Pilih Jabatan -'
		})

		$form.find(`[name="id_employee_group"]`).select2({
			'placeholder': '- Pilih Grup Karyawan -'
		})

		$form.find(`[name="id_employee"]`).select2({
			'placeholder': '- Pilih Karyawan -'
		})

		$form.on('submit', function(e) {
			e.preventDefault();

			const formData = $(this).serialize();
			window.open(`{{ route('admin.sick_necessity_resume.generate') }}?${formData}`, '_blank');
		})


});
</script>
@endsection --}}

@extends('template.backLayout')


@section('content')
<div class="row">

	<div class="col-lg-6">
		<div class="card support-pane-card">
			<div class="card-body">
				{!! Template::titleBanner($title) !!}

				<form id="mainForm">

					<div class="row">
						<div class="col-lg-6">
							<div class="form-group">
								<label> Tanggal Awal {!! \Setting::required() !!} </label>
								<input type="date" name="start_date" class="form-control" required>
							</div>
						</div>

						<div class="col-lg-6">
							<div class="form-group">
								<label> Tanggal Akhir {!! \Setting::required() !!} </label>
								<input type="date" name="end_date" class="form-control" required>
							</div>
						</div>
					</div>

					<hr>
					<div class="row">
						<div class="col-lg-6">
							<div class="form-group">
								<label> Departemen </label>
								<select class="form-control" name="id_department" required>
									<option value="all"> - Semua Departemen - </option>

									@foreach(\App\Models\Department::all() as $department)
									<option value="{{ $department->id }}"> {{ $department->department_name }} </option>
									@endforeach

								</select>
							</div>
						</div>

						<div class="col-lg-6">
							<div class="form-group">
								<label> Jabatan </label>
								<select class="form-control" name="id_position" required>
									<option value="all"> - Semua Jabatan - </option>
                                    @foreach (\App\Models\Position::all() as $position)
                                        <option value="{{$position->id}}">{{$position->position_name}}</option>
                                    @endforeach
								</select>
							</div>
						</div>

						<div class="col-lg-6">
							<div class="form-group">
								<label> Grup Karyawan </label>
								<select class="form-control" name="id_employee_group" required>
									<option value="all"> - Semua Grup Karyawan - </option>

									@foreach(\App\Models\EmployeeGroup::all() as $employeeGroup)
									<option value="{{ $employeeGroup->id }}"> {{ $employeeGroup->group_name }} </option>
									@endforeach

								</select>
							</div>
						</div>

						<div class="col-lg-6">
							<div class="form-group">
								<label> Karyawan </label>
								<select class="form-control" name="id_employee" required>
									<option value="all"> - Semua Karyawan - </option>
                                    @foreach (\App\Models\Employee::all() as $employee)
                                        <option value="{{$employee->id}}">{{$employee->employee_name}}</option>
                                    @endforeach
								</select>
							</div>
						</div>
					</div>

                    <div class="form-group">
						<label> Status Pengajuan </label>
						<select name="status" style="width: 100%;" required>
							<option value="all"> - Semua Status - </option>
							<option value="wait"> Menunggu </option>
							<option value="approved" selected> Disetujui </option>
							<option value="rejected"> Ditolak </option>
						</select>
					</div>

					<hr>

					<div class="row">
						<div class="col-lg-12">
							<div class="form-group">
								<label> Jenis Pengajuan </label>
								<select class="form-control" name="type" required>
									<option value="all"> - Semua Jenis Pengajuan - </option>
									<option value="Sakit"> Sakit </option>
									<option value="Izin"> Izin </option>
								</select>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label> Aksi </label>
						<select class="form-control" name="action">
							<option value="pdf_stream"> Preview Report PDF </option>
							<option value="pdf_download"> Download Report PDF </option>
							<option value="xlsx_download"> Download Report Excel </option>
						</select>
					</div>

					<hr>

					<div class="form-group">
						<button class="btn btn-success" type="submit">
							<i class="mdi mdi-check"></i> Buat Rekapan
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

		let $form = $('#mainForm');
		let $submitBtn = $form.find(`[type="submit"]`).ladda();

		$form.find(`[name="type"]`).select2({
			'placeholder': '- Pilih Jenis Pengajuan -'
		})

        $form.find(`[name="status"]`).select2({
            'placeholder': '- Pilih Status Pengajuan -'
        })

		const generatePositionOptions = () => {
			let departmentId = $form.find(`[name="id_department"]`).val()
			let html = '';

			html += `<option value="all"> - Semua Jabatan - </option>`;
			if(departmentId != 'all') {
				$.get({
					url: `{{ route('helper.get_positions') }}?id_department=${departmentId}`,
					dataType: 'json'
				})
				.done(response => {
					const { positions } = response
					positions.forEach(position => {
						html += `<option value="${position.id}"> ${position.position_name} </option>`
					})

					$form.find(`[name="id_position"]`).html(html)
				})
			} else {
				$form.find(`[name="id_position"]`).html(html)
			}
		}

		const generateEmployeesOptions = () => {
			let departmentId = $form.find(`[name="id_department"]`).val()
			let positionId = $form.find(`[name="id_position"]`).val()
			let employeeGroupId = $form.find(`[name="id_employee_group"]`).val()
			let html = `<option value="all"> - Semua Karyawan - </option>`;

			$.get({
				url: `{{ route('helper.get_employees') }}?id_department=${departmentId}&id_position=${positionId}&id_employee_group=${employeeGroupId}`,
				dataType: 'json'
			})
			.done(response => {
				const { employees } = response
				employees.forEach(employee => {
					html += `<option value="${employee.id}" data-start-working-date="${employee.start_working_date}" data-employee-name="${employee.employee_name}"> ${employee.employee_name} </option>`
				})

				$form.find(`[name="id_employee"]`).html(html)
			})
		}

		$form.find(`[name="id_department"]`).select2({
			'placeholder': '- Pilih Departemen -'
		})

		$form.find(`[name="id_position"]`).select2({
			'placeholder': '- Pilih Jabatan -'
		})

		$form.find(`[name="id_employee_group"]`).select2({
			'placeholder': '- Pilih Grup Karyawan -'
		})

		$form.find(`[name="id_employee"]`).select2({
			'placeholder': '- Pilih Karyawan -'
		})

		$form.find(`[name="id_department"]`).on('change', function(){
			generatePositionOptions()
			generateEmployeesOptions()
		})

		$form.find(`[name="id_position"]`).on('change', function(){
			generateEmployeesOptions()
		})

		$form.find(`[name="id_employee_group"]`).on('change', function(){
			generateEmployeesOptions()
		})

		$form.on('submit', function(e) {
			e.preventDefault();

			let type = $form.find(`[name="type"]`).val()

			const formData = $(this).serialize();
			window.open(`{{ route('admin.sick_necessity_resume.generate') }}?${formData}&type=${type}`, '_blank')
		})

		$form.find(`[name="id_employee"]`).on('change', function(){
			$('.start-working').hide()
			const val = $(this).val()
			if(!val) return false;
			if(val == 'all') return false;
			let { startWorkingDate, employeeName } = $(this).find('option:selected').data();
			if(startWorkingDate) {
				startWorkingDate = moment(startWorkingDate, 'YYYY-MM-DD').format('DD MMM YYYY')
				$('.start-working').show()
				$('.start-working').find('.employee-name').html(employeeName)
				$('.start-working').find('.start-working-date').val(startWorkingDate)
			}
		})

		generateEmployeesOptions()


	});
</script>
@endsection
