@extends('layouts.app')

@section('content')
    <section class="py-3 border-bottom border-top d-none d-md-flex bg-light">
        <div class="container">
            <div class="page-breadcrumb d-flex align-items-center">
                <h3 class="breadcrumb-title pe-3">Сброс пароля</h3>
                <div class="ms-auto">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="/"><i class="bx bx-home-alt"></i> Главная</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Сброс пароля</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>
    <!--end breadcrumb-->

    <!--start shop cart-->
    <section class="">
        <div class="container">
            <div class="authentication-reset-password d-flex align-items-center justify-content-center">
                <div class="row">
                    <div class="col-12 col-lg-10 mx-auto">
                        <div class="card">
                            <div class="row g-0">
                                <div class="col-lg-5 border-end">
                                    <div class="card-body">
                                        <div class="p-5">
                                            <h4 class="font-weight-bold">Сгенерировать новый пароль</h4>
                                            <p class="">Мы получили ваш запрос на сброс пароля. Пожалуйста, введите новый пароль!</p>
                                            <form method="POST" action="{{ route('password.update') }}">
                                                @csrf
                                                <input type="hidden" name="token" value="{{ $token }}">
                                                <div class="mb-3 mt-5">
                                                    <label class="form-label">Email</label>
                                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus />
                                                    @error('email')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                                <div class="mb-3 mt-5">
                                                    <label class="form-label">Новый пароль</label>
                                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Введите новый пароль" />
                                                    @error('password')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Повторите новый пароль</label>
                                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="Повторите пароль" />
                                                </div>
                                                <div class="d-grid gap-2">
                                                    <button type="submit" class="btn btn-dark">Изменить пароль</button> <a href="{{route('login')}}" class="btn btn-light"><i class='bx bx-arrow-back mr-1'></i>Вернуться на страницу авторизации</a>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-7">
                                    <img src="{{asset('/assets/images/login-images/forgot-password-frent-img.jpg')}}" class="card-img login-img h-100" alt="...">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
