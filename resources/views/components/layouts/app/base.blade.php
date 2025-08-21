<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
	<meta name="description" content="POS - Bootstrap Admin Template">
	<meta name="keywords" content="admin, estimates, bootstrap, business, corporate, creative, invoice, html5, responsive, Projects">
	<meta name="author" content="Dreamguys - Bootstrap Admin Template">
	<meta name="robots" content="noindex, nofollow">
	<title>{{ $title ?? 'Dashboard' }}</title>

	<!-- Favicon -->
	<link rel="shortcut icon" type="image/x-icon" href="{{asset('backend/img/favicon.png') }}">

	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="{{asset('backend/css/bootstrap.min.css') }}">

	<!-- Datetimepicker CSS -->
	<link rel="stylesheet" href="{{asset('backend/css/bootstrap-datetimepicker.min.css') }}">

	<!-- Animation CSS -->
	<link rel="stylesheet" href="{{asset('backend/css/animate.css') }}">

	<!-- Select2 CSS -->
	<link rel="stylesheet" href="{{asset('backend/css/select2.min.css') }}">

	<!-- Fontawesome CSS -->
	<link rel="stylesheet" href="{{asset('backend/plugins/fontawesome/css/fontawesome.min.css') }}">
	<link rel="stylesheet" href="{{asset('backend/plugins/fontawesome/css/all.min.css') }}">

		<!-- Feathericon CSS -->
		<link rel="stylesheet" href="{{ asset('backend/css/feather.css') }}">
        
	<!-- Main CSS -->
	<link rel="stylesheet" href="{{asset('backend/css/style.css') }}">


	<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />



    
    @livewireStyles
</head>

<body>
	<div id="global-loader">
		<div class="whirly-loader"> </div>
	</div>
	<!-- Main Wrapper -->
	<div class="main-wrapper">

		<!-- Header -->
		@include('components.layouts.base_partials.nav')
		<!-- /Header -->

		<!-- Sidebar -->
		@include('components.layouts.base_partials.sidebar')
		<!-- /Sidebar -->

		<!-- Page Wrapper -->
		<div class="page-wrapper">
			{{ $slot }}
		</div>
		<!-- /Page Wrapper -->

		<!-- Customizer -->
		<div class="customizer-links" id="setdata">
			<ul class="sticky-sidebar">
				<li class="sidebar-icons">
					<a href="#" class="navigation-add" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-original-title="Theme">
						<i data-feather="settings" class="feather-five"></i>
					</a>
				</li>
			</ul>
		</div>

	</div>
	<!-- /Main Wrapper -->

    @livewireScripts

	<!-- jQuery -->
	<script src="{{asset('backend/js/jquery-3.7.1.min.js')}}"></script>

		<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


	<!-- Feather Icon JS -->
	<script src="{{asset('backend/js/feather.min.js')}}"></script>

	<!-- Slimscroll JS -->
	<script src="{{ asset('backend/js/jquery.slimscroll.min.js') }}"></script>

	<!-- Bootstrap Core JS -->
	<script src="{{ asset('backend/js/bootstrap.bundle.min.js') }}" data-navigate-once></script>


	<!-- Select2 JS -->
	<script src="{{ asset('backend/js/select2.min.js') }}"></script>

	<!-- Custom JS -->
	<script src="{{ asset('backend/js/script.js') }}"></script>

	<!-- Datetimepicker JS -->
	<script src="{{ asset('backend/js/moment.min.js') }}"></script>
	<script src="{{ asset('backend/js/bootstrap-datetimepicker.min.js') }}"></script>


	<script>  
		document.addEventListener("DOMContentLoaded", function() {
			feather.replace();
			
			// Initialize tooltips
			const tooltips = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
			tooltips.map(t => new bootstrap.Tooltip(t));
		});

		// Re-initialize feather icons and tooltips after Livewire updates
		document.addEventListener("livewire:navigated", () => {
			feather.replace();
			const tooltips = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
			tooltips.map(t => new bootstrap.Tooltip(t));
		});
	</script>
	
</body>

</html>