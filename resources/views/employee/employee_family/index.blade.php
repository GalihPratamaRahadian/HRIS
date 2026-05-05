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
								<th> Nama </th>
								<th> Status Hubungan </th>
								<th> Tempat Lahir </th>
								<th> Tanggal Lahir </th>
							</tr>
						</thead>
						<tbody>
							@foreach(employee()->employeeFamilies as $family)
							<tr>
								<td> {{ $family->name }} </td>
								<td> {{ $family->relationship_status }} </td>
								<td> {{ $family->place_of_birth }} </td>
								<td> {{ $family->date_of_birth }} </td>
							</tr>
							@endforeach
						</tbody>
						<tfoot>
							<tr>
								<th> Nama </th>
								<th> Status Hubungan </th>
								<th> Tempat Lahir </th>
								<th> Tanggal Lahir </th>
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