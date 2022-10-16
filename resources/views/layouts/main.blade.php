<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--favicon-->
    <link rel="icon" href="{{asset('/images/favicon.ico')}}" type="image/png" />
    @if(Request::is('vinyls/details/*'))
        <link href="{{asset('assets/css/fancybox.css')}}" rel="stylesheet">
    @elseif(Request::is('/'))
        <link href="{{asset('assets/plugins/OwlCarousel/css/owl.carousel.min.css')}}" rel="stylesheet" />
    @endif
    <link href="{{asset('assets/css/semantic.min.css')}}" rel="stylesheet">
    <link href="{{asset('assets/plugins/metismenu/css/metisMenu.min.css')}}" rel="stylesheet" />
    <!-- loader-->
    <link href="{{asset('assets/css/pace.min.css')}}" rel="stylesheet" />
    <!-- Bootstrap CSS -->
    <link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{mix("/assets/css/build.css")}}" rel="stylesheet">
    <link href="{{asset('/assets/css/icons.css')}}" rel="stylesheet">
    <link href="{{asset('/assets/css/select2.min.css')}}" rel="stylesheet">
    <title>@yield('title') | VinylFlea.By - Барахолка Виниловых Пластинок</title>
</head>

<body>

<b class="screen-overlay"></b>
<!--wrapper-->
<div class="wrapper">
    <!--start top header wrapper-->
    <div class="header-wrapper">
        <div class="header-content pb-md-0">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-8 col-md-auto">
                        <div class="d-flex align-items-center">
                            <div class="mobile-toggle-menu d-lg-none px-lg-2" data-trigger="#navbar_main"><i class='bx bx-menu'></i>
                            </div>
                            <div class="logo d-none d-lg-flex">
                                <a href="/">
                                    <img src="{{asset('/assets/images/logo-icon.png')}}" class="logo-icon" alt="Logo Icon" />
                                </a>
                            </div>
                        </div>
                    </div>
                    @include('includes.search-block')
                    <div class="col-4 col-md-auto order-2 order-md-4 block-icons">
                        <div class="top-cart-icons float-end">
                            <nav class="navbar navbar-expand">
                                <ul class="navbar-nav ms-auto">
                                    <li class="nav-item cart-list">
                                        @if(auth()->check())
                                            @if (auth()->user()->avatar)
                                                <img src="{{asset('/storage') . auth()->user()->avatar}}" class="avatar-img" />
                                            @else
                                                <img src="{{asset('/assets/images/avatars/no-avatar.png')}}" class="avatar-img" />
                                            @endif
                                        @else
                                            <a href="/profile" class="nav-link cart-link"><i class='bx bx-user'></i></a>
                                        @endif
                                    </li>
                                    @auth
                                        <li class="nav-item"><a  class="logout_link nav-link cart-link"><i class="bx bx-exit"></i></a>
                                        </li>
                                        <form id="logout_form" method="post" action="{{ route('logout') }}">
                                            @csrf
                                        </form>
                                    @endauth
                                </ul>
                            </nav>
                            @include('includes.profile-menu')
                        </div>
                    </div>
                </div>
                <!--end row-->
            </div>
        </div>
       @include('includes.main-menu')
    </div>
    <!--end top header wrapper-->

    <!--start page wrapper -->
    <div class="page-wrapper">
        @include('includes.message')
        <div class="page-content">
            @yield('content')
        </div>
    </div>
    <!--end page wrapper -->
    <!--start footer section-->
    <footer>
        <section class="border-top bg-light">
            <div class="container">

                <div class="row row-cols-1 row-cols-md-2 align-items-center">
                    <div class="col-6 footer-cp">
                        <div class="logo d-lg-flex">
                            <a href="/">
                                <img src="{{asset('/assets/images/logo-icon.png')}}" class="logo-icon" alt="Logo Icon" />
                            </a>
                            <p class="mb-0 copyright">© {{date('Y')}}</p>
                        </div>
                    </div>
                    <div class="info-block col-6">
                        <p><a href="mailto:support@vinylflea.by">support@vinylflea.by</a></p>
                    </div>
                </div>
                <!--end row-->
            </div>
        </section>
    </footer>
    <!--end footer section-->
    <!--start quick view product-->

    <!--Start Back To Top Button--> <a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
    <!--End Back To Top Button-->
</div>
@include('includes.modal')
<!--end wrapper-->

<!-- Bootstrap JS -->
<script src="{{asset("/assets/js/bootstrap.bundle.min.js")}}"></script>
<script src="{{asset("/assets/js/jquery.min.js")}}"></script>
<script src="{{mix("/assets/js/build.js")}}"></script>
@if(Request::is('vinyls/details/*'))
    <script src="{{asset("/assets/js/fancybox.js")}}"></script>
@elseif(Request::is('/'))
    <script src="{{asset("/assets/plugins/OwlCarousel/js/owl.carousel.min.js")}}"></script>
@endif
<script src="{{asset("/assets/js/semantic.min.js")}}"></script>
<script src="{{asset("/assets/js/jquery.inputmask.min.js")}}"></script>
<script src="{{asset("/assets/plugins/metismenu/js/metisMenu.min.js")}}"></script>
<script src="{{asset("/assets/js/pace.min.js")}}"></script>
<script src="{{asset("/assets/js/select2.min.js")}}"></script>
<!--app JS-->
</body>

</html>


