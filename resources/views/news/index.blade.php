@extends('layouts.main')
@section('description', 'Виниловые пластинки в РБ большой выбор, обмен, ' . $title)
@section('title', $title)
@section('content')
    <section class="py-3 border-bottom mb-3 border-top d-none d-md-flex bg-light profile-breadcrumbs">
        <div class="container">
            <div class="page-breadcrumb d-flex align-items-center">
                <h3 class="breadcrumb-title pe-3">
                    {{$title}}
                </h3>
                <div class="ms-auto">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="/">Главная</a>
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
    <section class="py-4">
        <div class="container">
            <div class="row">
                <div class="col-12 col-lg-12">
                    <div class="blog-right-sidebar p-3">
                        @foreach ($newsList as $new)
                            <div class="card mb-4">
                                <img src="{{asset('/assets/images/posts/'  . $new->id . '.webp')}}" class="card-img-top" alt="{{$new->name}}">
                                <div class="card-body">
                                    <div class="list-inline">
                                        <a href="javascript:;" class="list-inline-item"><i class="bx bx-calendar me-1"></i>{{$new->getFormatDate()}}</a>
                                    </div>
                                    <h4 class="mt-2 new-h4">{{$new->name}}</h4>
                                    <div>
                                        @if (mb_strlen($new->content) > 300)
                                            {!! mb_substr($new->content, 0, 205) . '...' !!}
                                        @else
                                            {!! $new->content !!}
                                        @endif
                                    </div>
                                    <a href="{{route('news', $new->url)}}" class="btn mt-2 btn-dark btn-ecomm">Читать всю новость <i class="bx bx-chevrons-right"></i></a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
            <!--end row-->
        </div>
    </section>
@endsection
