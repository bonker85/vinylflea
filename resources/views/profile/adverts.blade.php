@extends('layouts.main')
@section('title', 'Мои Пластинки')
@section('content')
    <section class="py-3 border-bottom border-top d-none d-md-flex bg-light profile-breadcrumbs">
        <div class="container">
            <div class="page-breadcrumb d-flex align-items-center">
                <h3 class="breadcrumb-title pe-3">Мои пластинки</h3>
                <div class="ms-auto">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="/">Главная</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Мои пластинки</li>
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
                                    <div class="col-12 table-responsive">
                                        <table class="text-nowrap adverts-menu">
                                            <tbody>
                                            <tr>
                                                <td><a href="{{route('profile.adverts', ['status' => 'activated'])}}" @if ($status === 'activated')class="active" @endif>Активные ({{$advert_counts['activated']}})</a></td>
                                                <td><a id="ad-mod" href="{{route('profile.adverts', ['status' => 'moderation'])}}" @if ($status === 'moderation')class="active" @endif>На модерации ({{$advert_counts['moderation']}})</a></td>
                                                <td><a id="ad-rejected" href="{{route('profile.adverts', ['status' => 'rejected'])}}" @if ($status === 'rejected')class="active" @endif>Отклоненные ({{$advert_counts['rejected']}})</a></td>
                                                <td><a id="ad-deact" href="{{route('profile.adverts', ['status' => 'deactivated'])}}" @if ($status === 'deactivated')class="active" @endif>Неактивные ({{$advert_counts['deactivated']}})</a></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    @if ($advertList->count())
                                    <div class="ui search focus u-search" style="">
                                        <div class=" input-group flex-nowrap  search-box">
                                            <div class="ui left icon input">
                                                <i class="bx bx-search icon"></i>
                                                <input type="hidden" name="user" value="{{auth()->user()->id}}" id="s-user">
                                                <input type="hidden" name="status" value="{{$status}}" id="s-status"/>
                                                <input type="text" class="form-control w-100 prompt" @if(request()->uq)value="{{request()->uq}}"@endif placeholder="Поиск пластинки" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="results"></div>
                                    </div>
                                    <div class="col-12">
                                        <div class="shop-cart-list mb-3 p-3">
                                            @foreach($advertList as $advert)
                                            <div class="row align-items-center g-3">
                                                <div class="col-12">
                                                    @if ($status == 'rejected' && $advert->reject_message)
                                                        <div class="reject-message">{{$advert->reject_message}}</div>
                                                    @endif
                                                    <div class="d-lg-flex align-items-center gap-3 block-vinyl-list">
                                                        <div class="cart-img text-center text-lg-start">
                                                            @if ($status == 'activated')
                                                                <a href="{{route('vinyls.details', $advert->url)}}" target="_blank">
                                                            @endif
                                                            @if (count($advert->images))
                                                                @foreach ($advert->images as $image)
                                                                    <img src="{{thumb_url(asset('/storage' ). $image->path, $image)}}" width="130" alt="">
                                                                    @break
                                                                @endforeach
                                                            @else
                                                                <img src="{{asset('/assets/images/avatars/no-avatar.png')}}" width="130" alt="">
                                                            @endif
                                                                @if ($status == 'activated')
                                                                    </a>
                                                                @endif
                                                        </div>
                                                        <div class="cart-detail col-lg-9 text-center text-lg-start">
                                                            <h6 class="mb-0">{{($advert->author ? $advert->author . ' | ' : '')}}{{$advert->name}}</h6>
                                                            <div class="m-style">{{$advert->style->name}}</div>
                                                            @if ($advert->favorits->count() && !$admin && $status == 'activated')
                                                                <div>
                                                                    <i class="bx bxs-heart" ></i>
                                                                    <div class="advert-favorits">{{$advert->favorits->count()}}</div>
                                                                </div>
                                                            @endif
                                                            <h5 class="mb-0">
                                                                @if ($advert->deal == 'sale')
                                                                    {{str_replace('.00', '', $advert->price)}} Руб.
                                                                @elseif ($advert->deal == 'exchange')
                                                                    обменяю
                                                                @else
                                                                    отдам даром
                                                                @endif
                                                            </h5>
                                                            @if ($admin)
                                                                <div class="user-link-profile">
                                                                    <form method="post" action="{{route('tasks', ['param' => 'toggle_user'])}}">
                                                                        @csrf
                                                                        <input type="hidden" name="id" value="{{$advert->user_id}}" />
                                                                        <button type="submit">
                                                                            @if ($advert->user->avatar)
                                                                                <img src="{{cdn_url(asset('/storage') . $advert->user->avatar, $advert->user)}}"/>
                                                                            @else
                                                                                <img src="{{asset('/assets/images/avatars/no-avatar.png')}}" />
                                                                            @endif
                                                                                {{$advert->user->name}}
                                                                        </button>
                                                                  </form>
                                                                </div>
                                                            @endif

                                                            @if ($status != 'moderation' || $admin)
                                                            <div class="text-center block-cart-button">
                                                                <div class="d-flex gap-2 justify-content-center justify-content-lg-end mt-1">
                                                                    @if ($status == 'rejected' || $status == 'activated' || $admin)
                                                                        <a href="{{route('profile.edit_advert', $advert->id)}}" class="btn btn-success rounded-3 btn-ecomm"><i class="bx bx-edit"></i> Изменить</a>
                                                                    @endif
                                                                    @if ($status == 'activated')
                                                                            <form method="post" action="{{route('profile.deactiv_advert', ['id' => $advert->id])}}">
                                                                                @csrf
                                                                                <button type="submit" class="btn btn-warning rounded-3 btn-ecomm"><i class="bx bx-hide"></i> Скрыть</button>
                                                                            </form>
                                                                    @endif
                                                                    @if ($status == 'deactivated')
                                                                        <form method="post" action="{{route('profile.activ_advert', ['id' => $advert->id])}}">
                                                                            @csrf
                                                                            <button type="submit" class="btn btn-warning rounded-3 btn-ecomm"><i class="bx bx-show"></i> Показать</button>
                                                                        </form>
                                                                    @endif
                                                                    @if ($status == 'rejected' || $status == 'deactivated' || $admin)
                                                                        <form method="post" action="{{route('profile.del_advert', $advert->id)}}">
                                                                            @csrf
                                                                            <button type="submit" class="btn btn-danger rounded-3 btn-ecomm"><i class="bx bx-x-circle"></i> Удалить</button>
                                                                        </form>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            @endif
                                                        </div>
                                                        <div class="vinyl-up-date">
                                                            @if ($advert->up_time)
                                                                {{$advert->getFormatDate()}}
                                                            @endif
                                                                @if ($status == 'activated' && ($advert->isUpTime() || $admin))
                                                                    <div class="up-button">
                                                                        <form method="post" action="{{route('profile.up_advert', ['id' => $advert->id])}}">
                                                                            @csrf
                                                                            <button type="submit" class="btn-warning rounded-3 btn-ecomm"><i class="bx bxs-upvote"></i> Поднять</button>
                                                                        </form>
                                                                    </div>
                                                                @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @if (!$loop->last)
                                                <div class="my-4 border-top"></div>
                                            @endif
                                            @endforeach

                                        </div>
                                        @if ($advertList->total() > $advertList->perPage())
                                            <div class="my-4 border-top"></div>
                                            <div class="d-flex justify-content-between">
                                                {{ $advertList->onEachSide(1)->links()}}
                                            </div>
                                        @endif
                                    </div>
                                    @else
                                        <div class="col-12">
                                            <div class="shop-cart-list favorit-blocks mb-3 p-3">
                                                @if ($search)
                                                    Нет такой пластинки
                                                @else
                                                    <p>
                                                        У Вас пока нет
                                                        @switch ($status)
                                                            @case ('activated')
                                                                активных пластинок
                                                                @break
                                                            @case ('moderation')
                                                                пластинок на модерации
                                                                @break
                                                            @case ('rejected')
                                                                отклоненных пластинок
                                                                @break
                                                            @case ('deactivated')
                                                                неактивных пластинок
                                                                @break
                                                        @endswitch
                                                    </p>
                                                @endif
                                                @if ($status == 'activated')
                                                    <div class="add-vinyl">
                                                        <a href="{{route('profile.add_advert')}}" class="rounded-3 list-group-item  d-flex justify-content-between align-items-center"><i class="bx bx-plus fs-5"></i> Добавить</a>
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
