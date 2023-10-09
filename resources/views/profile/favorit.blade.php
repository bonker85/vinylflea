@extends('layouts.main')
@section('title', 'Мои Пластинки')
@section('content')
    <section class="profile-breadcrumbs">
        <div class="container">
            <div class="page-breadcrumb pb-3 d-flex align-items-center">
                <h3 class="breadcrumb-title pe-3">Избранные пластинки</h3>
                <div class="ms-auto">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="/">Главная</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Избранные</li>
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
                                    @if ($favoritLists->count())
                                        <div class="col-12">
                                            <div class="shop-cart-list favorit-blocks mb-3 p-3">
                                                @foreach($favoritLists as $favorit)
                                                    <a href="{{route('vinyls.details', $favorit->advert->url)}}" target="_blank">
                                                        <div class="row align-items-center g-3">
                                                            <div class="col-12">
                                                                <div class="d-lg-flex align-items-center gap-3 block-vinyl-list">
                                                                    <div class="cart-img text-center text-lg-start">
                                                                        @if (count($favorit->advert->images))
                                                                            @foreach ($favorit->advert->images as $image)
                                                                                <img src="{{thumb_url(asset('/storage' . $image->path), $image)}}" width="130" alt="">
                                                                                @break
                                                                            @endforeach
                                                                        @else
                                                                            <img src="{{asset('/assets/images/avatars/no-avatar.png')}}" width="130" alt="">
                                                                        @endif
                                                                    </div>
                                                                    <div class="cart-detail col-lg-9 text-center text-lg-start">
                                                                        <h6 class="mb-0">{{$favorit->advert->name}}</h6>
                                                                        <div class="m-style">{{$favorit->advert->style->name}}</div>
                                                                        <h5 class="mb-0">
                                                                            @if ($favorit->advert->deal == 'sale')
                                                                                {{str_replace('.00', '', $favorit->advert->price)}} Руб.
                                                                            @elseif ($favorit->advert->deal == 'exchange')
                                                                                обменяю
                                                                            @else
                                                                                отдам даром
                                                                            @endif
                                                                        </h5>
                                                                        <form method="post" action="{{route('profile.favorit.delete', $favorit->id)}}">
                                                                            @csrf
                                                                            @method('delete')
                                                                            <button type="submit" class="btn mt-5 btn-sm btn-warning">Удалить из избранного</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </a>
                                                    @if (!$loop->last)
                                                        <div class="my-4 border-top"></div>
                                                    @endif
                                                @endforeach
                                                @if ($favoritLists->total() > $favoritLists->perPage())
                                                    <div class="my-4 border-top"></div>
                                                    <div class="d-flex justify-content-between">
                                                        {{ $favoritLists->onEachSide(1)->links()}}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <div class="col-12">
                                            <div class="shop-cart-list favorit-blocks mb-3 p-3">
                                                 <p>У Вас пока нет избранных пластинок</p>
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
