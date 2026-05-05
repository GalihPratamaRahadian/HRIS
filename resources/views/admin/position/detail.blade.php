@extends('template.backLayout')


@section('content')
<div class="row">
	<div class="col-md-8">
		<div class="card support-pane-card">
			<div class="card-body">
				{!! Template::titleBanner($title) !!}

				<table class="table table-bordered">
					<tbody>
						<tr>
							<th width="250"> Nama Jabatan </th>
							<td> {{ $position->position_name }} </td>
						</tr>
						<tr>
							<th> Departemen </th>
							<td> {{ $position->departmentName() }} </td>
						</tr>
						<tr>
							<th> Wajib Melakukan Absensi </th>
							<td> {{ $position->is_must_attend }} </td>
						</tr>
						<tr>
							<th> Penyetuju Cuti/Lembur 1 </th>
							<td> {{ $position->approver1PositionName() }} </td>
						</tr>
						<tr>
							<th> Penyetuju Cuti/Lembur 2 </th>
							<td> {{ $position->approver2PositionName() }} </td>
						</tr>
						<tr>
							<th> Deskripsi Pekerjaan </th>
							<td> {!! $position->job_description !!} </td>
						</tr>
						<tr>
							<th> Sasaran Kinerja </th>
							<td> {!! $position->performance_goals !!} </td>
						</tr>
						<tr>
							<th> Kompetensi </th>
							<td> {!! $position->competence !!} </td>
						</tr>
					</tbody>
				</table>

			</div>
		</div>
	</div>
</div>
@endsection