@extends('layouts.main')
@section('title', 'Список Ay')
@section('description', 'Список ay')
@section('content')
    <div class="table-responsive px-2 py-4">
        <div class="mb-3 fs-5 text-center">
            <a href="{{route('download-list')}}" style="color: #dd4433;text-decoration: underline;">СКАЧАТЬ СПИСОК В EXCELE</a></div>
        @if ($adverts instanceof \Illuminate\Pagination\AbstractPaginator &&
         $adverts->total() > $adverts->perPage())
            <div class="my-4 border-top"></div>
            <div class="d-flex justify-content-between">
                @if (request()->query())
                    {{$adverts->appends(request()->query())->onEachSide(1)->links()}}
                @else
                    {{$adverts->links()}}
                @endif
            </div>
        @endif
        <div class="card">
            <div class="d-flex">
                <form method="get" action="/tasks/parser-ay">
                    <input type="hidden" name="limit" value="1"/>
                    <div class="card-body row pb-0">
                        <div class="col-12">
                            <button type="submit" class="btn btn-warning">Обновить</button>
                        </div>
                    </div>
                </form>
                <form method="get" action="/tasks/parser-ay">
                    <div class="card-body row pb-0">
                        <div class="col-12 d-flex justify-content-center align-items-center">
                            <button type="submit" class="btn btn-danger">Обновить Все</button>
                            <div class="fs-5 px-3">Всего пластинок: <b>{{$adverts->total()}}</b></div>
                        </div>
                    </div>
                </form>
            </div>

            <form method="get" action="{{route('main.ay-list')}}">
                <div class="card-body row">
                    <div class="col-2">
                        <input type="text" value="{{request()->title}}" name="title" class="form-control" placeholder="Название"/>
                    </div>
                    <div class="col-2">
                        <input type="text" value="{{request()->author}}" name="author" class="px-2 form-control" placeholder="Пользователь" />
                    </div>
                    <div class="col-5 d-flex align-items-center ">
                        @foreach (\App\Models\AyBy::TYPES as $typeId => $typeName)
                            <div class="form-check fs-5">
                                <input type="checkbox" name="typeId[]" @if (is_array(request()->typeId) && in_array($typeId, request()->typeId)) checked @endif class="form-check-input" value="{{$typeId}}" />
                                <lablel class="form-check-label me-3">{{$typeName}}</lablel>
                            </div>
                        @endforeach
                    </div>
                    <div class="col-3">
                        <button type="submit" class="btn btn-success">Искать</button>
                    </div>
                </div>
                <div class="card-body row">
                    <div class="col-2">
                        <input type="text" class="form-control"  value="{{request()->priceMin}}" name="priceMin" placeholder="Цена от" />
                    </div>
                    <div class="col-2">
                        <input class="form-control" value="{{request()->priceMax}}"  type="text" name="priceMax" placeholder="Цена до"/>
                    </div>
                    <div class="col-6 d-flex">
                        <div class="form-check fs-5">
                            <input type="checkbox" name="with_hide" @if (request()->with_hide)) checked @endif class="form-check-input" value="1" />
                            <lablel class="form-check-label me-3">Показывать скрытые</lablel>
                        </div>
                        <div class="form-check fs-5">
                            <input type="checkbox" name="updated_price" @if (request()->updated_price)) checked @endif class="form-check-input" value="1" />
                            <lablel class="form-check-label me-3">Показывать измененные цены</lablel>
                        </div>
                    </div>
                </div>
            </form>

        </div>
        <form method="post" action="{{route('main.ay-list')}}">
            @method('post')
        <table class="table table-hover table-bordered">
            <thead class="table-light">
            <tr>
                <th style="width: 100px">
                    <button type="submit"  class="btn btn-warning">Скрыть отмеченные</button>
                </th>
                <th>
                    <a href="" class="align-middle d-flex justify-content-between align-items-center">
                        <div>
                            Название
                        </div>
                        <i class="bx bx-sort-down fs-5"></i>
                    </a>
                </th>
                <th scope="col" class="align-middle">Скрыть</th>
                <th scope="col" class="align-middle">Просмотрен</th>
                <th scope="col">
                    <a href="" class="align-middle d-flex justify-content-between align-items-center">
                        Стиль <i class="bx bx-sort-down fs-5"></i>
                    </a>
                </th>
                <th scope="col">
                    <a href="" class="align-middle d-flex justify-content-between align-items-center">
                        Цена <i class="bx bx-sort-down fs-5"></i>
                    </a>
                </th>
                <th scope="col">
                    <a href="" class="align-middle d-flex justify-content-between align-items-center">
                        Цена (Торг) <i class="bx bx-sort-down fs-5"></i>
                    </a>
                </th>
                <th scope="col">
                    <a href="" class="text-nowrap align-middle d-flex justify-content-between align-items-center">
                        Дата обновления <i class="bx bx-sort-down fs-5"></i>
                    </a>
                </th>
                <th scope="col">
                    <a href="" class="text-nowrap align-middle d-flex justify-content-between align-items-center">
                        Продавец <i class="bx bx-sort-down fs-5"></i>
                    </a>
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach ($adverts as $advert)
                <tr class="@if (!$advert->view) not-view @endif">
                    <td class="text-center @if ($advert->auction)auction @endif">
                        <a href="{{$advert->link}}" target="_blank" class="vinyl-link-list">
                            <img src="{{asset('/storage/ay/' . $advert->ay_id . '.' . $advert->img_ext )}}" width="100" alt="">
                        </a>
                    </td>
                    <td class="align-middle text-nowrap">
                        <a href="{{$advert->link}}" target="_blank" class="vinyl-link-truncate">{{$advert->title}}</a>
                    </td>
                    <td class="align-middle">
                        <div class="form-check fs-5 d-flex justify-content-center">
                            <input type="checkbox" name="hide[]" @if ($advert->hide) checked @endif class="form-check-input" value="{{$advert->id}}" />
                        </div>
                    </td>
                    <td class="align-middle">
                        <div class="form-check fs-5 d-flex justify-content-center">
                                <input type="checkbox" name="view[]" @if ($advert->view) checked @endif class="form-check-input" value="{{$advert->id}}" />
                        </div>
                    </td>
                    <td class="align-middle">{{\App\Models\AyBy::TYPES[$advert->type]}}</td>
                    <td class="align-middle @if ($advert->price_hot)price-hot @endif">{{$advert->price_hot}} @if (((int)$advert->price_hot_old)) <span class="updated-price"> было {{$advert->price_hot_old}}</span>@endif</td>
                    <td class="align-middle">{{$advert->price_auction}}</td>
                    <td class="align-middle">{{$advert->updated_at->format('d.m.Y H:i')}}</td>
                    <td class="align-middle">{{$advert->author}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        </form>
        @if ($adverts instanceof \Illuminate\Pagination\AbstractPaginator && $adverts->total() > $adverts->perPage())
            <div class="my-4 border-top"></div>
            <div class="d-flex justify-content-between">
                @if (request()->query())
                    {{$adverts->appends(request()->query())->onEachSide(1)->links()}}
                @else
                    {{$adverts->links()}}
                @endif
            </div>
        @endif
    </div>
    <style>
        .auction {
           background-color:#ec4848!important;
        }
        .green {
            color: #3ea721;
        }
        .price-hot {
            color: #dc8c1b;
        }
        .table-responsive {
            max-height:800px;
        }
        .not-view {
            background-color: #b8fabc !important;
        }
        .updated-price {
            color: #000;
        }
    </style>
@endsection
