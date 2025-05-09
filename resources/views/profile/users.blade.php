@extends('layouts.main')
@section('title', 'Мои Пластинки')
@section('content')
    <section class="profile-breadcrumbs">
        <div class="container">
            <div class="page-breadcrumb pb-3 d-flex align-items-center">
                <h3 class="breadcrumb-title pe-3">Пользователи</h3>
                <div class="ms-auto">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="/">Главная</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Пользователи</li>
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
                            <div class="adverts-block card shadow-none mb-0">
                                <div class="card-body">
                                    @if ($usersList->count())
                                        <div class="col-12">
                                            <div class="shop-cart-list favorit-blocks mb-3 p-3">
                                                @foreach($usersList as $user)
                                                        <div class="row align-items-center g-3">
                                                            <div class="col-12">
                                                                <div class="d-lg-flex align-items-center gap-3 block-vinyl-list">
                                                                    <a href="{{route('user', $user->id)}}" target="_blank">
                                                                        <div class="cart-img text-center text-lg-start">
                                                                            @if ($user->avatar)
                                                                                <img src="{{cdn_url(asset('/storage' . $user->avatar), $user)}}" width="130" alt="">
                                                                            @else
                                                                                <img src="{{asset('/assets/images/avatars/no-avatar.png')}}" width="130" alt="">
                                                                            @endif
                                                                        </div>
                                                                    </a>
                                                                    <div class="cart-detail h-25 col-lg-9 text-center text-lg-start">
                                                                        <h6 class="mb-0">{{$user->name}}</h6>
                                                                        <div class="m-style">{{$user->email}}</div>
                                                                        @if ($user->phone)
                                                                            <div class="m-style">{{$user->phone}}</div>
                                                                        @endif
                                                                        @if ($user->city)
                                                                            <div class="m-style">{{$user->city}}</div>
                                                                        @endif
                                                                        <form method="post" action="{{route('profile.users')}}">
                                                                            <input type="hidden" name="user_id" value="{{$user->id}}" />
                                                                            @csrf
                                                                            @if ($user->isBan())
                                                                                <input type="hidden" name="action" value="remove_ban" />
                                                                                <button type="submit" class="btn mt-2 btn-sm btn-warning">Удалить из бан листа</button>
                                                                            @else
                                                                                <input type="hidden" name="action" value="add_to_ban" />
                                                                                <button type="submit" class="btn mt-2 btn-sm btn-danger">Добавить в бан лист</button>
                                                                            @endif
                                                                        </form>
                                                                        <form method="post" action="{{route('profile.users')}}">
                                                                            <input type="hidden" name="user_id" value="{{$user->id}}" />
                                                                            @csrf
                                                                            @php $dialog = $user->isDialog($user->id); @endphp
                                                                            @if ($dialog)
                                                                                <a href="{{route('profile.messages', $dialog->id)}}" class="btn mt-2 btn-sm btn-warning" target="_blank">Перейти в диалог</a>
                                                                            @else
                                                                                <input type="hidden" name="action" value="add_dialog" />
                                                                                <button type="submit" class="btn mt-2 btn-sm btn-success">Создать диалог</button>
                                                                            @endif
                                                                        </form>
                                                                        <form method="post" action="{{route('tasks', ['param' => 'toggle_user'])}}">
                                                                            @csrf
                                                                            <input type="hidden" name="id" value="{{$user->id}}" />
                                                                            <button type="submit" class="btn mt-2 btn-sm btn-primary">
                                                                                Перейти в аккаунт
                                                                            </button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @if (!$loop->last)
                                                        <div class="my-4 border-top"></div>
                                                    @endif
                                                @endforeach
                                                @if ($usersList->total() > $usersList->perPage())
                                                    <div class="my-4 border-top"></div>
                                                    <div class="d-flex justify-content-between">
                                                        {{ $usersList->onEachSide(1)->links()}}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
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
