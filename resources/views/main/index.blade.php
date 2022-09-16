@extends('layouts.main')
@section('title', $page->title)
@section('content')
    @include('includes.left-sidebar')
    <div class="col-sm-9">
        @if ($page->url != 'home')
        <div class="blog-post-area">
            <h1 class="title text-center title-h1">{{$page->header}}</h1>
            <div class="single-blog-post">
                {!! $page->content !!}
            </div>
        </div>
        @endif

    </div>
@endsection
