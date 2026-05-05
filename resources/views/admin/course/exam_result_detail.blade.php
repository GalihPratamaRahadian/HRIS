@extends('template.backLayout')


@section('content')
<div class="row">

	<div class="col-md-12">

		<div class="card support-pane-card grid-margin show-on-lg-and-up">
			<div class="card-body">
				{!! Template::titleBanner($title) !!}

				<div class="table-responsive">
					<table class="table table-hover">

						<tr>
							<td> Course </td>
							<td> : </td>
							<th> {{ $course->course_title }} </th>
							<td> Tgl </td>
							<td> : </td>
							<th> {{ $courseExamParticipant->startedAt('d F Y H:i') }} </th>
						</tr>

						<tr>
							<td> Hasil </td>
							<td> : </td>
							<th> {{ $courseExamParticipant->result }} </th>
							<td> Jawaban Benar </td>
							<td> : </td>
							<th> {{ $courseExamParticipant->correct_answer }} / {{ $courseExamParticipant->amountOfQuestions() }} </th>
						</tr>

					</table>
				</div>

			</div>
		</div>

		<div class="card support-pane-card grid-margin show-on-lg-and-up">
			<div class="card-body">
				{!! Template::titleBanner('Pertanyaan/Soal') !!}

				<div class="table-responsive">
					<table class="table table-hover table-bordered" id="question-table">

						<thead>
							<tr>
								<th> No </th>
								<th> Pertanyaan </th>
								<th> Jawaban </th>
							</tr>
						</thead>

						<tbody>
							@forelse($courseExamParticipant->courseExamParticipantAnswers as $question)
							<tr>
								<td align="center"> {{ $loop->iteration }} </td>
								<td> {!! $question->question() !!} </td>
								<td>
									@if($question->isCorrect())
									<div class="answer-item correct">
										{{ $question->answer() }} 
									</div>
									@else
									<div class="answer-item incorrect">
										{{ $question->answer() }} 
									</div>
									@endif

								</td>
							</tr>
							@empty
							@endforelse
						</tbody>

					</table>
				</div>

			</div>
		</div>

	</div>

</div>
@endsection


@section('style')
<style type="text/css">
	.answer-item {
		padding: 15px 15px 15px 40px;
		box-shadow: rgb(100 100 111 / 20%) 0px 7px 29px 0px;
		margin-bottom: 10px;
		border-radius: 6px;
		position: relative;
	}

	.answer-item::before {
		font: normal normal normal 24px/1 "Material Design Icons";
		position: absolute;
		top: 10px;
		left: 10px;
	}

	.answer-item.correct {
		background: #0cca8e;
		color: white;
	}

	.answer-item.correct::before {
		content: "\F05E1";
	}

	.answer-item.incorrect {
		background: #f1635f;
		color: white;
	}

	.answer-item.incorrect::before {
		content: "\F015A";
	}
</style>
@endsection