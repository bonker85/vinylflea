@extends('layouts.main')
@section('title', 'Настройки')
@section('content')
    <section class="profile-breadcrumbs">
        <div class="container">
            <div class="page-breadcrumb pb-3 d-flex align-items-center">
                <h3 class="breadcrumb-title pe-3">Настройки</h3>
                <div class="ms-auto">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="/">Главная</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Настройки</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>
    <section>
        <div class="container">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        @include('includes.profile-menu')
                        <div class="col-lg-8">
                            <div class="card shadow-none mb-3 border profile-info-formblock">
                                <div class="card-body">
                                    <form class="row g-3 needs-validation" method="post" action="{{route('profile.settings')}}" enctype="multipart/form-data" novalidate>
                                        <input type="hidden" name="action" value="info" />
                                        @csrf
                                        <div class="col-12">
                                            <label class="form-label">Имя <span class="need-field">*</span></label>
                                            <input type="text" class="form-control" required name="name" maxlength="20" value="{{$user->name}}">
                                            <div class="invalid-tooltip">
                                                Поле не должно быть пустым!
                                            </div>
                                            @error('name')
                                                <span class="invalid-feedback d-block" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-12">
                                            <label for="js-file-avatar" class="form-label">Аватар</label>
                                            <div class="avatar-set mb-3">
                                                <div class="spinner-border mb-3 d-none"></div>
                                                @if ($user->avatar)
                                                    <img src="{{asset('storage') . $user->avatar}}" id="img-avatar" />
                                                @else
                                                    <img src="{{asset('assets/images/avatars/no-avatar.png')}}" id="img-avatar" />
                                                @endif

                                            </div>
                                            <div class="error_message" style="display: none;"></div>
                                            <input class="form-control" name="avatar" type="file" id="js-file-avatar">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Номер телефона</label>
                                            <input type="text" id="phone" name="phone" class="form-control" value="{{$user->phone}}">
                                            @error('phone')
                                            <span class="invalid-feedback d-block" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Город</label>
                                            <select class="form-control select2" name="city" style="width: 100%; height: 50px">
                                                <option value="">Выберите город</option>
                                                @foreach ($cities as $city)
                                                    <option value="{{$city->name}}" @if($user->city == $city->name) selected @endif>{{$city->name}}</option>
                                                @endforeach
                                            </select>
                                            @error('city')
                                            <span class="invalid-feedback d-block" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-dark btn-ecomm">Сохранить</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="card shadow-none mb-3 border profile-info-formblock">
                                <div class="card-body">
                                    <form class="row g-3 needs-validation" method="post" action="{{route('profile.settings')}}" novalidate>
                                        @csrf
                                        <input type="hidden" name="action" value="email" />
                                        <div class="col-12">
                                            <label class="form-label">Email</label>
                                            <input type="email" name="email" required class="form-control" value="{{$user->email}}">
                                            <div class="invalid-tooltip">
                                                Неверный формат email адреса!
                                            </div>
                                            @error('email')
                                            <span class="invalid-feedback d-block" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-dark btn-ecomm">Изменить</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="card shadow-none mb-0 border profile-info-formblock">
                                <div class="card-body">
                                    <form class="row g-3 needs-validation" method="post" action="{{route('profile.settings')}}" novalidate>
                                        @csrf
                                        <input type="hidden" name="action" value="password" />
                                        <div class="col-12">
                                            <label class="form-label">Новый пароль <small><b>(от 8 до 20 символов)</b></small></label>
                                            <input type="password"  name="password" class="form-control" required pattern=".{8,20}">
                                            @error('password')
                                                <span class="invalid-feedback d-block" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Повторить пароль</label>
                                            <input type="password" name="password_confirm" required class="form-control" pattern=".{8,20}" >
                                            @error('password_confirm')
                                                <span class="invalid-feedback d-block" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-dark btn-ecomm">Изменить</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end row-->
                </div>
            </div>
        </div>
    </section>
@endsection
