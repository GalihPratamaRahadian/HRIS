@extends('template.backLayout')


@section('content')
<div class="row">
	<div class="col-md-12">
		<div class="card support-pane-card">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h4 class="card-title mb-0"> {{ $title }} </h4>
				</div>

				<div class="table-responsive support-pane">
					<table class='table table-bordered table-hover' id="dataTable">
						<thead>
							<tr>
								<th> Waktu </th>
								<th> Judul </th>
								<th> Status </th>
								<th width="100px"> Aksi </th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th> Waktu </th>
								<th> Judul </th>
								<th> Status </th>
								<th width="100px"> Aksi </th>
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
				url : "{{ route('notification') }}"
			},
			columns : [
				{
					data : "created_at",
					name : 'created_at'
				},
				{
					data : "title",
					name : 'title'
				},
				{
					data : "is_read",
					name : 'is_read'
				},
				{
					data : 'action',
					name : 'action',
					orderable : false,
					searchable : false,
				}
			],
			order: [[ '0', 'desc' ]],
			drawCallback : settings => {
				renderedEvent();
			}
		})


		const reloadDT = () => {
			$('#dataTable').DataTable().ajax.reload();
		}


		const renderedEvent = () => {
			$('.delete').off('click')
			$('.delete').on('click', function(){
				let href = $(this).data('href')
				confirmation('Yakin ingin dihapus?', () => {
					ajaxSetup();
					$.ajax({
						url : href,
						method : 'delete',
					})
					.done(response => {
						ajaxSuccessHandling(response);
						reloadDT();
					})
					.fail(error => {
						ajaxErrorHandling(error)
					})
				})
			});
		}


	});
</script>
@endsection