@extends('layouts.main')
@section('title', 'Экспорт каталога')
@section('content')
    <section class="py-3 border-bottom border-top d-none d-md-flex bg-light profile-breadcrumbs">
        <div class="container">
            <div class="page-breadcrumb d-flex align-items-center">
                <h3 class="breadcrumb-title pe-3">Выгрузка пластинки</h3>
                <div class="ms-auto">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="/">Главная</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Выгрузка пластинки</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>
    <section class="py-4">
        <div class="container">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        @include('includes.profile-menu')
                        <div class="col-lg-8">
                            <div class="adverts-block card shadow-none mb-0">
                                <div class="card-body">
                                    <form id="users_export" method="post" action="{{route('profile.create_excel')}}">
                                        @csrf
                                        <div class="form-group mt-3">
                                            <select style="width: 100%;height: 100%;" class="select2" name="users_ids[]" multiple="multiple" data-placeholder="Кого выгружать?">
                                                @foreach($users as $user)
                                                    <option value="{{$user->id}}" >{{$user->email}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group mt-3">
                                            <select style="width: 100%;height: 100%;" class="select2" name="styles_ids[]" multiple="multiple" data-placeholder="Какие стили выгружать?">
                                                @foreach($styles as $style)
                                                    <option value="{{$style->id}}" >{{$style->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-check mt-3">
                                            <input class="form-check-input" type="radio" name="sep" value="styles">
                                            <label class="form-check-label">
                                                 Разбивать вкладки по стилям
                                            </label>
                                        </div>
                                        <div class="form-check mt-3">
                                            <input class="form-check-input" type="radio" name="sep" value="users">
                                            <label class="form-check-label">
                                                Разбивать вкладки по пользователям
                                            </label>
                                        </div>
                                        <div class="form-check mt-3">
                                            <input class="form-check-input" type="radio" checked name="sep" value="none">
                                            <label class="form-check-label">
                                                Не разбивать на вкладки
                                            </label>
                                        </div>
                                        <button type="submit" class="btn btn-dark btn-ecomm add-advert-button mt-3">Выгрузить</button>
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
