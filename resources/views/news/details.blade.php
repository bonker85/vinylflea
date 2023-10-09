@extends('layouts.main')
@section('description', 'Виниловые пластинки в РБ большой выбор, обмен, ' . $title)
@section('title', $title)
@section('content')
    <section class="profile-breadcrumbs">
        <div class="container">
            <div class="page-breadcrumb pb-3 d-flex align-items-center">
                <h3 class="breadcrumb-title pe-3">
                    {{$title}}
                </h3>
                <div class="ms-auto">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="/">Главная</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                <a href="{{route('news')}}">Новости</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                {{$title}}
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>
    <section class="">
        <div class="container">
            <div class="row">
                <div class="col-12 col-lg-12">
                    <div class="blog-right-sidebar p-3">
                            <div class="card mb-4">
                                <div class="new-header">
                                    <a href="{{route('news')}}"> Новости</a> |
                                    <p>{{$new->title}}</p>
                                </div>
                                <img src="{{asset('/assets/images/posts/'  . $new->id . '.webp')}}" class="card-img-top" alt="{{$new->name}}">
                                <div class="card-body blue-card">
                                    <div class="list-inline">
                                        <a href="javascript:;" class="list-inline-item"><i class="bx bx-calendar me-1"></i>{{$new->getFormatDate()}}</a>
                                    </div>
                                    <h4 class="mt-2 new-h4">{{$new->name}}</h4>
                                    {!! $new->content !!}
                                </div>
                            </div>
                    </div>
                </div>

            </div>
            <!--end row-->
        </div>
    </section>
@endsection
