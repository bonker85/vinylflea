@extends('admin.layouts.main')
@section('title', 'Редактирование пользователя')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Редактирование пользователя</h1>
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
                        <form action="{{route('admin.user.update', $user->id)}}" method="post" class="w-50">
                            @csrf
                            @method('patch')
                            <div class="form-group">
                                <label>Имя</label>
                                <input type="name" class="form-control" name="name" value="{{$user->name}}"  placeholder="Имя">
                                @error('name')
                                    <div class="text-danger">{{$message}}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="text" class="form-control" value="{{$user->email}}" name="email"  placeholder="Email">
                                @error('email')
                                    <div class="text-danger">{{$message}}</div>
                                @enderror
                            </div>
                            <input type="hidden" name="user_id" value="{{$user->id}}"/>
                            <div class="form-group">
                                <label>Роль</label>
                                <select class="form-control" name="role_id">
                                    <option value="">Выбрать</option>
                                    @foreach($roles as $id => $role)
                                        <option {{$id === $user->role_id ? 'selected' : ''}} value="{{$id}}">{{$role}}</option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <div class="text-danger">{{$message}}</div>
                                @enderror
                            </div>
                            <input type="submit" class="btn btn-primary" value="Обновить"/>
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
