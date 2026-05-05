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
								<th> Nama Pelatihan </th>
								<th> Tanggal Awal </th>
								<th> Tanggal Akhir </th>
								<th> Keterangan </th>
							</tr>
						</thead>
						<tbody>
							@foreach(employee()->employeeTrainings as $training)
							<tr>
								<td> {{ $training->training_name }} </td>
								<td> {{ $training->date_start }} </td>
								<td> {{ $training->date_end }} </td>
								<td> {{ $training->description ?? '-' }} </td>
							</tr>
							@endforeach
						</tbody>
						<tfoot>
							<tr>
								<th> Nama Pelatihan </th>
								<th> Tanggal Awal </th>
								<th> Tanggal Akhir </th>
								<th> Keterangan </th>
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