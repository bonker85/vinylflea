@extends('admin.layouts.main')
@section('title', 'Пользователи')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Пользователи</h1>
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
                    <div class="md-3 sm-5 mb-3 pl-2">
                        <a class="btn btn-block btn-primary" href="{{route('admin.user.create')}}">Добавить</a>
                    </div>
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body blue-card table-responsive p-0">
                                <table class="table table-hover text-nowrap">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Имя</th>
                                        <th>Профиль</th>
                                        <th>Действие</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($users as $user)
                                        <tr>
                                            <td>{{$user->id}}</td>
                                            <td>{{$user->name}}</td>
                                            <td>
                                                <form method="post" action="{{route('tasks', ['param' => 'toggle_user'])}}">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{$user->id}}" />
                                                    <button type="submit">
                                                        <img src="{{ (($user->avatar) ? cdn_url(asset('/storage') . $user->avatar, $user) : asset('/assets/images/avatars/no-avatar.png'))}}" width="30px" height="30px"/></td>
                                                    </button>
                                                </form>
                                            <td>
                                                <a href="{{route('admin.user.show', ['user' => $user->id])}}"><i class="far fa-eye"></i></a>
                                                <a class="ml-2 text-success" href="{{route('admin.user.edit', ['user' => $user->id])}}"><i class="fas fa-pencil-alt"></i></a>
                                                <form action="{{route('admin.user.delete', $user->id)}}" method="post" class="d-inline">
                                                    @csrf
                                                    @method('delete')
                                                    <button class="ml-2 border-0 bg-transparent" type="submit"><i class="fas fa-trash text-danger" role="button"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
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
