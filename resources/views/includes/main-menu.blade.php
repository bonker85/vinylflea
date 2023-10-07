<div class="primary-menu border-top">
    <div class="container">
        <nav id="navbar_main" class="mobile-offcanvas navbar navbar-expand-lg">
            <div class="offcanvas-header">
                <button class="btn-close float-end"></button>
            </div>
            <ul class="navbar-nav">
                <li class="nav-item el-logo-menu">
                    <a href="/" class="nav-link menu-single-link">
                        <img src="{{asset('/assets/images/logo-icon.png')}}" class="logo-icon" alt="" />
                    </a>
                </li>
                @if (auth()->check())
                <li class="nav-item nav-link login-in-menu">
                    Здравствуйте, <b>{{auth()->user()->name}}</b>
                </li>
                @endif
                <li class="nav-item dropdown">	<a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">Выберите стиль<i class='bx bx-chevron-down'></i></a>
                    <div class="dropdown-menu dropdown-large-menu">
                        <div class="row">
                            <div class="col-md-4">
                                <ul class="">
                                    @foreach ($styles as $style)
                                        <li><a href="{{route('vinyls.style', $style->slug)}}">{{$style->name}} ({{$style->count}})</a></li>
                                        @if ($loop->iteration % 15 === 0)
                                            </ul></div><div class="col-md-4"><ul>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="nav-item" style="color: red;">
                    <a class="nav-link menu-single-link" href="{{route('main.vinyl-list')}}" style="color: #28c75a;">Скачать список</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-single-link" href="{{route('news')}}">Новости</a>
                </li>
                <li class="nav-item dropdown">	<a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">Контакты <i class='bx bx-chevron-down'></i></a>
                    <div class="dropdown-menu dropdown-large-menu menu-info">
                        <div class="row">
                            <div class="col-md-4">
                                <ul class="">
                                        <li class="menu-info-block ">
                                            <a class="viber" href="viber://chat?number=375257167247" style="padding: 0 0 0 10px;"><img src="/images/viber.png" style="width: 20px;padding-top: 5px;"></a>
                                            <a class="telegram" href="https://t.me/vinylfleaby" class="list-inline-item"><i class="bx bxl-telegram"></i></a>
                                            <a href="mailto:support@vinylflea.by" class="list-inline-item desktop-hide"><i class="bx bx-mail-send"></i></a>
                                            <a class="mail mobile-hide" href="mailto:support@vinylflea.by">support@vinylflea.by </a>
                                        </li>
                                </ul>
                                </div>
                                <div class="col-md-4"><ul>
                                </ul>
                            </div>
                        </div>
                    </div>
                </li>
                @auth
                <li class="nav-item dropdown">	<a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">Профиль  @if ($countViewMessages) <div class="mess-indicate-profile">!</div> @endif <i class='bx bx-chevron-down'></i></a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{route('profile.adverts')}}">Мои пластинки</a>
                        </li>
                        <li><a class="dropdown-item" href="{{route('profile.messages')}}">Сообщения @if ($countViewMessages) <div class="mess-indicate">{{$countViewMessages}}</div> @endif</a>
                        </li>
                        <li><a class="dropdown-item" href="{{route('profile.favorit')}}">Избранное</a>
                        </li>
                        <li><a class="dropdown-item" href="{{route('profile.settings')}}">Настройки</a>
                        </li>
                        <li><a class="dropdown-item logout_link" >Выйти</a>
                        </li>
                    </ul>
                </li>
                @elseguest
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('login')}}">Войти в аккаунт</a>
                    </li>
                @endauth
            </ul>
            <div class="info-block">
                <p>
                    <a class="telegram" href="https://t.me/vinylfleaby" class="list-inline-item"><i class="bx bxl-telegram"></i></a>
                    <a class="viber" href="viber://chat?number=375257167247"><img src="/images/viber.png" style="width: 25px;margin-bottom: 15px;margin-right: 5px;"></a>
                    <a class="mail top-mail" href="mailto:support@vinylflea.by">support@vinylflea.by</a>
                </p>
            </div>
        </nav>
    </div>
</div>
