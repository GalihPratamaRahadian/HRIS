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
								<th> Judul </th>
								<th> Trainer / Provider </th>
								<th> Tgl Awal Pelaksanaan </th>
								<th> Tgl Akhir Pelaksanaan </th>
								<th width="100"> Aksi </th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th> Judul </th>
								<th> Trainer / Provider </th>
								<th> Tgl Awal Pelaksanaan </th>
								<th> Tgl Akhir Pelaksanaan </th>
								<th width="100"> Aksi </th>
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
			serverSide : true,
			autoWidth : false,
			ajax : {
				url : "{{ route('employee.training') }}"
			},
			columns : [
				{
					data : "title",
					name : 'title'
				},
				{
					data : "trainer_name",
					name : 'trainer_name'
				},
				{
					data : "start_date",
					name : 'start_date'
				},
				{
					data : "end_date",
					name : 'end_date'
				},
				{
					data : 'employee_action',
					name : 'employee_action',
					orderable : false,
					searchable : false,
				}
			],
			order: [[ '0', 'asc' ]],
			drawCallback : settings => {
				renderedEvent();
			}
		})


		const reloadDT = () => {
			$('#dataTable').DataTable().ajax.reload();
		}


		const renderedEvent = () => {
			
		}


	});
</script>
@endsection