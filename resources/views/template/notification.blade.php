<div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="notificationDropdown">

	<a class="dropdown-item my-3" href="{{ route('notification') }}">
		<p class="mb-0 font-weight-normal float-left">
		<?php 
			$amount = amountOfUnreadNotifications();
		?>
		@if($amount > 0)
			Kamu punya {{ $amount }} notifikasi baru.
		@else
			Tidak ada notifikasi.
		@endif
		</p>
		<span class="badge badge-pill badge-warning float-right">Lihat semua</span>
	</a>

	@foreach(unreadNotifications() as $notification)
	<div class="dropdown-divider"></div>
	<a class="dropdown-item preview-item" href="{{ route('notification.detail', $notification->id) }}">
		<div class="preview-thumbnail">
			@if($notification->isInformationType())
			<div class="preview-icon bg-primary">
				<i class="mdi mdi-information-outline mx-0"></i>
			</div>
			@elseif($notification->isSuccessType())
			<div class="preview-icon bg-success">
				<i class="mdi mdi-check mx-0"></i>
			</div>
			@elseif($notification->isDangerType())
			<div class="preview-icon bg-danger">
				<i class="mdi mdi-alert mx-0"></i>
			</div>
			@elseif($notification->isWarningType())
			<div class="preview-icon bg-warning">
				<i class="mdi mdi-comment-text-outline mx-0"></i>
			</div>
			@endif
		</div>

		<div class="preview-item-content">
			<h6 class="preview-subject font-weight-medium text-dark">
				{{ $notification->title }}
			</h6>
			<p class="font-weight-light small-text">
				{{ $notification->createdAtText() }}
			</p>
		</div>
	</a>
	@endforeach
</div>