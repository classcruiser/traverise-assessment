@extends((Auth::check() ? 'app' : 'main'))

@section('content')
<!-- Page content -->
	<div class="page-content">

		<!-- Main content -->
		<div class="content-wrapper">

			<!-- Content area -->
			<div class="content d-flex justify-content-center align-items-center">

				<!-- Container -->
				<div class="flex-fill">

					<!-- Error title -->
					<div class="text-center mb-3">
						@if (Auth::check())
							<img src="/images/500.png" width="300" class="d-block mb-2 mx-auto" />
							<h5>Oops, an error has occurred. You can copy the message below to the developer:</h5>
							<code class="error-code w-50 d-block mx-auto p-2">
								{{ $exception->getMessage() }}
								<br /><br />
								URL: {{ request()->url() }}
							</code>
						@else
							<img src="/images/500-user.jpg" class="d-block mx-auto img-fluid rounded" />
							<h3 class="letter-spacing-1"><b>SERVER ERROR</b></h3>
							<p>It seems we have a little problem, please contact our Customer Service team by Live Chat or <a href="mailto:info@kimasurf.com" title="" class="font-weight-bold text-kima">click here</a> to send us an email!</p>
							<br /><br />
							<a href="https://kimasurf.com" title="" class="btn btn-lg btn-kima mr-2"><i class="fal fa-home mr-1"></i> RETURN TO HOMEPAGE</a>
							<a href="https://{{ request()->getHost() }}/book-now" title="" class="btn btn-lg btn-kima"><i class="fal fa-redo mr-1"></i> RETRY BOOKING</a>
						@endif
					</div>
					<!-- /error title -->

				</div>
				<!-- /container -->

			</div>
			<!-- /content area -->

		</div>
		<!-- /main content -->

	</div>
	<!-- /page content -->
  @endsection