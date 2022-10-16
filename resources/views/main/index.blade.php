@extends('layouts.main')
@section('title', $page->title)
@section('description', $page->description)
@section('content')
    @if ($page->url == 'home')
        @include('includes.last-adverts-block')
        @include('includes.last-news-block');
    @endif
@endsection
