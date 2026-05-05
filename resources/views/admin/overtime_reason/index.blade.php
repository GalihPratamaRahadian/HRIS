@extends('template.backLayout')

@section('action')
@if(UserPermission::check('overtime_reason', 'c'))
<a href="{{ route('admin.overtime_reason.create') }}" class="btn btn-success">
	<i class="mdi mdi-plus-thick"></i> Tambah
</a>
@endif
@endsection


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
								<th> Alasan Lembur </th>
								<th width="100"> Aksi </th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th> Alasan Lembur </th>
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
				url : "{{ route('admin.overtime_reason') }}"
			},
			columns : [
				{
					data : "reason",
					name : 'reason'
				},
				{
					data : 'action',
					name : 'action',
					orderable : false,
					searchable : false,
				}
			],
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