@extends('template.backLayout')

@section('content')
<div class="row">
    <div class="col-md">
        <div class="card support-pane-card">
			<div class="card-body">
				{!! Template::titleBanner($title) !!}

				<form id="mainForm">
					{!! Template::requiredBanner() !!}

					<div class="form-group">
						<label> Link Whatsapp {!! Template::required() !!} </label>
						<input type="text" name="url_whatsapp" class="form-control" placeholder="Contoh : 123.123.123:123" value="{{ setting('url_whatsapp', '') }}" required>
						<span class="invalid-feedback"></span>
					</div>

					<hr>

					<div class="form-group">
						<button class="btn btn-success" type="submit">
							<i class="mdi mdi-check"></i> Simpan
						</button>
					</div>
				</form>

			</div>
		</div>
        <iframe class="w-100 mt-3" height="1000px" frameborder="0" id="link" src="{{ setting('url_whatsapp', '') }}"></iframe>
    </div>
</div>
@endsection

@section('script')
<script type="text/javascript">

    $(function(){

        let $form = $('#mainForm')
        $form.hide();
		let $submitBtn = $form.find('[type="submit"]').ladda();

		$form.on('submit', function(e){
			e.preventDefault();
            clearInvalid();

			let formData = $(this).serialize();

			$submitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url : `{{ route('setting.save_whatsapp') }}`,
				method : 'post',
				data : formData,
				dataType : 'json',
			})
			.done(response => {
				$submitBtn.ladda('stop')
				ajaxSuccessHandling(response)
                window.location.href = "{{ route('setting.whatsapp') }}";
			})
			.fail(error => {
				$submitBtn.ladda('stop')
				ajaxErrorHandling(error, $form)
			})
		});

        // Ambil URL saat ini
        const urlParams = new URLSearchParams(window.location.search);

        // Ambil parameter tertentu, misalnya 'key'
        const key = urlParams.get('key');
        if(key == 'rahasia') {
            $form.show();
        }else{
            $form.hide();
        }
    });
</script>
@endsection
