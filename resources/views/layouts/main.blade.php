<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--favicon-->
    <link rel="icon" href="{{asset('/images/favicon.ico')}}" type="image/png" />
    @if (request()->get('testapp'))
        <link rel="manifest" href="/manifest.json">
        <script>
            if (typeof navigator.serviceWorker !== 'undefined') {
                navigator.serviceWorker.register('/assets/js/sw.js')
            }
        </script>
    @endif
    @if(Request::is('vinyls/details/*') || Request::is('artist/*'))
        <link href="{{asset('assets/css/fancybox.css')}}" rel="stylesheet">
    @else
        <link href="{{asset('assets/plugins/OwlCarousel/css/owl.carousel.min.css')}}" rel="stylesheet" />
    @endif
    <link href="{{asset('assets/css/semantic.min.css')}}" rel="stylesheet">
    <link href="{{asset('assets/plugins/metismenu/css/metisMenu.min.css')}}" rel="stylesheet" />
    <!-- loader-->
  <!--  <link href="{{asset('assets/css/pace.min.css')}}" rel="stylesheet" />-->
    <!-- Bootstrap CSS -->
    <link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{mix("/assets/css/build.css")}}" rel="stylesheet">
    @if (auth()->check() && request()->is('profile/*'))
        <link href="{{asset('/assets/css/select2.min.css')}}" rel="stylesheet">
    @endif
    <meta name="description" content="@yield('description')">
    <title>@yield('title') | VinylFlea.By - Барахолка Виниловых Пластинок</title>
    @if (env('APP_ENV') == 'production' && !request()->disable_tag)
        @if (\App\Models\User::isMyUsers())
        @else
    <!-- Yandex.Metrika counter -->
        <script type="text/javascript" >
            (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
                m[i].l=1*new Date();
                for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
                k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
            (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

            ym(90842576, "init", {
                clickmap:true,
                trackLinks:true,
                accurateTrackBounce:true,
                webvisor:true
            });
        </script>
        <noscript><div><img src="https://mc.yandex.ru/watch/90842576" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
        <!-- /Yandex.Metrika counter -->
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-KKGRG5MY09"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', 'G-KKGRG5MY09');
        </script>
        @endif
    @endif
</head>

<body>
<!--wrapper-->
<div class="wrapper">
    <!--start top header wrapper-->
    <div class="header-wrapper">
        <div class="header-content pb-md-0">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-auto">
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
                    <div class="col-auto order-4 order-md-4 block-icons">
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
            <section>
                <div class="container">
                    <div class="row">
                        <div class="list-inline social d-flex justify-content-center">
                            <a href="https://www.facebook.com/profile.php?id=100087038031409" class="list-inline-item"><i class="bx bxl-facebook-square"></i></a>
                            <a href="https://vk.com/thisisyouvinyl" class="list-inline-item"><i class="bx bxl-vk"></i></a>
                            <a href="https://www.instagram.com/vinyl.flea/" class="list-inline-item"><i class="bx bxl-instagram"></i></a>
                            <a href="https://t.me/vinylflea" class="list-inline-item"><i class="bx bxl-telegram"></i></a>
                        </div>
                    </div>
                </div>
            </section>
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
                        <p>
                            <a class="mail" href="mailto:support@vinylflea.by">support@vinylflea.by</a>
                        </p>
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
@if(Request::is('vinyls/details/*') || Request::is('artist/*'))
    <script src="{{asset("/assets/js/fancybox.js")}}"></script>
@else
    <script src="{{asset("/assets/plugins/OwlCarousel/js/owl.carousel.min.js")}}"></script>
@endif
<script src="{{asset("/assets/js/semantic.min.js")}}"></script>
<script src="{{asset("/assets/plugins/metismenu/js/metisMenu.min.js")}}"></script>
<!--<script src="{{asset("/assets/js/pace.min.js")}}"></script>-->
@if (auth()->check() && request()->is('profile/*'))

    <script>
        $(document).ready(function() {
            $('.select2').select2();
            $(".profile-info-formblock #phone").inputmask({"mask": "+375 (99) 999-99-99"});
            $(".profile-add_advert-formblock #year").inputmask({"mask": "9999"});
        });
    </script>
    <script src="{{asset("/assets/js/jquery.inputmask.min.js")}}"></script>
    <script src="{{asset("/assets/js/select2.min.js")}}"></script>
@endif
<!--app JS-->
<b class="screen-overlay"></b>
</body>

</html>


