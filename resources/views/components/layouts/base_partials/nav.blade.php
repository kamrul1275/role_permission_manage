
	<!-- Header -->
		<div class="header">

			<!-- Logo -->
			<div class="header-left active">
				<a href="index.html" class="logo logo-normal">
					<img src="{{ route('dashboard')}}" alt="">
				</a>
				<a href="index.html" class="logo logo-white">
					<img src="assets/img/logo-white.png" alt="">
				</a>
				<a href="index.html" class="logo-small">
					<img src="assets/img/logo-small.png" alt="">
				</a>
				<a id="toggle_btn" href="javascript:void(0);">
					<i data-feather="chevrons-left" class="feather-16"></i>
				</a>
			</div>
			<!-- /Logo -->

			<a id="mobile_btn" class="mobile_btn" href="#sidebar">
				<span class="bar-icon">
					<span></span>
					<span></span>
					<span></span>
				</span>
			</a>

			<!-- Header Menu -->
			<ul class="nav user-menu">

				<!-- Search -->
				<li class="nav-item nav-searchinputs">
				
				</li>
				<!-- /Search -->

				<!-- Notifications -->
				<li class="nav-item dropdown nav-item-box">
					
				</li>
				<!-- /Notifications -->

				<li class="nav-item nav-item-box">
					{{-- <a href="general-settings.html"><i data-feather="settings"></i></a> --}}
				</li>
				<li class="nav-item dropdown has-arrow main-drop">
					<a href="javascript:void(0);" class="dropdown-toggle nav-link userset" data-bs-toggle="dropdown">
						<span class="user-info">
							<span class="user-letter">
								<img src="{{ asset('backend/img/avatar/icon2.png') }}" alt="" class="img-fluid">
							</span>
							<span class="user-detail">
								<span class="user-name">{{ Auth::user()->name }}</span>
								
							</span>
						</span>
					</a>
					<div class="dropdown-menu menu-drop-user">
						<div class="profilename">
							<div class="profileset">
								<span class="user-img"><img src="{{ asset('backend/img/avatar/icon2.png') }}" alt="">
									<span class="status online"></span></span>
								<div class="profilesets">
									<h6>{{ Auth::user()->name }}</h6>
									
								</div>
							</div>
							<hr class="m-0">
							<a class="dropdown-item" href="profile.html"> <i class="me-2" data-feather="user"></i> My Profile</a>
							<a class="dropdown-item" href="general-settings.html"><i class="me-2" data-feather="settings"></i>Settings</a>
							<hr class="m-0">
							<a class="dropdown-item logout pb-0" href="{{ route('logout') }}"> <img src="{{ asset('backend/img/icons/log-out.svg') }}" class="me-2" alt="Logout">
                                Logout</a>
						</div>
					</div>
				</li>
			</ul>
			<!-- /Header Menu -->

			<!-- Mobile Menu -->
			<div class="dropdown mobile-user-menu">
				<a href="javascript:void(0);" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
				<div class="dropdown-menu dropdown-menu-right">
					<a class="dropdown-item" href="profile.html">My Profile</a>
					<a class="dropdown-item" href="general-settings.html">Settings</a>
					<a class="dropdown-item" href="{{ route('logout') }}">Logout</a>
				</div>
			</div>
			<!-- /Mobile Menu -->


			<style>
				.user-info::after{
					top: 13px !important;
				}
			</style>
		</div>
		<!-- /Header -->