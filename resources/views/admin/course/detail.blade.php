@extends('template.backLayout')


@section('content')
<div class="row">

	<div class="col-md-12">
		<ul class="nav nav-tabs tab-solid tab-solid-danger" role="tablist">
			<li class="nav-item">
				<a class="nav-link active" id="tab-detail" data-toggle="tab" href="#detail" role="tab" aria-controls="detail" aria-selected="true">
					Detail Course
				</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" id="tab-participant" data-toggle="tab" href="#participant" role="tab" aria-controls="participant" aria-selected="false">
					Peserta Course
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
														<td> Judul Course </td>
														<td> : </td>
														<th> {{ $course->course_title }} </th>
													</tr>
													<tr>
														<td> Departemen </td>
														<td> : </td>
														<th> {{ $course->departmentName() }} </th>
													</tr>
													<tr>
														<td> Jabatan </td>
														<td> : </td>
														<th> {{ $course->positionName() }} </th>
													</tr>
													<tr>
														<td> Grup Karyawan </td>
														<td> : </td>
														<th> {{ $course->employeeGroupName() }} </th>
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
														<td> Status Publikasi </td>
														<td> : </td>
														<th> {!! $course->isPublishedHtml() !!} </th>
													</tr>
													<tr>
														<td> Sumber Video </td>
														<td> : </td>
														<th> {{ $course->videoSourceText() }} </th>
													</tr>
													<tr>
														<td> Syarat Kelulusan </td>
														<td> : </td>
														<th> {{ $course->passRequirementFormatted() }} </th>
													</tr>
													<tr>
														<td> Tenggat Waktu </td>
														<td> : </td>
														<th> {{ $course->deadlineText('d M Y') }} </th>
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

				<div class="row">
					<div class="col-lg-7 grid-margin">
						<h4 class="mb-3"><b> Video </b></h4>

						@if($course->videoSourceIsFromFile())
						<video controls="true" autoplay height="auto" width="100%" id="course-video">
							<source src="{{ $course->videoLink() }}" type="{{ mime_content_type($course->videoPath()) }}">
						</video>
						@elseif($course->videoSourceIsFromLink())
						<div id="player"></div>
						@endif
					</div>
				</div>

				<div class="row">
					<div class="col-md-7 mt-3">
						<h4> Komentar </h4>

						<div class="my-4">
							@forelse($course->courseComments as $comment)
							<div class="comment-container">
								<div class="comment-item">
									<div class="comment-avatar">
										<img src="{{ $comment->avatarLink() }}">
									</div>
									<div class="comment-body">
										<div class="comment-user">
											{{ $comment->name() }}
										</div>
										<div class="comment-content">
											{!! $comment->commentHtml() !!}
										</div>
										<div class="comment-meta">
											{{ $comment->createdAtText() }} |
											<a href="javascript:void(0);" class="reply" data-id="{{ $comment->id }}">
												<i class="mdi mdi-reply"></i> Balas 
											</a>
											<a href="javascript:void(0);" class="delete text-danger" data-href="{{ route('course.comment.delete', $comment->id) }}">
												<i class="mdi mdi-trash-can"></i> Hapus 
											</a>
										</div>
									</div>
								</div>
								<div class="comment-reply-list">

									@foreach($comment->replies as $reply)
									<div class="comment-item">
										<div class="comment-avatar">
											<img src="{{ $reply->avatarLink() }}">
										</div>
										<div class="comment-body">
											<div class="comment-user">
												{{ $reply->name() }}
											</div>
											<div class="comment-content">
												{!! $reply->commentHtml() !!}
											</div>
											<div class="comment-meta">
												{{ $reply->createdAtText() }} |
												<a href="javascript:void(0);" class="reply" data-id="{{ $comment->id }}">
													<i class="mdi mdi-reply"></i> Balas 
												</a>
												<a href="javascript:void(0);" class="delete text-danger" data-href="{{ route('course.comment.delete', $reply->id) }}">
													<i class="mdi mdi-trash-can"></i> Hapus 
												</a>
											</div>
										</div>
									</div>
									@endforeach

								</div>
								<div class="comment-reply-create"></div>
							</div>
							@empty
							<p align="center" class="mb-3"> Belum ada komentar </p>
							@endforelse
						</div>

						<hr>

						<form id="comment-form">
							<textarea class="form-control mb-2 bg-white" name="comment" placeholder="Komentar" rows="3" required></textarea>
							<input type="hidden" name="id_course" value="{{ $course->id }}">
							<button class="btn btn-success" type="submit">
								Kirim <i class="mdi mdi-send"></i>
							</button>
						</form>
					</div>
				</div>
			</div>
			<!-- END DETAIL COURSE -->


			<!-- PARTICIPANT -->
			<div class="tab-pane fade" id="participant" role="tabpanel" aria-labelledby="participant">
				<div class="card support-pane-card grid-margin">
					<div class="card-body">

						<div class="d-flex justify-content-between align-items-center mb-3">
							{!! Template::titleBanner('Peserta Course') !!}
						</div>
						
						<div class="table-responsive">
							<table class="table table-bordered table-hover" id="participantTable">
								<thead>
									<tr>
										<th> Nama </th>
										<th> Departemen</th>
										<th> Tgl Mulai Akses </th>
										<th> Akses Video </th>
										<th> Status Kelulusan </th>
									</tr>
								</thead>
								<tbody>
									@foreach($course->courseParticipants as $participant)
									<tr>
										<td> {{ $participant->employeeName() }} </td>
										<td> {{ $participant->departmentName() }} </td>
										<td> {{ $participant->startedAt('Y-m-d') }} </td>
										<td>
											{{ $participant->videoSecondsPassedFormatted() }} / {{ $participant->videoDurationFormatted() }} <br><br>
											{!! $participant->isVideoPassedHtml() !!}
										</td>
										<td> {!! $participant->isHavePassedHtml() !!} </td>
									</tr>
									@endforeach
									@foreach($course->getEmployeesNotAccessed() as $employee)
									<tr>
										<td> {{ $employee->employee_name }} </td>
										<td> {{ $employee->departmentName() }} </td>
										<td> - </td>
										<td>
											<span class="text-danger"> Belum Akses </span>
										</td>
										<td> - </td>
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

		@if($course->videoSourceIsFromLink())
		var tag = document.createElement('script');

		tag.src = "https://www.youtube.com/iframe_api";
		var firstScriptTag = document.getElementsByTagName('script')[0];
		firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

		let player;
		const onYouTubeIframeAPIReady = () => {
			player = new YT.Player('player', {
				height: '390',
				width: '100%',
				videoId: `{{ $course->getYoutubeId() }}`,
				playerVars: {
					'playsinline': 1
				}
			});
		}

		window.onYouTubePlayerAPIReady = () => {
			onYouTubeIframeAPIReady()
		}
		@endif


		const renderEvent = () => {
			$('.comment-container').find('.reply').off('click')
			$('.comment-container').find('.reply').on('click', function(){
				$('.comment-container').find('.comment-reply-create').html('')

				const { id } = $(this).data()
				let html = $('#reply-form-template').text()
							.replaceAll(`{id_comment_reply}`, id)
				$(this).parents('.comment-container').find('.comment-reply-create').html(html);

				const $replyForm = $(this).parents('.comment-container').find('.reply-form')
				const $replySubmitBtn = $replyForm.find(`[type="submit"]`).ladda()

				$replyForm.on('submit', function(e){
					e.preventDefault();
					clearInvalid()

					let formData = $(this).serialize();
					$replySubmitBtn.ladda('start')

					ajaxSetup()
					$.ajax({
						url: `{{ route('course.comment.create') }}`,
						method: 'post',
						dataType: 'json',
						data: formData
					})
					.done(response => {
						ajaxSuccessHandling(response)
						setTimeout(() => {
							window.location.reload()
						}, 1000)
					})
					.fail(error => {
						$replySubmitBtn.ladda('stop')
						ajaxErrorHandling(error)
					})
				})
			})

			$('.comment-container').find('.delete').off('click')
			$('.comment-container').find('.delete').on('click', function(){
				confirmation('Yakin komentar ini akan dihapus?', () => {
					const { href } = $(this).data()

					ajaxSetup()
					$.ajax({
						url: href,
						method: 'post',
						dataType: 'json'
					})
					.done(response => {
						ajaxSuccessHandling(response)
						setTimeout(() => {
							window.location.reload()
						}, 1000)
					})
					.fail(error => {
						ajaxErrorHandling(error)
					})
				})
			})
		}

		const $commentForm = $('#comment-form')
		const $commentSubmitBtn = $commentForm.find(`[type="submit"]`).ladda()

		$commentForm.on('submit', function(e){
			e.preventDefault();
			clearInvalid()

			let formData = $(this).serialize();
			$commentSubmitBtn.ladda('start')

			ajaxSetup()
			$.ajax({
				url: `{{ route('course.comment.create') }}`,
				method: 'post',
				dataType: 'json',
				data: formData
			})
			.done(response => {
				ajaxSuccessHandling(response)
				setTimeout(() => {
					window.location.reload()
				}, 1000)
			})
			.fail(error => {
				$commentSubmitBtn.ladda('stop')
				ajaxErrorHandling(error)
			})
		})

		$('#participantTable').DataTable()

		renderEvent();

	})

