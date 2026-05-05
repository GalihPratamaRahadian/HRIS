@extends('template.backLayout')


@section('content')
<div class="row">
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
						@if(auth()->user()->isAdmin())
						<tr>
							<td class="column1"> Status Publikasi </td>
							<td> {!! $announcement->isPublishedHtml() !!} </td>
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

</div>
@endsection


@section('style')
<style type="text/css">
	.column1 {
		width: 180px;
	}
</style>
@endsection