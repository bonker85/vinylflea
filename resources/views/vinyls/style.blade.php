@extends('layouts.main')
@if (isset($style))
    @section('description', 'Виниловые пластинки в стиле ' . $style->name)
    @section('title',  'Виниловые пластинки в стиле ' . $style->name)
@else
    @section('title', 'Все стили')
@endif
@section('content')
    <section class="profile-breadcrumbs ">
        <div class="container">
            <div class="page-breadcrumb pb-3 d-flex align-items-center">
                <h3 class="breadcrumb-title pe-3">
                    @if (isset($style))
                        Пластинки в стиле {{$style->name}}
                    @else
                        Все стили
                    @endif
                </h3>
                <div class="ms-auto">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="/">Главная</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                @if (isset($style))
                                    Пластинки в стиле {{$style->name}}
                                @else
                                    Все стили
                                @endif
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>
    <section @unless(request()->q) class="adverts-section" @endunless>
        <div class="container">
            <div class="d-flex align-items-center">
                <h5 class="text-uppercase mb-0 mt-4 text-center fs-5 rounded-3 fw-bold mb-4">
                    @if (isset($style))
                        Пластинки в стиле {{$style->name}}
                    @else
                        Все стили
                    @endif
                </h5>
            </div>
            @if ($searchMess)
                {!! $searchMess !!}
            @endif
            @if ($adverts->total())
                @include('includes.advert-block', ['adverts' => $adverts])
            @elseif ($lastAdvertInStyle)
                <h5 class="text-uppercase">Последние обновления в стиле {{$style->name}}</h5>
                <hr class="mb-4"/>
                @include('includes.advert-block', ['adverts' => $lastAdvertInStyle])
            @endif
        </div>
    </section>
    @include('includes.video')
    @include('includes.popular-block')
    @include('includes.last-news-block')
    <!--end Featured product-->
@endsection
