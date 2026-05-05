@extends('template.backLayout')


@section('content')
<p><b> {{ $course->course_title }} </b></p>

<p><b> Sisa Waktu : <span id="time-remaining"> - </span> </b></p>

<div class="row mb-3">
	<?php $i = 1; ?>
	@foreach($answers as $answerGroup)
	<div class="col-md-1">
		@foreach($answerGroup as $answer)
		<a href="{{ route('course.exam', $course->id).'?number='.$i }}">
			{{ $i++ }}. {{ $answer->alphabet_answer ?? '-' }}
		</a> <br>
		@endforeach
	</div>
	@endforeach
</div>

<hr>

<button class="btn btn-success done">
	<i class="mdi mdi-check"></i> Selesai
</button>
@endsection


@section('script')
<script type="text/javascript">
	
	$(function(){

		let secondRemaining = parseInt(`{{ $examParticipant->timeRemainingInSeconds() }}`)

		const showTimeRemaining = (seconds) => {
			let text = '';
			if(seconds >= 3600) {
				const hour = Math.floor(seconds/3600)
				text += `${hour} Jam`
				seconds -= hour * 3600 
			}

			if(seconds >= 60) {
				const minute = Math.floor(seconds/60)
				text += ` ${minute} Menit`
				seconds -= minute * 60 
			}

			if(seconds > 0) {
				text += ` ${seconds} Detik`
			}

			$('#time-remaining').text(text.trim())
		}

		setInterval(() => {
			secondRemaining--;
			if(secondRemaining >= 0) {
				showTimeRemaining(secondRemaining)
				console.log(secondRemaining)
			} else {

			}
		}, 1000)

		const $doneBtn = $('.done').ladda()
		$doneBtn.on('click', function(){
			confirmation('Yakin ingin mengakhiri exam?', () => {
				$doneBtn.ladda('start')
				ajaxSetup()
				$.ajax({
					url: `{{ route('course.exam_done', $course->id) }}`,
					dataType: 'json',
					method: 'post'
				})
				.done(response => {
					ajaxSuccessHandling(response)
					const { result_link } = response
					setTimeout(() => {
						window.location.href = result_link
					}, 1000)
				})
				.fail(error => {
					$doneBtn.ladda('stop')
					ajaxErrorHandling(error)
				})
			})
		})

	})

</script>
@endsection