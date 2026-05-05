@extends('template.backLayout')


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
								<th> Tingkat Pendidikan </th>
								<th> Nama Sekolah </th>
								<th> Jurusan </th>
								<th> Tahun Awal </th>
								<th> Tahun Akhir </th>
							</tr>
						</thead>
						<tbody>
							@foreach(employee()->employeeEducations as $education)
							<tr>
								<td> {{ $education->education_level }} </td>
								<td> {{ $education->school_name }} </td>
								<td> {{ $education->major_name }} </td>
								<td> {{ $education->year_start }} </td>
								<td> {{ $education->year_end }} </td>
							</tr>
							@endforeach
						</tbody>
						<tfoot>
							<tr>
								<th> Tingkat Pendidikan </th>
								<th> Nama Sekolah </th>
								<th> Jurusan </th>
								<th> Tahun Awal </th>
								<th> Tahun Akhir </th>
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

		$('#dataTable').DataTable({
			processing : true,
			autoWidth : false
		})

	});
</script>
@endsection