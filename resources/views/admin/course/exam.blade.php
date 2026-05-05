@extends('template.backLayout')


@section('content')
<p><b> Sisa Waktu : <span id="time-remaining"> - </span> </b></p>

<div class="row">

	<div class="col-md-8">


		<div class="border p-4 mb-3">
			<div class="question">
				<p>{{ $number }}.&nbsp;</p>
				<div> {!! $question->question->question() !!} </div>
			</div>

			<div class="answer-list">
				
				@foreach($question->question->answerOptions() as $option)
				<div class="answer-item" data-id="{{ $option->id }}" data-alphabet="{{ chr($loop->iteration + 64) }}">
					<div><b> {{ chr($loop->iteration + 64) }}. </b>&nbsp;&nbsp;</div>
					<div> {!! $option->answer !!} </div>
				</div>
				@endforeach

			</div>

			<hr>

			<div class="row">
				<div class="col-lg-5">
					@if($question->prevQuestionLink)
					<a href="{{ $question->prevQuestionLink }}" class="btn btn-outline-danger btn-block mb-2">
						<i class="mdi mdi-arrow-left"></i> Pertanyaan Sebelumnya
					</a>
					@endif
				</div>
				<div class="col-lg-2"></div>
				<div class="col-lg-5">
					@if($question->nextQuestionLink)
					<a href="{{ $question->nextQuestionLink }}" class="btn btn-outline-primary btn-block mb-2">
						Pertanyaan Selanjutnya <i class="mdi mdi-arrow-right"></i>
					</a>
					@else
					<a href="{{ route('course.exam_answers', $course->id) }}" class="btn btn-outline-success btn-block mb-2">
						<i class="mdi mdi-check"></i> Selesai
					</a>
					@endif
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-4">
		<div class="h3 border rounded p-3" align="center">
			{{ $number }} dari {{ $amount }} Soal
		</div>
		<a href="{{ route('course.exam_answers', $course->id) }}" class="btn btn-inverse-primary btn-block">
			Lihat Semua Jawaban
		</a>
	</div>
</div>
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

		$('.answer-item').on('click', function(){
			$('.chosen').removeClass('chosen')
			$(this).addClass('chosen')

			let { id, alphabet } = $(this).data()

			ajaxSetup()
			$.ajax({
				url: `{{ route('course.set_answer', $question->question->id) }}`,
				method: 'post',
				dataType: 'json',
				data: {
					id_course_exam_answer: id,
					alphabet_answer: alphabet
				}
			})
			.done(response => {
				console.log(response)
			})
			.fail(error => {
				ajaxErrorHandling(error);
			})
		})

		@if(!empty($question->question->id_course_exam_answer))
		$(`.answer-item[data-id="{{ $question->question->id_course_exam_answer }}"]`).addClass('chosen')
		@endif

	})

</script>
@endsection


@section('style')
<style type="text/css">

	.border {
		border-color: 1px solid #dfdfdf;
	}
	
	.question {
		display: flex;
		font-weight: bold;
		align-items: baseline;
	}

	.answer-item {
		display: flex;
		padding: 10px;
		margin-bottom: 10px;
		border: 1px solid #dfdfdf;
		border-radius: 5px;
		cursor: pointer;
	}

	.answer-item:hover {
		background: #dfdfdf;
	}

	.answer-item.chosen {
		background: #c2c2c2;
		border-color: #c2c2c2;
	}

	.btn-outline-primary {
		border-color: #269bff !important;
		color: #269bff !important;
		background: white !important;
	}

	.btn-outline-danger {
		border-color: #F1635F !important;
		background: white !important;
		color: #F1635F !important;
	}

	.btn-outline-success {
		border-color: #0cca8e !important;
		background: white !important;
		color: #0cca8e !important;
	}

</style>
@endsection