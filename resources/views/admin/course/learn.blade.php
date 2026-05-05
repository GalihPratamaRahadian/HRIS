@extends('template.backLayout')


@section('content')
<div class="row">

	<div class="col-md-8">
		<div class="mb-2">
			@if($course->videoSourceIsFromFile())
			<video height="auto" width="100%" id="course-video">
				<source src="{{ $course->videoLink() }}" type="{{ mime_content_type($course->videoPath()) }}">
			</video>
			@elseif($course->videoSourceIsFromLink())
			<div id="player"></div>
			@endif
		</div>
	</div>

	<div class="col-md-4">
		<h2> {{ $course->course_title }} </h2>
		<p>
			<i class="mdi mdi-clock-outline"></i> {{ $course->createdAtText('d F Y H:i') }}
		</p>

		@if($participant->isHavePassed())
		<div class="alert alert-success">
			<i class="mdi mdi-star mr-1"></i>
			Selamat atas kelulusanmu pada course ini.
		</div>
		@else
		<div class="alert alert-primary">
			<i class="mdi mdi-information mr-1"></i>
			@if($course->pass_requirement == \App\Models\Course::PASS_VIDEO)
			Untuk lulus kamu perlu menyelesaikan menonton video course sampai selesai.
			@elseif($course->pass_requirement == \App\Models\Course::PASS_EXAM)
			Untuk lulus kamu perlu lulus exam. Exam bisa diakses setelah kamu menyelesaikan menonton course sampai selesai.
			@endif
		</div>
		@endif

		@if($participant->isHavePassed())
		<a class="btn btn-primary btn-block" href="{{ route('course.download_certificate', $course->id) }}" target="_blank">
			<i class="mdi mdi-certificate-outline"></i> Download Sertifikat
		</a>
		@else
			@if($course->pass_requirement == \App\Models\Course::PASS_EXAM)
				@if($course->courseExam)
				<button class="btn btn-primary exam-btn" disabled>
					Kerjakan Exam <i class="mdi mdi-arrow-right"></i>
				</button>
				@else
				<button class="btn btn-danger" disabled>
					Exam Belum Tersedia
				</button>
				@endif
			@endif
		@endif

	</div>

	<div class="col-md-8 mt-3">
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
@endsection


@section('script')
<script type="text/javascript">
	
	$(function(){

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
		}

		const videoPassed = () => {
			ajaxSetup()
			$.ajax({
				url: `{{ route('course.pass_video', $course->id) }}`,
				method: 'post',
				dataType: 'json',
			})
			.done(response => {
				@if($course->passRequirementIsPassVideo() && $participant->isHavePassed())
				setTimeout(() => {
					window.location.reload()
				}, 1000)
				@endif
			})
		}

		let saveSecondsPassedRequest = false
		const saveSecondsPassed = (seconds, duration) => {
			if(!saveSecondsPassedRequest) {
				saveSecondsPassedRequest = true
				ajaxSetup()
				$.ajax({
					url: `{{ route('course.save_seconds_passed', $course->id) }}`,
					method: 'post',
					dataType: 'json',
					data: {
						'video_seconds_passed': seconds,
						'video_duration': duration
					}
				})
				.done(response => {
					saveSecondsPassedRequest = false
				})
				.fail(error => {
					saveSecondsPassedRequest = false
				})
			}
		}

		$('#course-video').on('ended', function(){
			$('.exam-btn').removeAttr('disabled');
		})

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
					'playsinline': 1,
					'autoplay': 1,
					'controls': 0
				},
				events: {
					'onReady': onPlayerReady,
					'onStateChange': onPlayerStateChange
				}
			});
		}

		const onPlayerReady = (event) => {
			event.target.playVideo();
		}

		const onPlayerStateChange = (state) => {
			if(state.data == 0) {
				videoPassed()
				$('.exam-btn').removeAttr('disabled');
			}
		}

		window.onYouTubePlayerAPIReady = () => {
			onYouTubeIframeAPIReady()

			setInterval(() => {
				saveSecondsPassed(player.getCurrentTime(), player.getDuration())
			}, 2000)
		}
		@else
		// let playerInterval = setInterval(() => {

		// }, 1000)
		$('#course-video').on('ended', function(){
			videoPassed();
		})
		$('#course-video').on('click', function(){
			if(this.paused) {
				$(this).get(0).play()
			} else {
				$(this).get(0).pause()
			}
		})
		setInterval(() => {
			let videoPlayer = $('#course-video').get(0)
			// saveSecondsPassed(videoPlayer)
			console.log(videoPlayer.currentTime)
		}, 2000)
		@endif

		$('.exam-btn').on('click', function(){
			window.location.href = `{{ route('course.exam', $course->id) }}`
		});


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