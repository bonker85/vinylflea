@extends('layouts.main')
@section('title', 'Добавление пластинки')
@section('content')
    <section class="py-3 border-bottom border-top d-none d-md-flex bg-light profile-breadcrumbs">
        <div class="container">
            <div class="page-breadcrumb d-flex align-items-center">
                <h3 class="breadcrumb-title pe-3">Добавление пластинки</h3>
                <div class="ms-auto">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="/">Главная</a>
                            </li>
                            <li class="breadcrumb-item"><a href="{{route('profile.adverts')}}">Мои пластинки</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Добавление</li>
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
                                    <form class="row g-3 needs-validation" method="post" action="{{route('profile.store_advert')}}" enctype="multipart/form-data" novalidate>
                                        @csrf
                                        <div class="col-12">
                                            <label class="form-label">Название <small><b>(до 100 символов)</b></small> <span class="need-field">*</span></label>
                                            <input type="text" class="form-control" required name="name" value="{{old('name')}}" maxlength="100">
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
                                            <input type="text" class="form-control"  name="author" value="{{old('author')}}" maxlength="60">
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
                                                    <option value="{{$style->id}}" @if(old('style_id') == $style->id) selected @endif>{{$style->name}}</option>
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
                                        <div class="col-12">
                                            <label class="form-label" for="edition_id">Издание</label>
                                            <select class="form-control select2" name="edition_id" style="width: 100%; height: 50px">
                                                <option value="">Выберите издание</option>
                                                @foreach ($editions as $edition)
                                                    <option value="{{$edition->id}}" @if(old('edition_id') == $edition->id) selected @endif>{{$edition->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Год издания</label>
                                            <input type="text" class="form-control"  name="year" id="year" value="{{old('year')}}">
                                            @error('year')
                                            <span class="invalid-feedback d-block" role="alert">
                                                    <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Состояние <span class="need-field">*</span></label>
                                            <select class="form-control" required name="state">
                                                <option value="">Выберите</option>
                                                @foreach(\App\Services\AdvertService::STATES as $key => $state)
                                                    <option value="{{$key}}" @if(old('state') == $key) selected @endif>{{$state}}</option>
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
                                           <input type="text" maxlength="100" placeholder="VG+/NM"class="form-control" name="condition" value="{{old('condition')}}" />
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
                                                    <option value="{{$key}}" @if(old('deal') == $key) selected @endif>{{$deal}}</option>
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
                                        <div class="col-12 deal-sale">
                                            <label class="form-label">Цена (Руб.) <span class="need-field">*</span></label>
                                            <input type="text" class="form-control" required name="price" placeholder="0.00" id="price" value="{{old('price')}}">
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
                                            <label class="form-label">Описание <small><b>(до 1000 символов)</b></small> <span class="need-field">*</span></label>
                                            <textarea type="text" class="form-control" maxlength="950" required name="description">{{old('description')}}</textarea>
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
                                                <div class="vinyl-set mb-3">
                                                    <div class="spinner-border d-none"></div>
                                                    <div  class="del-vinyl-img bx" id="del-vinyl-img1" ></div>
                                                    <input type="hidden" name="vinyl[]" value="" id="vinyl1"/>
                                                    <img src="{{asset('assets/images/avatars/no-avatar.png')}}" class="no-vinyl-img" id="img-vinyl-1" />
                                                </div>
                                                <div class="vinyl-set mb-3">
                                                    <div class="spinner-border d-none"></div>
                                                    <div  class="del-vinyl-img bx" id="del-vinyl-img2" ></div>
                                                    <input type="hidden" name="vinyl[]" value="" id="vinyl2"/>
                                                    <img src="{{asset('assets/images/avatars/no-avatar.png')}}" class="no-vinyl-img" id="img-vinyl-2" />
                                                </div>
                                                <div class="vinyl-set mb-3">
                                                    <div class="spinner-border d-none"></div>
                                                    <div  class="del-vinyl-img bx" id="del-vinyl-img3" ></div>
                                                    <input type="hidden" name="vinyl[]" value="" id="vinyl3"/>
                                                    <img src="{{asset('assets/images/avatars/no-avatar.png')}}" class="no-vinyl-img" id="img-vinyl-3" />
                                                </div>
                                                <div class="vinyl-set mb-3">
                                                    <div class="spinner-border d-none"></div>
                                                    <div  class="del-vinyl-img bx" id="del-vinyl-img4" ></div>
                                                    <input type="hidden" name="vinyl[]" value="" id="vinyl4"/>
                                                    <img src="{{asset('assets/images/avatars/no-avatar.png')}}" class="no-vinyl-img" id="img-vinyl-4" />
                                                </div>
                                            </div>
                                            <div class="error_message"></div>
                                            <input class="form-control mt-1" name="avatar" type="file" id="js-file-vinyl">
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-dark btn-ecomm add-advert-button">Сохранить</button>
                                        </div>
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
