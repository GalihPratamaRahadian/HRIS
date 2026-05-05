@extends('template.backLayout')


@section('content')
<div class="container">
	<div class="row">

		<div class="col-md-8">
			<div class="mb-2">
				<h3> Materi Training </h3>

				@foreach($training->trainingMaterials as $trainingMaterial)
                @if ($training->is_published === 'Ya')
				<div class="p-3 rounded border border-dark mb-3">
					<h4> {{ $trainingMaterial->title }} </h4>
					@if($trainingMaterial->material_type == 'File Upload')
					<a href="{{ $trainingMaterial->fileLink() }}" download>
						<i class="mdi mdi-download"></i> {{ $trainingMaterial->file_material }}
					</a>
                    <br>
                    <br>
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
                @endif
				@endforeach
                    @php
                        $participant = \App\Models\TrainingParticipant::firstOrCreate([
                            'id_training' => $training->id,
                            'id_employee' => employee()->id,
                        ]);
                    @endphp
                    @if ($participant->photo != null)
                        <p class="text-danger">Anda Sudah Melakukan Absen Foto</p>
                    @else
                    <a href="{{ route('employee.training.take_photo', $participant->id) }}" class="btn btn-primary">>> Klik Disini Untuk Melakukan Foto <<</a>
                    @endif
			</div>
		</div>

		<div class="col-md-4">
			<h2> {{ $training->title }} </h2>
			<p>
				<i class="mdi mdi-clock-outline"></i> {{ $training->start_date->format('d M Y') }} - {{ $training->end_date->format('d M Y') }}
			</p>

			<p>
				<i class="mdi mdi-account"></i> {{ $training->trainer_name }}
			</p>

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

	})

</script>
@endsection
