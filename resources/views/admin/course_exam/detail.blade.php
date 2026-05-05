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
							<th> {{ $courseExam->courseTitle() }} </th>
							<td> Durasi Exam </td>
							<td> : </td>
							<th> {{ $courseExam->durationText() }} </th>
						</tr>

						<tr>
							<td> Pertanyaan Acak </td>
							<td> : </td>
							<th> {{ $courseExam->isRandomQuestionText() }} </th>
							<td> Jumlah Pertanyaan </td>
							<td> : </td>
							<th> {{ $courseExam->amount_of_questions }} </th>
						</tr>

					</table>
				</div>

			</div>
		</div>

		<div class="card support-pane-card grid-margin show-on-lg-and-up">
			<div class="card-body">
				{!! Template::titleBanner('Pertanyaan/Soal') !!}

				@if(UserPermission::check('course_exam', 'u'))
				<div class="text-right">
					<button class="btn btn-primary" id="refresh-question">
						<i class="mdi mdi-sync"></i> Refresh Pertanyaan
					</button>
					<button class="btn btn-success" data-toggle="modal" data-target="#create-modal">
						<i class="mdi mdi-plus-thick"></i> Tambah Pertanyaan
					</button>
				</div>

				<hr>
				@endif

				<div class="table-responsive">
					<table class="table table-hover table-bordered" id="question-table">

						<thead>
							<tr>
								<th> No </th>
								<th> Pertanyaan </th>
								<th> Jawaban </th>
								@if(UserPermission::check('course_exam', 'u'))
								<th width="100"> Aksi </th>
								@endif
							</tr>
						</thead>

						<tbody>
						</tbody>

					</table>
				</div>

			</div>
		</div>

	</div>

</div>
@endsection


@section('modal')
<div class="modal fade" id="create-modal" role="dialog" aria-hidden="true">
	<div class="modal-dialog mt-4" role="document">
		<div class="modal-content">
			<form id="create-form">

				<div class="modal-header">
					<h5 class="modal-title">
						Tambah Pertanyaan
					</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>

				<div class="modal-body">

					<div class="form-group">
						<label> Pertanyaan {!! Template::required() !!} </label>
						<textarea class="textarea" name="question" required></textarea>
					</div>
					
				</div>

				<div class="modal-footer">
					<button type="submit" class="btn btn-success">
						<i class="mdi mdi-plus"></i> Tambah
					</button>
					<button type="button" class="btn btn-light" data-dismiss="modal">
						<i class="mdi mdi-close"></i> Tutup
					</button>
				</div>

			</form>
		</div>
	</div>
</div>


<div class="modal fade" id="edit-modal" role="dialog" aria-hidden="true">
	<div class="modal-dialog mt-4" role="document">
		<div class="modal-content">
			<form id="edit-form">

				<div class="modal-header">
					<h5 class="modal-title">
						Edit Pertanyaan
					</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>

				<div class="modal-body">

					<div class="form-group">
						<label> Pertanyaan {!! Template::required() !!} </label>
						<textarea class="textarea" name="question" required></textarea>
					</div>
					
				</div>

				<div class="modal-footer">
					<button type="submit" class="btn btn-success">
						<i class="mdi mdi-pencil"></i> Edit
					</button>
					<button type="button" class="btn btn-light" data-dismiss="modal">
						<i class="mdi mdi-close"></i> Tutup
					</button>
				</div>

			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="add-answer-modal" role="dialog" aria-hidden="true">
	<div class="modal-dialog mt-4" role="document">
		<div class="modal-content">
			<form id="add-answer-form">

				<div class="modal-header">
					<h5 class="modal-title">
						Tambah Jawaban
					</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>

				<div class="modal-body">

					<input type="hidden" name="id_course_exam_question">

					<div class="form-group">
						<label> Jawaban {!! Template::required() !!} </label>
						<textarea name="answer" class="form-control" rows="3" placeholder="Isi Jawaban" required></textarea>
					</div>

					<div class="form-group">
						<label> Jawaban Benar {!! Template::required() !!} </label>
						<select class="form-control" name="is_correct" required>
							<option value="no"> Tidak </option>
							<option value="yes"> Ya </option>
						</select>
					</div>
					
				</div>

				<div class="modal-footer">
					<button type="submit" class="btn btn-success">
						<i class="mdi mdi-plus"></i> Tambah
					</button>
					<button type="button" class="btn btn-light" data-dismiss="modal">
						<i class="mdi mdi-close"></i> Tutup
					</button>
				</div>

			</form>
		</div>
	</div>
