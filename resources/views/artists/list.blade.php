@extends('layouts.main')
@section('description', 'Список исполнителей')
@section('title', 'Список исполнителей' )
@section('content')
    <section class="profile-breadcrumbs ">
        <div class="container">
            <div class="page-breadcrumb pb-3 d-flex align-items-center">
                <h3 class="breadcrumb-title pe-3">
                    Список исполнителей
                </h3>
                <div class="ms-auto">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="/">Главная</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                               Список исполнителей
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>
    <section>
        <div class="container">
            <div class="d-flex align-items-center">
                <h5 class="text-uppercase mb-0 mt-4 text-center fs-5 rounded-3 fw-bold mb-4">
                    Список исполнителей
                </h5>
            </div>
            <div class="row">
            @foreach ($artists as $artist)
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <a href="{{route('artist', $artist->discogs_artist_id)}}">{{$artist->name}}</a><br/>
                </div>
            @endforeach
            </div>
        </div>
    </section>
@endsection
