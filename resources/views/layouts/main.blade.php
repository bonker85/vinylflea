<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <title>@yield('title') | VinylFlea</title>
    <link href="{{asset('assets/css/semantic.min.css')}}" rel="stylesheet">
    <link href="{{mix('assets/css/build.css')}}" rel="stylesheet">
    <link rel="shortcut icon" href="{{asset('assets/images/ico/favicon.ico')}}">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="{{asset('assets/images/ico/apple-touch-icon-144-precomposed.png')}}">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="{{asset('assets/images/ico/apple-touch-icon-114-precomposed.png')}}">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="{{asset('assets/images/ico/apple-touch-icon-72-precomposed.png')}}">
    <link rel="apple-touch-icon-precomposed" href="{{asset('assets/images/ico/apple-touch-icon-57-precomposed.png')}}">
</head><!--/head-->

<body>
<div class="wrapper">
    <header id="header"><!--header-->
       <div class="header_top">
            <div class="container">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="contactinfo">
                            <ul class="nav nav-pills">
                                <li><a href="tel:+375259925434"><i class="fa fa-phone"></i> +375 (25) 992 54 34</a></li>
                               <!-- <li><a href="#"><i class="fa fa-envelope"></i> info@domain.com</a></li>-->
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="social-icons pull-right">
                            <ul class="nav navbar-nav">
                                <li><a href="https://www.instagram.com/mdk_doors/" target="_blank"><i class="fa fa-instagram"></i></a></li>
                                <li><a href="https://vk.com/mdkdoors" target="_blank"><i class="fa fa-vk"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="header-bottom"><!--header-bottom-->
            <div class="container">
                <div class="row">
                    <div class="col-sm-12 col-md-3 logo-block">
                        <div class="logo pull-left">
                            <a href="/"><img src="{{asset('assets/images/home/doors/logo_r.png')}}" alt="" /></a>
                        </div>
                    </div>
                        <div class="col-sm-7 col-md-5">
                            <div class="navbar-header">
                                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                                    <span class="sr-only">Toggle navigation</span>
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                                </button>
                            </div>
                        @include('includes.main-menu')
                        <div class="col-md-4 col-sm-5 ui search">
                            <div class="search_box pull-right ui left icon input">
                                <input  type="text" placeholder="Поиск" class="prompt" autocomplete="off">
                                <i class=" icon"><i class="fa fa-search"></i></i>
                            </div>
                            <div class="results"></div>
                        </div>

                </div>
            </div>
        </div>
    </header><!--/header-->

    <section>
        <div class="container">
            <div class="row">
                @yield('content')
            </div>
        </div>
    </section>
</div>
<footer id="footer"><!--Footer-->
    <div class="footer-top">
        <div class="container">
            <div class="row">
                    <div class="companyinfo">
                        <h2><span>MDK</span>Doors</h2>
                    </div>
                </div>

        </div>
    </div>



    <div class="footer-bottom">
        <div class="container">
            <div class="row">
                <p class="pull-left">Copyright © 2022 MDKDoors</p>
            </div>
        </div>
    </div>

</footer><!--/Footer-->

<script src="{{asset("/assets/js/jquery.js")}}"></script>
<script src="{{asset("/assets/js/semantic.min.js")}}"></script>
<script src="{{asset("/assets/js/jquery.inputmask.min.js")}}"></script>
<script src="{{mix("/assets/js/build.js")}}"></script>
</body>
</html>
