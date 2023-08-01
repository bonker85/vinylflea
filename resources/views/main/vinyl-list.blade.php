@extends('layouts.main')
@section('title', 'Список всех пластинок')
@section('description', 'Список всех пластинок')
@section('content')
    <div class="table-responsive px-2 py-4">
        <div class="mb-3 fs-5 text-center">
            <a href="{{route('download-list')}}" style="color: #dd4433;text-decoration: underline;">СКАЧАТЬ СПИСОК В EXCELE</a></div>
    <table class="table table-hover table-bordered">
        <thead class="table-light">
        <tr>
            <th scope="col" style="width: 25px;">#</th>
            <th style="width: 100px"></th>
            <th>
                <a href="" class="align-middle d-flex justify-content-between align-items-center">
                    Исполнитель <i class="bx bx-sort-down fs-5"></i>
                </a>
            </th>
            <th scope="col">
                <a href="" class="align-middle d-flex justify-content-between align-items-center">
                    Название <i class="bx bx-sort-down fs-5"></i>
                </a>
            </th>
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
            <th scope="col" class="align-middle">
                Оценка
            </th>
            <th scope="col" class="align-middle">Артикул</th>
            <th scope="col">
                <a href="" class="text-nowrap align-middle d-flex justify-content-between align-items-center">
                    Дата добавления <i class="bx bx-sort-down fs-5"></i>
                </a>
            </th>
        </tr>
        </thead>
        <tbody>
        @foreach ($adverts as $advert)
            <tr>
                <th scope="row" class="align-middle">1</th>
                <td class="text-center"><img width="50px" src="/public/storage/advert_thumbs/11/4248/vinyl1.webp"/></td>
                <td class="align-middle text-nowrap">{{$advert->author}}</td>
                <td class="align-middle text-nowrap">
                    <a href="" class="vinyl-link-list">
                        {{$advert->name}}
                    </a>
                </td>
                <td class="align-middle">{{$advert->sname}}</td>
                <td class="align-middle">{{$advert->price}}</td>
                <td class="align-middle">{{$advert->condition}}</td>
                <td class="align-middle">{{$advert->sku}}</td>
                <td class="align-middle">{{date('d-m-Y',strtotime($advert->created_at))}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
@endsection
