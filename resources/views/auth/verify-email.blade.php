@extends('layouts.main')
@section('title', 'Подтверждение email')
@section('content')
    <section class="profile-breadcrumbs ">
        <div class="container">
            <div class="pb-3 page-breadcrumb d-flex align-items-center">
                <h3 class="breadcrumb-title pe-3">Подтверждение Email</h3>
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
    <section class="py-3 py-lg-4">
        <div class="container">
            <p>
                Для входа в личный кабинет подтвердите Ваш Email. <br/>
                Ссылка подтверждения выслана на адрес: <b>{{auth()->user()->email}}</b>. <br/>
                Если по какой-то причине, письмо не пришло, проверьте папку "Спам" или воспользуйтесь кнопкой ниже для повторной отправки запроса.<br/>
                <form method="post" action="{{route('verification.send')}}">
                    @csrf
                    <button class="btn btn-dark" type="submit">Отправить повторно</button>
                </form>
            </p>
        </div>
    </section>
@endsection
