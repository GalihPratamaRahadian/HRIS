@extends('template.backLayout')


@section('content')
<div class="row">
	<div class="col-lg-12">
		@if(auth()->user()->isAdmin())
		@if($announcement->isPublished())
		<div class="grid-margin">
			<button class="btn btn-primary" id="broadcast-btn">
				<i class="mdi mdi-send"></i> Broadcast Ulang
			</button>	
		</div>
		@endif
		@endif
	</div>

	<div class="col-lg-6">
		<div class="card support-pane-card">
			<div class="card-body">
				{!! Template::titleBanner($title) !!}

				<div class="table-responsive">
					<table class="table table-hover">
						<tr>
							<td class="column1"> Judul </td>
							<td> <b>{{ $announcement->title }}</b> </td>
						</tr>
						@if($announcement->department)
						<tr>
							<td class="column1"> Departemen </td>
							<td> <b>{{ $announcement->departmentName() }}</b> </td>
						</tr>
						@endif
						@if($announcement->position)
						<tr>
							<td class="column1"> Jabatan </td>
							<td> <b>{{ $announcement->positionName() }}</b> </td>
						</tr>
						@endif
						@if($announcement->employeeGroup)
						<tr>
							<td class="column1"> Grup Karyawan </td>
							<td> <b>{{ $announcement->employeeGroupName() }}</b> </td>
						</tr>
						@endif
						<tr>
							<td class="column1"> Status Publikasi </td>
							<td> {!! $announcement->isPublishedHtml() !!} </td>
						</tr>
						<tr>
							<td class="column1"> Status Pengiriman </td>
							<td> {!! $announcement->sendStatusHtml() !!} </td>
						</tr>
						@if($announcement->isWaitingToSend())
						<tr>
							<td class="column1"> Jadwal Pengiriman </td>
							<td> {{ $announcement->sendScheduleFormatted('d M Y H:i') }} </td>
						</tr>
						@endif
					</table>
				</div>

				@if(!empty($announcement->content))
				<hr>
				{!! Template::titleBanner('Konten') !!}
				<div class="border rounded p-3">
					{{ str_replace("\n", "<br>", $announcement->content) }}
				</div>
				@endif

				@if($announcement->isHasFile())
				<hr>
				{!! Template::titleBanner('File Pengumuman') !!}
				<div class="border rounded p-3">
					@if($announcement->fileIsImage())
						<img src="{{ $announcement->fileLink() }}" class="img-fluid">
					@elseif($announcement->fileIsVideo())
						<video controls style="height: 300px; width: auto;">
							<source src="{{ $announcement->fileLink() }}">
						</video>
					@elseif($announcement->fileIsDocument())
						@if($announcement->fileExtension() == 'pdf')
							<a href="{{ $announcement->fileLink() }}" class="btn btn-danger">
								<i class="mdi mdi-file-pdf"></i> Download File
							</a>
						@elseif($announcement->fileExtension() == 'xls' || $announcement->fileExtension() == 'xlsx')
							<a href="{{ $announcement->fileLink() }}" class="btn btn-success">
								<i class="mdi mdi-file-excel"></i> Download File
							</a>
						@elseif($announcement->fileExtension() == 'doc' || $announcement->fileExtension() == 'docx')
							<a href="{{ $announcement->fileLink() }}" class="btn btn-primary">
								<i class="mdi mdi-file-word"></i> Download File
							</a>
						@endif
					@endif
				</div>
				@endif


			</div>
		</div>
	</div>

	<div class="col-lg-6">
		<div class="card support-pane-card">
			<div class="card-body">
				{!! Template::titleBanner('Penerima Pengumuman') !!}

				<p>
					Total Penerima : <b>{{ count($announcement->getEmployees()) }} Karyawan </b>
				</p>

				<div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
					<table class="table table-hover table-bordered">
						<thead>
							<tr>
								<th> Karyawan </th>
								<th> Departemen </th>
								<th> Grup Karyawan </th>
							</tr>
						</thead>
						<tbody>
							@foreach($announcement->getEmployees() as $employee)
							<tr>
								<td> {{ $employee->employee_name }} </td>
								<td> 
									{{ $employee->departmentName() }} <br>
									<span class="text-primary"> [{{ $employee->positionName() }}] </span>
								</td>
								<td> {{ $employee->employeeGroupName() }} </td>
							</tr>
							@endforeach
						</tbody>
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

		$('#broadcast-btn').on('click', function(){
			$(this).ladda()

			confirmation('Yakin ingin broadcast ulang?', () => {
				$(this).ladda('start')

				ajaxSetup()
				$.ajax({
					url: `{{ route('announcement.send_broadcast', $announcement->id) }}`,
					method: 'post',
				})
				.done(response => {
					// $(this).ladda('stop')
					ajaxSuccessHandling(response)
					setTimeout(() => {
						window.location.reload()
					}, 1000)
				})
				.fail(error => {
					$(this).ladda('stop')
					ajaxErrorHandling(error)
				})
			})			
		})

	})

</script>
@endsection


@section('style')
<style type="text/css">
	.column1 {
		width: 180px;
	}
</style>
@endsection