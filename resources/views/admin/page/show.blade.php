@extends('admin.layouts.main')
@section('title', 'Просмотр страницы')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Просмотр страницы</h1>
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
                        <div class="card">
                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover text-nowrap">
                                    <tbody>
                                    <tr>
                                        <td>ID</td>
                                        <td>{{$page->id}}</td>
                                    </tr>
                                    <tr>
                                        <td>Название</td>
                                        <td>{{$page->name}}</td>
                                    </tr>
                                    <tr>
                                        <td>Заголовок страницы</td>
                                        <td>{{$page->header}}</td>
                                    </tr>
                                    <tr>
                                        <td>Url</td>
                                        <td>{{$page->url}}</td>
                                    </tr>
                                    <tr>
                                        <td>Мета-тег title</td>
                                        <td>{{$page->title}}</td>
                                    </tr>
                                    <tr>
                                        <td>Мета-тег keywords</td>
                                        <td>{{$page->keywords}}</td>
                                    </tr>
                                    <tr>
                                        <td>Мета-тег description</td>
                                        <td>{{$page->description}}</td>
                                    </tr>
                                    <tr>
                                        <td>Содержание</td>
                                        <td>{!!$page->content!!}</td>
                                    </tr>
                                    <tr>
                                        <td>Относится к странице</td>
                                        <td>
                                            {{$parent_name}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Активна</td>
                                        <td>
                                            @if ($page->status)
                                                Да
                                            @else
                                                Нет
                                            @endif
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.row -->

            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection
