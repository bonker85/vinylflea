@extends('admin.layouts.main')
@section('title', 'Добавление страницы')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Добавление страницы</h1>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- Small boxes (Stat box) -->
                <div class="row">
                    <div class="col-12">
                        <form action="{{route('admin.page.store')}}" method="post" class="col-md-6 col-sm-12 pl-0" id="form_admin_create" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label>Название</label>
                                <input type="text" id="name" value="{{old('name')}}" class="form-control" name="name"  placeholder="Название">
                                @error('name')
                                    <div class="text-danger">{{$message}}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Заголовок страницы</label>
                                <input type="text" id="header" value="{{old('header')}}" class="form-control" name="header"  placeholder="Заголовок">
                                @error('header')
                                    <div class="text-danger">{{$message}}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Url <span class="translate d-none text-success"> (<b id="url_translate"></b>)</span></label>
                                <input autocomplete="off" type="name" value="{{old('url')}}" id="url" class="form-control" name="url"  placeholder="Url">
                                @error('url')
                                    <div class="text-danger">{{$message}}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Мета-тег title</label>
                                <input type="text" id="title" value="{{old('title')}}" class="form-control" name="title"  placeholder="Meta-Title">
                                @error('title')
                                    <div class="text-danger">{{$message}}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Мета-тег keywords</label>
                                <input type="text" id="keywords" value="{{old('keywords')}}" class="form-control" name="keywords"  placeholder="Meta-Keywords">
                                @error('keywords')
                                    <div class="text-danger">{{$message}}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Мета-тег description</label>
                                <input type="text" id="description" value="{{old('description')}}" class="form-control" name="description"  placeholder="Meta-Description">
                                @error('description')
                                <div class="text-danger">{{$message}}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Содержание</label>
                                <textarea id="summernote" name="content">{{old('content')}}</textarea>
                                @error('content')
                                    <div class="text-danger">{{$message}}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>К какой странице относится</label>
                                <select class="form-control" name="parent_id" id="parent_id">
                                    <option value="">Выбрать</option>
                                    <option value="0" {{old('parent_id') === '0' ? 'selected' : ''}}>Корневая</option>
                                    {!! $options !!}
                                </select>
                                @error('parent_id')
                                    <div class="text-danger">{{$message}}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <div class="form-check d-flex">
                                    <label class="ml-2">Активна</label>
                                    <input type="hidden" name="status" value="0"/>
                                    <input class="form-check-input mr-2" type="checkbox" {{old('status') === '1' ? 'checked' : ''}}  value="1" name="status">
                                </div>
                                @error('status')
                                <div class="text-danger">{{$message}}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <div class="door-gallery-img">
                                    <div class="add-link"><a href="#">Добавить изображениe</a></div>
                                </div>
                                <div class="gallery-img">
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" name="add_images[]">
                                            <label class="custom-file-label">Выбрать</label>
                                        </div>
                                        <div class="input-group-append">
                                            <span class="input-group-text">Обновить</span>
                                        </div>
                                    </div>
                            </div>
                            <div class="form-group">
                                <input type="submit" class="btn btn-primary" value="Добавить"/>
                            </div>
                        </form>
                    </div>

                </div>
                <!-- /.row -->

            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection
