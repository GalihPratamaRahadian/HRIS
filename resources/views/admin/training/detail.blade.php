@extends('template.backLayout')

@section('content')
<div class="row">

	<div class="col-md-12">
		<ul class="nav nav-tabs tab-solid tab-solid-danger" role="tablist">
			<li class="nav-item">
				<a class="nav-link active" id="tab-detail" data-toggle="tab" href="#detail" role="tab" aria-controls="detail" aria-selected="true">
					Detail Training
				</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" id="tab-material" data-toggle="tab" href="#material" role="tab" aria-controls="material" aria-selected="false">
					Materi Training
				</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" id="tab-participant" data-toggle="tab" href="#participant" role="tab" aria-controls="participant" aria-selected="false">
					Peserta Training
				</a>
			</li>
		</ul>


		<div class="tab-content tab-content-solid">

			<!-- DETAIL -->
			<div class="tab-pane fade show active" id="detail" role="tabpanel" aria-labelledby="detail">
				<div class="row">
					<div class="col-md-12 grid-margin">
						<div class="card support-pane-card">
							<div class="card-body">
								{!! Template::titleBanner($title) !!}

								<div class="row">
									<div class="col-lg-6">
										<div class="table-responsive">
											<table class="table table-hover">
												<tbody>
													<tr>
														<td> Judul Training </td>
														<td> : </td>
														<th> {{ $training->title }} </th>
													</tr>
													<tr>
														<td> Departemen </td>
														<td> : </td>
														<th> {{ $training->departmentName() }} </th>
													</tr>
													<tr>
														<td> Jabatan </td>
														<td> : </td>
														<th> {{ $training->positionName() }} </th>
													</tr>
													<tr>
														<td> Grup Karyawan </td>
														<td> : </td>
														<th> {{ $training->employeeGroupName() }} </th>
													</tr>
												</tbody>
											</table>
										</div>
									</div>

									<div class="col-lg-6">
										<div class="table-responsive">
											<table class="table table-hover">
												<tbody>
													<tr>
														<td> Trainer / Provider </td>
														<td> : </td>
														<th> {{ $training->trainer_name }} </th>
													</tr>
													<tr>
														<td> Status Publikasi </td>
														<td> : </td>
														<th> {!! $training->isPublishedHtml() !!} </th>
													</tr>
													<tr>
														<td> Tanggal Pelaksanaan </td>
														<td> : </td>
														<th> {{ $training->start_date->format('d F Y') }} - {{ $training->end_date->format('d F Y') }} </th>
													</tr>
													<tr>
														<td> </td>
														<td> </td>
														<th> </th>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>

							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- END DETAIL -->


			<!-- MATERIAL -->
			<div class="tab-pane fade" id="material" role="tabpanel" aria-labelledby="material">
				<div class="row">
					<div class="col-lg-8">
						<div class="card support-pane-card grid-margin">
							<div class="card-body">

								<div class="d-flex justify-content-between align-items-center mb-3">
									{!! Template::titleBanner('Materi Training') !!}
								</div>

								@foreach($training->trainingMaterials as $trainingMaterial)
								<div class="p-3 rounded border border-dark mb-3">
									<h4> {{ $trainingMaterial->title }} </h4>
									@if($trainingMaterial->material_type == 'File Upload')
									<a href="{{ $trainingMaterial->fileLink() }}" download>
										<i class="mdi mdi-download"></i> {{ $trainingMaterial->file_material }}
									</a>
									@elseif($trainingMaterial->material_type == 'File Video')
									<video controls="true" height="auto" width="100%" style="border-radius: 10px;">
										<source src="{{ $trainingMaterial->fileLink() }}" type="{{ mime_content_type($trainingMaterial->filePath()) }}">
									</video>
									@elseif($trainingMaterial->material_type == 'Link Youtube')
										@if($trainingMaterial->getYoutubeId())
										<div id="player{{ $trainingMaterial->id }}"></div>
										@else
										<p> Link Youtube Tidak Valid </p>
										@endif
									@endif
								</div>
								@endforeach

							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- END MATERIAL -->


			<!-- PARTICIPANT -->
			<div class="tab-pane fade" id="participant" role="tabpanel" aria-labelledby="participant">
                <div class="row">
                    <div class="col-lg-6 mb-3 display-flex align-items-center">
                        <button class="btn btn-success exportBtn">
                             <i class="mdi mdi-download"></i> Export
                        </button>
                    </div>
                </div>
				<div class="card support-pane-card grid-margin">
                    <div class="card-body">
						<div class="d-flex justify-content-between align-items-center mb-3">
							{!! Template::titleBanner('Peserta Training') !!}
						</div>

						<div class="table-responsive">
							<table class="table table-bordered" id="participantTable">
								<thead>
									<tr>
										<th> Tgl Ikut Serta </th>
										<th> Karyawan </th>
										<th> Departemen </th>
                                        <th> Foto </th>
                                        <th> Waktu Foto </th>
                                        <th> Tanggal Foto </th>
									</tr>
								</thead>
								<tbody>
									@foreach($training->trainingParticipants as $participant)
									<tr>
										<td> {{ $participant->created_at->format('Y-m-d') }} </td>
										<td> {{ $participant->employeeName() }} </td>
										<td> {{ $participant->departmentName() }} </td>
                                        @if ($participant->photo != null)
                                            <td> {!! $participant->trainingParticipantPhotoHtml() !!} </td>
                                        @else
                                            <td><span class="text-danger">Belum Upload Foto</span></td>
                                        @endif
                                        <td> {{ \Carbon\Carbon::parse($participant->photo_time)->format('H:i:s') }} </td>
                                        <td> {{ \Carbon\Carbon::parse($participant->photo_date)->format('Y-m-d') }} </td>
									</tr>
									@endforeach
								</tbody>
							</table>
						</div>

					</div>
				</div>
			</div>
			<!-- END PARTICIPANT -->
		</div>
	</div>
</div>
@endsection


@section('script')
<script type="text/javascript">
	$(function(){

		const tag = document.createElement('script');

		tag.src = "https://www.youtube.com/iframe_api";
		const firstScriptTag = document.getElementsByTagName('script')[0];
		firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);


		@foreach($training->trainingMaterials as $trainingMaterial)
		@if($trainingMaterial->material_type == 'Link Youtube')
		@if($trainingMaterial->getYoutubeId())
		let player{{ $trainingMaterial->id }};
		const onYouTubeIframeAPIReady{{ $trainingMaterial->id }} = () => {
			player{{ $trainingMaterial->id }} = new YT.Player('player{{ $trainingMaterial->id }}', {
				height: '390',
				width: '100%',
				videoId: `{{ $trainingMaterial->getYoutubeId() }}`,
				playerVars: {
					'playsinline': 1
				}
			});
		}
		@endif
		@endif
		@endforeach

		window.onYouTubePlayerAPIReady = () => {
			@foreach($training->trainingMaterials as $trainingMaterial)
			@if($trainingMaterial->material_type == 'Link Youtube')
			@if($trainingMaterial->getYoutubeId())
			onYouTubeIframeAPIReady{{ $trainingMaterial->id }}()
			@endif
			@endif
			@endforeach
		}

		$('#participantTable').DataTable({
			order: [[ '0', 'desc' ]]
		})

        $('.exportBtn').on('click', function(){
            const url = `{{ route('admin.training.export') }}?id_training={{ $training->id }}`;
            window.open(url, '_blank');
        })

	})
</script>
@endsection