</div>


<div class="modal fade" id="edit-answer-modal" role="dialog" aria-hidden="true">
	<div class="modal-dialog mt-4" role="document">
		<div class="modal-content">
			<form id="edit-answer-form">

				<div class="modal-header">
					<h5 class="modal-title">
						Edit Jawaban
					</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>

				<div class="modal-body">

					<div class="form-group">
						<label> Jawaban {!! Template::required() !!} </label>
						<textarea name="answer" class="form-control" rows="3" placeholder="Isi Jawaban" required></textarea>
					</div>

					<div class="form-group">
						<label> Jawaban Benar {!! Template::required() !!} </label>
						<select class="form-control" name="is_correct" required>
							<option value="no"> Tidak </option>
							<option value="yes"> Ya </option>
						</select>
					</div>
					
				</div>

				<div class="modal-footer">
					<button type="submit" class="btn btn-success">
						<i class="mdi mdi-pencil"></i> Edit
					</button>
					<button type="button" class="btn btn-danger delete-answer">
						<i class="mdi mdi-trash-can"></i> Hapus
					</button>
					<button type="button" class="btn btn-light" data-dismiss="modal">
						<i class="mdi mdi-close"></i> Tutup
					</button>
				</div>

			</form>
		</div>
	</div>
</div>
@endsection


@section('script')
<script type="text/javascript">
	$(function(){


		$createForm = $('#create-form')
		$createSubmitBtn = $createForm.find(`[type="submit"]`).ladda()
		
		$editForm = $('#edit-form')
		$editSubmitBtn = $editForm.find(`[type="submit"]`).ladda()
		
		$addAnswerForm = $('#add-answer-form')
		$addAnswerSubmitBtn = $addAnswerForm.find(`[type="submit"]`).ladda()

		$editAnswerForm = $('#edit-answer-form')
		$editAnswerSubmitBtn = $editAnswerForm.find(`[type="submit"]`).ladda()

		const renderEvent = () => {
			@if(UserPermission::check('course_exam', 'c'))
			$('#question-table').find('.edit').off('click')
			$('#question-table').find('.edit').on('click', function(){
				let { getHref, editHref } = $(this).data();

				$.get({
					url: getHref,
					dataType: 'json'
				})
				.done(response => {
					const { courseExamQuestion } = response
					const { question } = courseExamQuestion

					$editForm.find(`[name="question"]`).summernote('code', question)
					$editForm.attr('action', editHref)
					$('#edit-modal').modal('show')
				})
			})

			$('#question-table').find('.delete').off('click')
			$('#question-table').find('.delete').on('click', function(){
				let { href } = $(this).data();

				confirmation('Yakin ingin dihapus?', () => {
					ajaxSetup();
					$.ajax({
						url: href,
						method: 'delete',
						dataType: 'json'
					})
					.done(response => {
						ajaxSuccessHandling(response)
						renderQuestions()
					})
					.fail(error => {
						ajaxErrorHandling(error, $createForm)
					})
				})
			})

			$('#question-table').find('.add-answer').off('click')
			$('#question-table').find('.add-answer').on('click', function(){
				const { questionId } = $(this).data()
				$addAnswerForm.find(`[name="id_course_exam_question"]`).val(questionId)
				$('#add-answer-modal').modal('show');
			})

			$('#question-table').find('.answer-item').off('click')
			$('#question-table').find('.answer-item').on('click', function(){
				let { getHref, editHref, destroyHref } = $(this).data();

				$.get({
					url: getHref,
					dataType: 'json'
				})
				.done(response => {
					const { courseExamAnswerOption } = response
					const { answer, is_correct } = courseExamAnswerOption

					$editAnswerForm.find(`[name="answer"]`).val(answer)
					$editAnswerForm.find(`[name="is_correct"]`).val(is_correct)
					$editAnswerForm.attr('action', editHref)
					$('#edit-answer-modal').find('.delete-answer').data('href', destroyHref)
					$('#edit-answer-modal').modal('show')
				})
			})
			@endif
		}


		const loadingQuestions = () => {
			const html = $('#loading-item-template').text()
			$('#question-table').find('tbody').html(html)
		}

		const emptyQuestions = () => {
			const html = $('#empty-item-template').text()
			$('#question-table').find('tbody').html(html)
		}


		const getQuestions = async () => {
			let result = null
			await $.get({
				url: `{{ route('admin.course_exam.get', $courseExam->id) }}`,
				dataType: 'json'
			})
			.done(response => {
				const { courseExamQuestions } = response
				result = courseExamQuestions;
			})

			return result;
		}

		const renderQuestions = () => {
			loadingQuestions()
			getQuestions().then(questions => {
				if(questions.length == 0) {
					emptyQuestions();
				} else {
					let html = '';
					questions.forEach((questionItem, iteration) => {
						const { id, question, answers, get_link, update_link, destroy_link } = questionItem
						let answersHtml = '';
						answers.forEach(answer => {
							if(answer.is_correct) {
								answersHtmlTmp = $('#correct-answer-item-template').text()
							} else {
								answersHtmlTmp = $('#incorrect-answer-item-template').text()
							}

							answersHtmlTmp = answersHtmlTmp
											.replaceAll('{answer}', answer.answer)
											.replaceAll('{get_link}', answer.get_link)
											.replaceAll('{update_link}', answer.update_link)
											.replaceAll('{destroy_link}', answer.destroy_link)
							answersHtml += answersHtmlTmp

						})
						html += $('#question-item-template').text()
								.replaceAll('{number}', iteration + 1)
								.replaceAll('{id}', id)
								.replaceAll('{question}', question)
								.replaceAll('{get_link}', get_link)
								.replaceAll('{update_link}', update_link)
								.replaceAll('{destroy_link}', destroy_link)
								.replaceAll('{answers_html}', answersHtml);
					})
					$('#question-table').find('tbody').html(html)
					renderEvent();
				}
			})
		}

		renderQuestions()


		$createForm.find(`[name="question"]`).summernote({
			'height': '200px',
		})

		$createForm.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = $(this).serialize();
			formData += `&id_course_exam={{ $courseExam->id }}`

			$createSubmitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url: `{{ route('admin.course_exam_question.store') }}`,
				data: formData,
				method: 'post',
				dataType: 'json'
			})
			.done(response => {
				ajaxSuccessHandling(response)
				renderQuestions();
				$createSubmitBtn.ladda('stop')
				$createForm[0].reset()
				$createForm.find(`[name="question"]`).summernote('code', '')
				$('#create-modal').modal('hide')
			})
			.fail(error => {
				$createSubmitBtn.ladda('stop')
				ajaxErrorHandling(error, $createForm)
			})
		})



		$editForm.find(`[name="question"]`).summernote({
			'height': '200px',
		})

		$editForm.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = $(this).serialize();
			let href = $(this).attr('action')

			$editSubmitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url: href,
				data: formData,
				method: 'put',
				dataType: 'json'
			})
			.done(response => {
				$editSubmitBtn.ladda('stop')
				ajaxSuccessHandling(response)
				renderQuestions();
				$('#edit-modal').modal('hide')
			})
			.fail(error => {
				$editSubmitBtn.ladda('stop')
				ajaxErrorHandling(error, $editForm)
			})
		})



		$addAnswerForm.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = $(this).serialize();

			$addAnswerSubmitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url: `{{ route('admin.course_exam_answer_option.store') }}`,
				data: formData,
				method: 'post',
				dataType: 'json'
			})
			.done(response => {
				ajaxSuccessHandling(response)
				renderQuestions();
				$addAnswerSubmitBtn.ladda('stop')
				$addAnswerForm[0].reset()
				$('#add-answer-modal').modal('hide')
			})
			.fail(error => {
				$addAnswerSubmitBtn.ladda('stop')
				ajaxErrorHandling(error, $addAnswerForm)
			})
		})


		$editAnswerForm.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = $(this).serialize();
			let href = $(this).attr('action')

			$editAnswerSubmitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url: href,
				data: formData,
				method: 'put',
				dataType: 'json'
			})
			.done(response => {
				$editAnswerSubmitBtn.ladda('stop')
				ajaxSuccessHandling(response)
				renderQuestions();
				$('#edit-answer-modal').modal('hide')
			})
			.fail(error => {
				$editAnswerSubmitBtn.ladda('stop')
				ajaxErrorHandling(error, $editAnswerForm)
			})
		})


		$('#add-answer-modal').on('shown.bs.modal', function(){
			$(this).find(`[name="answer"]`).focus()
		})

		$('#edit-answer-modal').on('shown.bs.modal', function(){
			$(this).find(`[name="answer"]`).focus()
		})

		$('#edit-answer-modal').find('.delete-answer').on('click', function(){
			let { href } = $(this).data();

			confirmation('Yakin ingin dihapus?', () => {
				ajaxSetup();
				$.ajax({
					url: href,
					method: 'delete',
					dataType: 'json'
				})
				.done(response => {
					ajaxSuccessHandling(response)
					renderQuestions();
					$('#edit-answer-modal').modal('hide')
				})
				.fail(error => {
					ajaxErrorHandling(error, $createForm)
				})
			})
		})

		$('#refresh-question').on('click', function(){
			renderQuestions()
		})

	})
</script>



<script type="text/html" id="question-item-template">
	<tr>
		<td align="center" width="20"> {number} </td>
		<td> {question} </td>
		<td>
			{answers_html}
			@if(UserPermission::check('course_exam', 'u'))
			<a class="add-answer" href="javascript:void(0);" data-question-id="{id}">
				<i class="mdi mdi-plus"></i> Tambah Opsi Jawaban
			</a>
			@endif
		</td>
		@if(UserPermission::check('course_exam', 'u'))
		<td>
			<div class="dropdown">
				<button class="btn btn-primary dropdown-toggle py-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Aksi
				</button>
				<div class="dropdown-menu">
					<button class="dropdown-item edit" data-get-href="{get_link}" data-edit-href="{update_link}" title="Edit Pertanyaan">
						<i class="mdi mdi-pencil"></i> Edit 
					</button>
					<button class="dropdown-item delete" href="javascript:void(0);" data-href="{destroy_link}" title="Hapus Pertanyaan">
						<i class="mdi mdi-trash-can"></i> Hapus
					</button>
				</div>
			</div>
		</td>
		@endif
	</tr>
</script>

<script type="text/html" id="loading-item-template">
	<tr>
		@if(UserPermission::check('course_exam', 'u'))
		<td colspan="4" align="center">
			<i class="mdi mdi-loading mdi-spin"></i> Loading...
		</td>
		@else
		<td colspan="3" align="center">
			<i class="mdi mdi-loading mdi-spin"></i> Loading...
		</td>
		@endif
	</tr>
</script>

<script type="text/html" id="empty-item-template">
	<tr>
		@if(UserPermission::check('course_exam', 'u'))
		<td colspan="4" align="center">
			Belum Ada Pertanyaan/Soal
		</td>
		@else
		<td colspan="3" align="center">
			Belum Ada Pertanyaan/Soal
		</td>
		@endif
	</tr>
</script>

<script type="text/html" id="correct-answer-item-template">
	<div class="answer-item correct" data-get-href="{get_link}" data-edit-href="{update_link}" data-destroy-href="{destroy_link}">
		{answer}
	</div>
</script>

<script type="text/html" id="incorrect-answer-item-template">
	<div class="answer-item incorrect" data-get-href="{get_link}" data-edit-href="{update_link}" data-destroy-href="{destroy_link}">
		{answer}
	</div>
</script>
@endsection


@section('style')
<style type="text/css">
	.answer-item {
		padding: 15px 15px 15px 40px;
		box-shadow: rgb(100 100 111 / 20%) 0px 7px 29px 0px;
		margin-bottom: 10px;
		border-radius: 6px;
		position: relative;
		cursor: pointer;
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