</script>

<script type="text/html" id="reply-form-template">
	<form class="reply-form">
		<textarea class="form-control mb-2 bg-white" name="comment" placeholder="Balasan" rows="3" required></textarea>
		<input type="hidden" name="id_comment_reply" value="{id_comment_reply}">
		<input type="hidden" name="id_course" value="{{ $course->id }}">
		<button class="btn btn-success" type="submit">
			Kirim Balasan <i class="mdi mdi-reply"></i>
		</button>
	</form>
</script>
@endsection


@section('style')
<style type="text/css">

	.comment-container .comment-reply-list,
	.comment-container .comment-reply-create {
		padding-left: 2rem;
		margin-bottom: 2rem;
	}

	.comment-item {
		background: white;
		box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
		padding: 1rem;
		display: flex;
		margin-bottom: 1rem;
		border-radius: 5px;
	}

	.comment-item .comment-avatar {
		margin-right: 10px;
	}
	
	.comment-item .comment-avatar img {
		width: 30px;
		height: 30px;
		object-fit: cover;
		object-position: center;
		border-radius: 100%;
	}

	.comment-item .comment-user {
		font-weight: bold;
	}

	.comment-item .comment-content {
		margin-bottom: .5rem;
		font-size: 11pt;
	}

	.comment-item .comment-meta {
		font-size: 7pt;
		color: #a0a0a0;
	}

</style>
@endsection