@extends('admin.layouts.main')
@section('title', 'Страницы')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Страницы</h1>
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
                    <div class="d-flex justify-content-between col-12">
                        <div class="col-4 mb-3 pl-0">
                            <a class="btn btn-block btn-primary" href="{{route('admin.page.create')}}">Добавить</a>
                        </div>
                        <div class="col-4 mb-3">
                            <a class="btn btn-block btn-success disabled" id="edit_link" data-link="{{route('admin.page.edit',['page'=>0])}}" href="">Изменить</a>
                        </div>
                        <div class="col-4 mb-3 pr-0">
                            <form method="post" action="" data-link="{{route('admin.page.destroy')}}" id="destroy_form">
                                @method('delete')
                                @csrf
                                <button type="submit" class="btn btn-block btn-danger disabled">Удалить</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-12">
                        <div id="kt_docs_jstree_dragdrop"></div>
                    </div>
                </div>
                <!-- /.row -->

            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection
