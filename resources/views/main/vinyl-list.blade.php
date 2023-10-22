@extends('layouts.main')
@section('title', 'Список всех пластинок')
@section('description', 'Список всех пластинок')
@section('content')
    <div class="table-responsive px-2 py-4">
        <div class="mb-3 fs-5 text-center">
            <a href="{{route('download-list')}}" style="color: #dd4433;text-decoration: underline;">СКАЧАТЬ СПИСОК В EXCELE</a></div>
        @if ($adverts instanceof \Illuminate\Pagination\AbstractPaginator &&
         $adverts->total() > $adverts->perPage())
            <div class="my-4 border-top"></div>
            <div class="d-flex justify-content-between">
                @if (request()->get('q'))
                    {{$adverts->appends(['q' => request()->get('q')])->onEachSide(1)->links()}}
                @else
                    {{$adverts->links()}}
                @endif
            </div>
        @endif
        <div class="card">
            <form method="post" action="{{route('main.vinyl-list')}}">
            <div class="card-body row">
                    @csrf
                    @method('post')
                    <div class="col-5">
                        <input type="text" name="author" class="form-control" placeholder="Исполнитель"/>
                    </div>
                    <div class="col-4">
                        <input type="text" name="name" class="form-control" placeholder="Альбом" />
                    </div>
                    <div class="col-3">
                        <button type="submit" class="btn btn-success">Искать</button>
                    </div>
            </div>
                @if (auth()->check() && (int)auth()->user()->role_id === 1)
                    <div class="card-body row">
                    <div class="col-4">
                        <input type="text" name="uid" class="form-control" placeholder="Uid" />
                    </div>
                    <div class="col-4">
                        <input type="text" name="sku" class="form-control" placeholder="Sku" />
                    </div>
                    </div>
                @endif
            </form>
        </div>
    <table class="table table-hover table-bordered">
        <thead class="table-light">
        <tr>
            <th style="width: 100px"></th>
            <th>
                <a href="" class="align-middle d-flex justify-content-between align-items-center">
                    <div>
                     Исполнитель - <span class="vinil-list-name">Название</span>
                    </div>
                    <i class="bx bx-sort-down fs-5"></i>
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
            <th scope="col" class="align-middle" style="width: 50px;">
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
                <td class="text-center">
                    <a href="{{route('vinyls.details', $advert->url)}}" class="vinyl-link-list">
                        @if (count($advert->images))
                            @foreach ($advert->images as $image)
                                <img src="{{thumb_url(asset('/storage' . $image->path), $image)}}" width="50" alt="">
                                @break
                            @endforeach
                        @else
                            <img src="{{asset('/assets/images/avatars/no-avatar.png')}}" width="50" alt="">
                        @endif
                    </a>
                <td class="align-middle text-nowrap">
                    <a href="{{route('vinyls.details', $advert->url)}}" class="vinyl-link-truncate">{{$advert->author}} - <span class="vinil-list-name">{{$advert->name}}</span></a></td>
                <td class="align-middle">{{$advert->sname}}</td>
                <td class="align-middle">{{$advert->price}}</td>
                <td class="align-middle">{{$advert->condition}}</td>
                <td class="align-middle">{{$advert->sku}}</td>
                <td class="align-middle">{{date('d-m-Y',strtotime($advert->created_at))}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
        @if ($adverts instanceof \Illuminate\Pagination\AbstractPaginator && $adverts->total() > $adverts->perPage())
            <div class="my-4 border-top"></div>
            <div class="d-flex justify-content-between">
                @if (request()->get('q'))
                    {{$adverts->appends(['q' => request()->get('q')])->onEachSide(1)->links()}}
                @else
                    {{$adverts->links()}}
                @endif
            </div>
        @endif
    </div>
    <style>
        .table-responsive {
            max-height:600px;
        }
    </style>
@endsection
