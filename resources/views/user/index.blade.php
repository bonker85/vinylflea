@extends('layouts.main')
@section('title', 'Все пластинки пользователя ' . $user->name)
@section('content')
    <section class="py-3 border-bottom border-top d-none d-md-flex bg-light profile-breadcrumbs">
        <div class="container">
            <div class="page-breadcrumb d-flex align-items-center">
                <h3 class="breadcrumb-title pe-3">Все пластинки {{$user->name}}</h3>
                <div class="ms-auto">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="/">Главная</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Все пластинки {{$user->name}}</li>
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
                        <div class="col-lg-4">
                            <div class="card shadow-none mb-3 mb-lg-0 border rounded-0">
                                <div class="card-body">
                                    <div class="list-group list-group-flush ">
                                        <div class="user-data">
                                            <div class="user-img">
                                                @if ($user->avatar)
                                                    <img src="{{cdn_url(asset('/storage') . $user->avatar, $user)}}" loading="lazy" alt="{{$user->name}}"/>
                                                @else
                                                    <img src="{{asset('/assets/images/avatars/no-avatar.png')}}" loading="lazy" alt="{{$user->name}}"/>
                                                @endif
                                            </div>
                                            <div class="user-info">
                                                <div class="user-name"><b>Имя:</b> {{$user->name}}</div>
                                                @if ($user->city)
                                                    <div class="user-city"><b>Город:</b> {{$user->city}}</div>
                                                @endif
                                                @if (\App\Models\User::isMyUserId($user->id))
                                                    <div class="user-city"><a class="telegram" href="https://t.me/vinylfleaby"><i class="bx fs-3 bxl-telegram"></i></a></div>
                                                @endif
                                                @if ($user->phone)
                                                    <div class="user-phone"><a class="btn btn-success btn-sm button-phone" data-user="{{$user->id}}">Показать телефон</a></div>
                                                 @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @php $currentStyle = 'Все стили'; @endphp
                        <div class="col-lg-8">
                            @if ($advertList->total() > $advertList->perPage() || request()->route('style_id') || request()->uq)
                                <div class="ui search focus u-search" style="">
                                    <div class=" input-group flex-nowrap  search-box">
                                        <div class="ui left icon input">
                                            <i class="bx bx-search icon"></i>
                                            <input type="hidden" name="user" value="{{$user->id}}" id="s-user"/>
                                            <input type="text" class="form-control w-100 prompt" @if(request()->uq)value="{{request()->uq}}"@endif placeholder="Поиск пластинки у пользователя {{$user->name}}" autocomplete="off">
                                            <select class="search-style form-select flex-shrink-0" aria-label="Default select example" >
                                                <option value="{{route("user", $user->id)}}">Все стили</option>
                                                @foreach ($styles as $style)
                                                    @php
                                                        if (\Illuminate\Support\Facades\Request::route('style_id') && \Illuminate\Support\Facades\Request::route('style_id') == $style->id) {
                                                            $currentStyle = $style->name;
                                                        }
                                                    @endphp
                                                    <option value="{{route('user', ['user' => $user->id, 'style_id' => $style->id])}}" data-id="{{$style->id}}" @if (request()->route('style_id') && request()->route('style_id') == $style->id) selected @endif>{{$style->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="results"></div>
                                </div>
                            @endif
                            <div class="adverts-block card shadow-none mb-0">
                                <div class="card-body px-0 py-4">
                                    @if ($advertList->count())
                                        <div class="col-12">
                                            <div class="shop-cart-list mb-3 p-3">
                                                @if ($advertList->total() > $advertList->perPage() || request()->route('style_id') || request()->uq)
                                                    <div class="search-info">В разделе <b>{{$currentStyle}}</b>@if(request()->uq) по запросу <b>"{{request()->uq}}"</b>@endif {{num_word($advertList->total(), ["найдена", "найдено", "найдено"], false)}} <b>{!! $advertList->total() . '</b> ' . num_word($advertList->total(), ["пластинка", "пластинки", "пластинок"], false)!!} </div>
                                                @endif
                                                @foreach($advertList as $advert)
                                                    <div class="row align-items-center g-3">
                                                        <div class="col-12">
                                                            <div class="d-lg-flex align-items-center gap-2 block-vinyl-list">

                                                                <a href="{{route('vinyls.details', $advert->url)}}" >
                                                                    <div class="cart-img text-center text-lg-start">
                                                                        @if (count($advert->images))
                                                                            @foreach ($advert->images as $image)
                                                                                <img src="{{thumb_url(asset('/storage' . $image->path), $image)}}" loading="lazy" width="130" alt="">
                                                                                @break
                                                                            @endforeach
                                                                        @else
                                                                            <img src="{{asset('/assets/images/avatars/no-avatar.png')}}" loading="lazy" width="130" alt="">
                                                                        @endif
                                                                    </div>
                                                                </a>
                                                                <div class="cart-detail col-lg-9 text-center text-lg-start">
                                                                    <h6 class="mb-0"><a href="{{route('vinyls.details', $advert->url)}}" class="user-links" >{{$advert->name}}</a></h6>
                                                                    @if ($advert->author)
                                                                        <div class="m-style"><b>{{$advert->author}}</b></div>
                                                                    @endif
                                                                    <div class="m-style"><a href="{{route('vinyls.style', $advert->style->slug)}}" class="user-links" >{{$advert->style->name}}</a></div>
                                                                    <h5 class="mb-0">
                                                                        @if ($advert->deal == 'sale')
                                                                            {{str_replace('.00', '', $advert->price)}} Руб.
                                                                        @elseif ($advert->deal == 'exchange')
                                                                            обменяю
                                                                        @else
                                                                            отдам даром
                                                                        @endif
                                                                    </h5>
                                                                    <div class="d-flex gap-2 mt-2 user-button">
                                                                        <div class="d-grid gap-2">
                                                                            @if (auth()->check() && auth()->user()->id == $advert->user_id)
                                                                                <a href="{{route('vinyls.details', $advert->url)}}" class="user-mess-button btn btn-dark btn-ecomm" ><i class="bx bxs-show"></i>Просмотр пластинки</a>
                                                                            @else
                                                                                <a @if (auth()->check()) href="javascript:;" data-bs-toggle="modal" data-bs-target="#message-modal" data-name="{{$advert->name}}" data-id="{{$advert->id}}" data-button="" @else href="{{route('login')}}" @endif" class="user-mess-button btn btn-dark btn-ecomm" ><i class="bx bx-message"></i>Отправить сообщение</a>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @if (!$loop->last)
                                                        <div class="my-4 border-top"></div>
                                                    @endif
                                                @endforeach

                                            </div>
                                        </div>
                                        @if ($advertList->total() > $advertList->perPage())
                                            <div class="my-4 border-top"></div>
                                            <div class="d-flex justify-content-between">
                                                {{ $advertList->onEachSide(1)->appends(['uq' => request()->get('uq')])->links()}}
                                            </div>
                                        @endif
                                    @else
                                        <div class="col-12">
                                                <div class="search-info">В разделе <b>{{$currentStyle}}</b>@if(request()->uq) по запросу <b>"{{request()->uq}}"</b>@endif @if($advertList->total()){{num_word($advertList->total(), ["найдена", "найдено", "найдено"], false)}} <b>{!! $advertList->total() . '</b> ' . num_word($advertList->total(), ["пластинка", "пластинки", "пластинок"], false)!!}@else ничего не найдено@endif</div>
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
