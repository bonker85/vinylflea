@extends('layouts.main')
@section('description', $artist->name . ', Виниловые пластинки, купить пластинки, Вся Беларусь, город ' .
['Минск', 'Витебск', 'Гродно', 'Могилев', 'Гомель'][rand(0,4)])
@section('title', 'Исполнитель ' . $artist->name )
@section('content')
    <section class="profile-breadcrumbs ">
        <div class="container">
            <div class="page-breadcrumb pb-3 d-flex align-items-center">
                <h3 class="breadcrumb-title pe-3">{{'Исполнитель ' . $artist->name}} </h3>
                <div class="ms-auto">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="/">Главная</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{'Исполнитель ' . $artist->name}}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>
    <!--end breadcrumb-->
    <section class="author-section">
        <div class="container">
            <div class="product-detail-card">
                <div class="product-detail-body">
                    <div class="row g-0">
                        <div class="col-12 col-lg-7 h3-mobile">
                            <div class="product-info-section p-3 py-0">
                                <h3 class=" mt-2 mb-2">
                                        <a href="@if(url()->previous() == url()->full())/@else{{url()->previous()}}@endif" class="backpage bx bxs-skip-previous-circle"></a>
                                    {{$artist->name}}
                                </h3>
                            </div>
                        </div>
                        <div class="col-12 col-lg-5">
                            <div class="image-zoom-section">
                                <div class="product-gallery owl-carousel owl-theme border mb-3 p-3" data-slider-id="1">
                                    <div class="item">
                                        @if ($artist->cdn_count_images)
                                                <a href="{{env('CDN_HOST') . '/discogs/artist/' . $artist->id . '/artist1.jpeg'}}" data-fancybox="gallery" data-caption="{{$artist->name}} (Изображение 1)">
                                                    <img src="{{env('CDN_HOST') . '/discogs/artist/' . $artist->id . '/artist1.jpeg'}}" class="img-fluid"   alt="{{$artist->name}}" loading="lazy">
                                                </a>
                                        @else
                                            <img src="{{asset('/assets/images/avatars/no-avatar.png')}}" class="img-fluid"   alt="{{$artist->name}}" loading="lazy">
                                        @endif
                                    </div>
                                </div>
                                @if ($artist->cdn_count_images > 1)
                                    <div class="owl-thumbs d-flex justify-content-center" data-slider-id="1">
                                        @for($i=2; $i<=$artist->cdn_count_images; $i++)
                                            <button class="owl-thumb-item">
                                                <a href="{{env('CDN_HOST') . '/discogs/artist/' . $artist->id . '/artist' . $i . '.jpeg'}}" data-fancybox="gallery" data-caption="{{$artist->name}} (Изображение {{$i}})">
                                                    <img src="{{env('CDN_HOST') . '/discogs/artist/' . $artist->id . '/artist' . $i . '.jpeg'}}" class="" alt="{{$artist->name}} (Изображение {{$i}})"  loading="lazy">
                                                </a>
                                            </button>
                                        @endfor
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-12 col-lg-7">
                            <div class="product-info-section py-0 p-3">
                                <h3 class="mb-0 mt-3 h3-desktop">{{$artist->name}}</h3>
                                @if ($artist->profile)
                                    <div class="mt-3">
                                        <h6>Описание:</h6>
                                        @if ($artist->profile_translate)
                                            <p class="mb-0">{!! $artist->profile_translate !!}</p>
                                            <hr/>
                                        @endif
                                        <p class="mb-0">{!!$artist->profile!!}</p>
                                        @if (auth()->check() && in_array(auth()->user()->id, \App\Models\User::MY_USERS_IDS))
                                            <form style="border: 1px solid #DEE2E6; padding: 10px;background-color: #F8F9FA;" method="post" action="{{route('artist.edit', $artist->discogs_artist_id)}}" >
                                                @csrf
                                                <textarea style="margin: 10px 0" class="form-control"  name="profile">{{$artist->profile}}</textarea>
                                                <button type="submit" class="btn btn-warning">Изменить</button>
                                            </form>
                                        @endif
                                    </div>
                                @endif
                                @if ($artist->realname)
                                <dl class="row mt-3">
                                    <dt class="col-sm-3">Настоящее имя</dt>
                                    <dd class="col-sm-9">{{$artist->realname}}</dd>
                                </dl>
                                @endif
                                @if ($artist->namevariations)
                                    <dl class="row mt-3">
                                        <dt class="col-sm-3">Вариации:</dt>
                                        <dd class="col-sm-9">{{implode(', ', json_decode($artist->namevariations))}}</dd>
                                    </dl>
                                @endif
                                @if ($adverts->count())
                                    <hr/>
                                    <h4>Пластинки на сайте:</h4>
                                    @foreach ($adverts as $advert)
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
                                                        <a href="{{route('vinyls.details', $advert->url)}}">{{$advert->name}}</a>
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
                                                            @elseif ($advert->deal == 'news')
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
                                            @unless($loop->last)
                                                <hr>
                                            @endunless
                                        </dl>
                                    @endforeach
                                @endif
                                @if ($releases)
                                    <hr/>
                                    <h4>Релизы (Vinyl):</h4>
                                    @include('includes.releases', ['releases' => $releases])
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
