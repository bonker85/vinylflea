@extends('layouts.main')
@section('title', 'Ошибка 404')
@section('content')
    <section class="profile-breadcrumbs ">
        <div class="container">
            <div class="page-breadcrumb pb-3 d-flex align-items-center">
                <h3 class="breadcrumb-title pe-3">Сессия истекла!</h3>
                <div class="ms-auto">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="/">Главная</a>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>
    <div class="error-404 pb-5 text-center container">
        <h2>419</h2>
    </div>
    @include('includes.video')
    @include('includes.popular-block')
    @include('includes.last-news-block')
@endsection
