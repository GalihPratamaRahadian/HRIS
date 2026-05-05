@extends('template.backLayout')


@section('content')
<div class="row">

	<div class="col-md-6">
		<div class="card support-pane-card grid-margin">
			<div class="card-body">
				{!! Template::titleBanner($title) !!}

				<div class="form-group">
					<strong> Waktu </strong> <br>
					<span> {{ $notification->createdAtText() }} </span>
				</div>

				<div class="form-group">
					<strong> Judul </strong> <br>
					<span> {{ $notification->title }} </span>
				</div>

				<div class="form-group">
					<strong> Status </strong> <br>
					<span> {!! $notification->isReadHtml() !!} </span>
				</div>

				<div class="form-group">
					<strong> Isi </strong> <br>
					<span> {{ $notification->description }} </span>
				</div>

			</div>
		</div>
	</div>

</div>
@endsection