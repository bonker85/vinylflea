@extends('layouts.main')
@section('title', 'Редактирование пластинки')
@section('content')
    <section class="py-3 border-bottom border-top d-none d-md-flex bg-light profile-breadcrumbs">
        <div class="container">
            <div class="page-breadcrumb d-flex align-items-center">
                <h3 class="breadcrumb-title pe-3">Редактирование пластинки</h3>
                <div class="ms-auto">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="/">Главная</a>
                            </li>
                            <li class="breadcrumb-item"><a href="{{route('profile.adverts')}}">Мои пластинки</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Редактирование</li>
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
                            <div class="card shadow-none mb-3 border profile-add_advert-formblock">
                                <div class="card-body">
                                    <form class="row g-3 needs-validation" method="post" action="{{route('profile.update_advert', $advert->id)}}" enctype="multipart/form-data" novalidate>
                                        @csrf
                                        <input type="hidden" name="id" value="{{$advert->id}}" />
                                        <div class="col-12">
                                            <label class="form-label">Название <small><b>(до 100 символов)</b></small> <span class="need-field">*</span></label>
                                            <input type="text" class="form-control" required name="name" value="{{$advert->name}}" maxlength="100">
                                            <div class="invalid-tooltip">
                                                Поле не должно быть пустым!
                                            </div>
                                            @error('name')
                                            <span class="invalid-feedback d-block" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Исполнитель <small><b>(до 60 символов)</b></small></label>
                                            <input type="text" class="form-control"  name="author" value="{{$advert->author}}" maxlength="60">
                                            @error('author')
                                            <span class="invalid-feedback d-block" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label" for="style_id">Стиль <span class="need-field">*</span></label>
                                            <select class="form-control select2" required name="style_id" style="width: 100%; height: 50px">
                                                <option value="">Выберите стиль</option>
                                                @foreach ($styles as $style)
                                                    <option value="{{$style->id}}" @if($advert->style_id == $style->id) selected @endif>{{$style->name}}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-tooltip">
                                                Поле не должно быть пустым!
                                            </div>
                                            @error('style_id')
                                            <span class="invalid-feedback d-block" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-12 ui search search-edition search-box">
                                            <label class="form-label" for="edition_id">Издание</label>
                                            <div class="ui left icon input">
                                                <i class="bx bx-search icon"></i>
                                                <input type="text" class="form-control prompt" id="edition" name="edition" value="{{$edition}}"/>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Год издания</label>
                                            <input type="text" class="form-control"  name="year" id="year" value="{{$advert->year}}">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Состояние <span class="need-field">*</span></label>
                                            <select class="form-control" required name="state">
                                                <option value="">Выберите</option>
                                                @foreach(\App\Services\AdvertService::STATES as $key => $state)
                                                    <option value="{{$key}}" @if($advert->state == $key) selected @endif>{{$state}}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-tooltip">
                                                Поле не должно быть пустым!
                                            </div>
                                            @error('state')
                                            <span class="invalid-feedback d-block" role="alert">
                                                    <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Оценка (пластинки/конверта) </label>
                                            <div class="condition-mess"><a href="{{route('news', 'sistemy-otsenki-vinilovyh-plastinok')}}" target="_blank">посмотреть систему оценки</a></div>
                                            <input type="text" maxlength="100" placeholder="VG+/NM"class="form-control" name="condition" value="{{$advert->condition}}" />
                                            @error('condition')
                                            <span class="invalid-feedback d-block" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Вид сделки <span class="need-field">*</span></label>
                                            <select id="deal" class="form-control" required name="deal">
                                                @foreach(\App\Services\AdvertService::DEAL as $key => $deal)
                                                    <option value="{{$key}}" @if($advert->deal == $key) selected @endif>{{$deal}}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-tooltip">
                                                Поле не должно быть пустым!
                                            </div>
                                            @error('deal')
                                            <span class="invalid-feedback d-block" role="alert">
                                                    <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                        <div class="col-12 deal-sale" @if ($advert->deal !== 'sale') style="display: none;" @endif>
                                            <label class="form-label">Цена (Руб.) <span class="need-field">*</span></label>
                                            <input type="text" class="form-control" required name="price" placeholder="0.00" id="price" value="{{str_replace('.00', '', $advert->price)}}">
                                            <div class="invalid-tooltip">
                                                Поле не должно быть пустым!
                                            </div>
                                            @error('price')
                                            <span class="invalid-feedback d-block" role="alert">
                                                    <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Описание <small><b>(до 1000 символов)</b></small></label>
                                            <textarea type="text" class="form-control" maxlength="950" name="description">{{strip_tags($advert->description)}}</textarea>
                                            <div class="invalid-tooltip">
                                                Поле не должно быть пустым!
                                            </div>
                                            @error('description')
                                            <span class="invalid-feedback d-block" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-12">
                                            <label for="js-file-avatar" class="form-label">Изображения</label>
                                            <div class="vinyl-img-block">
                                                @for ($i = 1; $i <= 4; $i++)
                                                <div class="vinyl-set mb-3">
                                                    <div class="spinner-border d-none"></div>
                                                    @if (isset($advert->images[$i-1]))
                                                        <input type="hidden" name="vinyl[]" value="{{$advert->images[$i-1]['path']}}" id="vinyl{{$i}}"/>
                                                        <div class="del-vinyl-img bx bx-x" id="del-vinyl-img{{$i}}" data-image="{{$advert->images[$i-1]->id}}"></div>
                                                        <img src="{{cdn_url(asset('storage') . $advert->images[$i-1]['path'], $advert->images[$i-1])}}" class="vinyl-img" id="img-vinyl-{{$i}}" />
                                                    @else
                                                        <input type="hidden" name="vinyl[]" value="" id="vinyl{{$i}}"/>
                                                        <div  class="del-vinyl-img bx" id="del-vinyl-img{{$i}}" ></div>
                                                        <img src="{{asset('assets/images/avatars/no-avatar.png')}}" class="no-vinyl-img" id="img-vinyl-{{$i}}" />
                                                    @endif
                                                </div>
                                                @endfor
                                            </div>
                                            <div class="error_message"></div>
                                            <input class="form-control mt-1" name="avatar" type="file" id="js-file-vinyl">
                                        </div>
                                        @if ($admin)
                                        <div class="col-12">
                                            <label class="form-label">Status</label>
                                            <select class="form-control" name="status">
                                                @foreach(\App\Services\AdvertService::STATUS as $key => $status)
                                                    <option value="{{$key}}" @if($advert->status == $key) selected @endif>{{$status}}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-tooltip">
                                                Поле не должно быть пустым!
                                            </div>
                                            @error('state')
                                            <span class="invalid-feedback d-block" role="alert">
                                                    <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                        <div class="col-12" id="reject_message_block" style="@if ($advert->status  == \App\Services\AdvertService::getStatusByName('rejected')) display: block; @else display: none;  @endif">
                                            <label class="form-label">Причина <small><b>(до 255 символов)</b></small> <span class="need-field">*</span></label>
                                            <textarea placeholder="Объявление содержит недопустимые значения" type="text" class="form-control" maxlength="255"  name="reject_message">{{$advert->reject_message}}</textarea>
                                        </div>
                                        @endif
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-dark btn-ecomm add-advert-button">Изменить</button>
                                            @if (in_array(auth()->user()->id, \App\Models\User::MY_USERS_IDS))
                                                <a  id="check_discogs" class="btn btn-ecomm check-button">Проверить</a>
                                                <script>
                                                    document.getElementById('check_discogs').addEventListener('click', function() {
                                                        const name = $('input[name="name"]').val();
                                                        const author = $('input[name="author"]').val();
                                                        const year = $('input[name="year"]').val();
                                                        if (!name) {
                                                            alert('Не задано название альбома');
                                                            return false;
                                                        }
                                                        if (year && (!Number.isInteger(+year) || year.length != 4)) {
                                                            alert('Не верный формат года');
                                                        }
                                                        $.ajax({
                                                            url: "/ajax/check_discogs",
                                                            method: 'GET',
                                                            data: { name: name, author: author, year:year}
                                                        }).done(function(data) {
                                                            $('#releases-ajax-block').html(data);
                                                        });
                                                    });
                                                </script>
                                            @endif
                                        </div>
                                        @if (in_array(auth()->user()->id, \App\Models\User::MY_USERS_IDS))
                                            @if ($advert->discogs_author_ids)
                                                <div class="col-12">
                                                    Связанные исполнители: {!! \App\Services\Utility\DiscogsService::getArtistsLink($advert->discogs_author_ids) !!}
                                                </div>
                                            @endif
                                                <div class="col-12" id="releases-ajax-block">

                                                </div>
                                        @endif
                                    </form>
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
