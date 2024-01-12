@extends('layouts.main')
@section('description', 'Купить виниловые пластинки, пластинка: ' . (($advert->author) ? $advert->author . ' - ': '') . $advert->name)
@section('title', (($advert->author) ? $advert->author . ' - ': '') . $advert->name)
@section('content')
    <section class="profile-breadcrumbs ">
        <div class="container">
            <div class="page-breadcrumb pb-3 d-flex align-items-center">
                <h3 class="breadcrumb-title pe-3">{{(($advert->author) ? $advert->author . ' - ': '') . $advert->name}}</h3>
                <div class="ms-auto">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="/">Главная</a>
                            </li>
                            <li class="breadcrumb-item"><a href="{{route('vinyls.style', $advert->style->slug)}}">{{$advert->style->name}}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{$advert->name}}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>
    <!--end breadcrumb-->
    <!--start product detail-->
    <section class=" detail-section">
        <div class="container">
            <div class="product-detail-card">
                <div class="product-detail-body">
                    <div class="row g-0">
                        <div class="col-12 col-lg-7 h3-mobile">
                            <div class="product-info-section p-3 py-0">
                                <h3 class="mt-2 mb-2">
                                        <a href="@if(url()->previous() == url()->full())/@else{{url()->previous()}}@endif" class="backpage bx bxs-skip-previous-circle"></a>
                                    {{$advert->name}}
                                </h3>
                            </div>
                        </div>
                        <div class="col-12 col-lg-5">
                            <div class="image-zoom-section">
                                <div class="product-gallery owl-carousel owl-theme border mb-3 p-3" data-slider-id="1">
                                    <div class="item">
                                        @if (count($advert->images))
                                            @foreach ($advert->images as $image)
                                                <a href="{{cdn_url(asset('/storage' . $image->path), $image)}}" data-fancybox="gallery" data-caption="{{$advert->name}} (Изображение {{$loop->iteration}})">
                                                    <img src="{{cdn_url(asset('/storage' . $image->path), $image)}}" class="img-fluid"   alt="{{$advert->name}}" loading="lazy">
                                                </a>
                                                @break
                                            @endforeach
                                        @else
                                            <img src="{{asset('/assets/images/avatars/no-avatar.png')}}" class="img-fluid"   alt="{{$advert->name}}" loading="lazy">
                                        @endif
                                    </div>
                                </div>
                                @if (count($advert->images) > 1)
                                    <div class="owl-thumbs d-flex justify-content-center" data-slider-id="1">
                                        @php unset($advert->images[0]); @endphp
                                        @foreach ($advert->images as $image)
                                            <button class="owl-thumb-item">
                                                <a href="{{cdn_url(asset('/storage' . $image->path), $image)}}" data-fancybox="gallery" data-caption="{{$advert->name}} (Изображение {{++$loop->iteration}})">
                                                    <img src="{{cdn_url(asset('/storage' . $image->path), $image)}}" class="" alt="{{$advert->name}} (Изображение {{$loop->iteration}})"  loading="lazy">
                                                </a>
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-12 col-lg-7">
                            <div class="product-info-section py-0 p-3">
                                <h3 class="mb-0 mt-3 h3-desktop">
                                    @if ($advert->deal == 'news')
                                        {{($advert->author) ? $advert->author . ' - ': '') . $advert->name}}
                                    @else
                                        {{$advert->name}}
                                    @endif
                                </h3>
                                <div class="d-flex align-items-center mt-2 gap-2">
                                    <h4 class="mb-0">
                                        @if ($advert->user_id == 11 || $advert->user_id == 6)
                                            @if ($advert->deal == 'news')
                                                новость
                                            @endif
                                        @else
                                            @if ($advert->deal == 'sale')
                                                {{str_replace('.00', '', $advert->price)}} Руб.
                                            @elseif ($advert->deal == 'exchange')
                                                обменяю
                                            @elseif ($advert->deal == 'news')
                                                новость
                                            @else
                                                отдам даром
                                            @endif
                                        @endif
                                    </h4>
                                </div>
                                @if ($advert->description)
                                <div class="mt-3">
                                    <h6>Описание:</h6>
                                    <p class="mb-0">{!!$advert->description!!}</p>
                                </div>
                                @endif
                                <dl class="row mt-3">
                                    <dt class="col-sm-3">@if ($advert->deal == 'news')Раздел@elseСтиль@endif</dt>
                                    <dd class="col-sm-9">{{$advert->style->name}}</dd>
                                    @if ($advert->deal != 'news' && ($advert->author || $advert->discogs_author_ids))
                                        <dt class="col-sm-3">Исполнитель</dt>
                                        <dd class="col-sm-9">
                                            @if ($artistLinks)
                                                {!! $artistLinks !!}
                                            @else
                                                {{$advert->author}}
                                            @endif
                                        </dd>
                                    @endif
                                    @if ($advert->edition_id)
                                        <dt class="col-sm-3">Издание</dt>
                                        <dd class="col-sm-9">{{$advert->edition->name}}</dd>
                                    @endif
                                    @if ($advert->year)
                                        <dt class="col-sm-3">Год</dt>
                                        <dd class="col-sm-9">{{$advert->year}}</dd>
                                    @endif
                                    @if ($advert->state)
                                        <dt class="col-sm-3">Состояние</dt>
                                        <dd class="col-sm-9">{{\App\Services\AdvertService::STATES[$advert->state]}}</dd>
                                    @endif
                                    @if ($advert->condition)
                                        <dt class="col-sm-3 condition-block"><a href="{{route('news', 'sistemy-otsenki-vinilovyh-plastinok')}}">Оценка</a></dt>
                                        <dd class="col-sm-9">{{$advert->condition}}</dd>
                                    @endif
                                </dl>
                                <hr/>
                                <h5>Предложение от пользователя</h5>
                                <dl class="row mt-3">
                                    <dt class="col-sm-3">Имя</dt>
                                    <dd class="col-sm-9"><a href="{{route('user', $advert->user_id)}}" >{{$advert->user->name}}</a></dd>
                                    @if ($advert->user->city)
                                        <dt class="col-sm-3">Город</dt>
                                        <dd class="col-sm-9">{{$advert->user->city}}</dd>
                                    @endif
                                    @if (\App\Models\User::isMyUserId($advert->user_id) && false)
                                        <dt class="col-sm-3">Telegram</dt>
                                        <dd class="col-sm-9">
                                            <a class="telegram" href="https://t.me/vinylfleaby"><i class="bx fs-4 bxl-telegram"></i></a>
                                        </dd>
                                        <dt class="col-sm-3">Viber</dt>
                                        <dd class="col-sm-9">
                                            <a class="viber" href="viber://chat?number=375257167247">
                                                <img src="/images/viber.png" style="width: 20px;" />
                                            </a>
                                        </dd>
                                    @endif
                                    @if ($advert->user->phone /*&& auth()->check()*/)
                                        <dt class="col-sm-3">Телефон</dt>
                                        <dd class="col-sm-9">
                                            <div class="button-phone" data-advert="{{$advert->id}}"><i class="bx bxs-show"></i> Показать</div>
                                        </dd>
                                    @endif
                                </dl>
                                <div class="d-flex gap-2 mt-3">
                                    <div class="d-grid gap-2">
                                        @auth
                                            @if (auth()->user()->id != $advert->user_id)
                                                <a href="javascript:;" class="btn btn-dark btn-ecomm" data-bs-toggle="modal" data-bs-target="#message-modal" data-name="{{$advert->name}}" data-id="{{$advert->id}}" data-button="">	<i class='bx bx-message'></i>Отправить сообщение</a>
                                            @endif
                                        @elseguest
                                            <a href="{{route('login')}}" class="btn btn-dark btn-ecomm">	<i class='bx bx-message'></i>Отправить сообщение</a>
                                        @endauth
                                    </div>
                                </div>
                                @if ($relationAdverts)
                                    <hr/>
                                    <h4>Вам также может быть интересно</h4>
                                     @foreach ($relationAdverts as $advert)
                                        <dl class="row mt-3 releases-block">
                                            <dt class="col-sm-4 img-release">
                                                @if (count($advert->images))
                                                    @foreach ($advert->images as $image)
                                                        <a href="{{route('vinyls.details', $advert->url)}}">
                                                            <img src="{{cdn_url(asset('/storage' . $image->path), $image)}}" class="img-fluid"   alt="{{$advert->name}}" loading="lazy">
                                                        </a>
                                                        @break
                                                    @endforeach
                                                @else
                                                    <img src="{{asset('/assets/images/avatars/no-avatar.png')}}" class="img-fluid"   alt="{{$advert->name}}" loading="lazy">
                                                @endif
                                            </dt>
                                            <dd class="col-sm-8">
                                                <dl class="row">
                                                    <dt class="col-sm-3">
                                                        Название
                                                    </dt>
                                                    <dd class="col-sm-9">
                                                        {{$advert->name}}
                                                    </dd>
                                                    <dt class="col-sm-3">
                                                        Исполнитель
                                                    </dt>
                                                    <dd class="col-sm-9">
                                                        @php $artistLinks = \App\Services\Utility\DiscogsService::getArtistsLink($advert->discogs_author_ids);@endphp
                                                        @if ($artistLinks)
                                                            {!! $artistLinks !!}
                                                        @else
                                                            {{$advert->author}}
                                                        @endif
                                                    </dd>
                                                    <dt class="col-sm-3">
                                                        Стиль
                                                    </dt>
                                                    <dd class="col-sm-9">
                                                        {{$advert->style->name}}
                                                    </dd>
                                                    <dt class="col-sm-3">
                                                        Цена
                                                    </dt>
                                                    <dd class="col-sm-9">
                                                        @if ($advert->user_id == 11 || $advert->user_id == 6)
                                                            @if ($advert->deal == 'news')
                                                                новость
                                                            @endif
                                                        @else
                                                            @if ($advert->deal == 'sale')
                                                                {{str_replace('.00', '', $advert->price)}} Руб.
                                                            @elseif ($advert->deal == 'exchange')
                                                                обменяю
                                                            @elseif ($advert->deal = 'news')
                                                                новость
                                                            @else
                                                                отдам даром
                                                            @endif
                                                        @endif
                                                    </dd>
                                                    <dt class="col-sm-3">
                                                        Предлагает
                                                    </dt>
                                                    <dd class="col-sm-9">
                                                        <a href="{{route('user', $advert->user_id)}}" >{{$advert->user->name}}</a>
                                                    </dd>
                                                </dl>
                                                <dl>
                                                    <a href="{{route('vinyls.details', $advert->url)}}"  class="btn btn-dark btn-ecomm"><i class="bx bx-show"></i>Смотреть пластинку</a>
                                                </dl>
                                            </dd>
                                            <hr>
                                        </dl>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                    <!--end row-->
                </div>
            </div>
        </div>
    </section>
@endsection
