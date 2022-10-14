@extends('layouts.main')
@section('title', (($advert->author) ? $advert->author . ' - ': '') . $advert->name)
@section('content')
    <section class="py-3 border-bottom border-top d-none d-md-flex bg-light">
        <div class="container">
            <div class="page-breadcrumb d-flex align-items-center">
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
    <section class="py-4">
        <div class="container">
            <div class="product-detail-card">
                <div class="product-detail-body">
                    <div class="row g-0">
                        <div class="col-12 col-lg-7 h3-mobile">
                            <div class="product-info-section p-3 py-0">
                                <h3 class=" mt-lg-0 mb-4">{{$advert->name}}</h3>
                            </div>
                        </div>
                        <div class="col-12 col-lg-5">
                            <div class="image-zoom-section">
                                <div class="product-gallery owl-carousel owl-theme border mb-3 p-3" data-slider-id="1">
                                    <div class="item">
                                        @if (count($advert->images))
                                            @foreach ($advert->images as $image)
                                                <a href="{{asset('/storage' . $image->path)}}" data-fancybox="gallery" data-caption="{{$advert->name}} (Изображение {{$loop->iteration}})">
                                                    <img src="{{asset('/storage' . $image->path)}}" class="img-fluid"   alt="{{$advert->name}}" loading="lazy">
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
                                                <a href="{{asset('/storage' . $image->path)}}" data-fancybox="gallery" data-caption="{{$advert->name}} (Изображение {{++$loop->iteration}})">
                                                    <img src="{{asset('/storage' . $image->path)}}" class="" alt="{{$advert->name}} (Изображение {{$loop->iteration}})"  loading="lazy">
                                                </a>
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-12 col-lg-7">
                            <div class="product-info-section py-0 p-3">
                                <h3 class="mt-lg-0 mb-0 h3-desktop">{{$advert->name}}</h3>
                                <div class="d-flex align-items-center mt-2 gap-2">
                                    <h4 class="mb-0">
                                        @if ($advert->deal == 'sale')
                                            {{str_replace('.00', '', $advert->price)}} Руб.
                                        @elseif ($advert->deal == 'exchange')
                                            обменяю
                                        @else
                                            отдам даром
                                        @endif
                                    </h4>
                                </div>
                                <div class="mt-3">
                                    <h6>Описание:</h6>
                                    <p class="mb-0">{{nl2br($advert->description)}}</p>
                                </div>
                                <dl class="row mt-3">
                                    <dt class="col-sm-3">Стиль</dt>
                                    <dd class="col-sm-9">{{$advert->style->name}}</dd>
                                    @if ($advert->author)
                                        <dt class="col-sm-3">Исполнитель</dt>
                                        <dd class="col-sm-9">{{$advert->author}}</dd>
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
                                        <dt class="col-sm-3 condition-block"><a href="{{route('news', 'sistemy-otsenki-vinilovyh-plastinok')}}" target="_blank">Оценка</a></dt>
                                        <dd class="col-sm-9">{{$advert->condition}}</dd>
                                    @endif
                                </dl>
                                <hr/>
                                <h5>Предложение от пользователя</h5>
                                <dl class="row mt-3">
                                    <dt class="col-sm-3">Имя</dt>
                                    <dd class="col-sm-9"><a href="{{route('user', $advert->user_id)}}" target="_blank">{{$advert->user->name}}</a></dd>
                                    @if ($advert->user->city)
                                        <dt class="col-sm-3">Город</dt>
                                        <dd class="col-sm-9">{{$advert->user->city}}</dd>
                                    @endif
                                    @if ($advert->user->phone && auth()->check())
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


                            </div>
                        </div>
                    </div>
                    <!--end row-->
                </div>
            </div>
        </div>
    </section>
@endsection
