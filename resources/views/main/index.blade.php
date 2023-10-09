@extends('layouts.main')
@section('title', $page->title)
@section('description', $page->description)
@section('content')
    @if ($page->url == 'home')
        @include('includes.last-adverts-block')
        {{--  @include('includes.sell-faster-block') --}}
        @include('includes.video')
          @include('includes.popular-block')
          @include('includes.last-news-block')
      @else
          <section class="profile-breadcrumbs">
              <div class="container">
                  <div class="page-breadcrumb pb-3 d-flex align-items-center">
                      <h3 class="breadcrumb-title pe-3">
                          {{$page->title}}
                      </h3>
                      <div class="ms-auto">
                          <nav aria-label="breadcrumb">
                              <ol class="breadcrumb mb-0 p-0">
                                  <li class="breadcrumb-item"><a href="/">Главная</a>
                                  </li>
                                  <li class="breadcrumb-item active" aria-current="page">
                                      {{$page->title}}
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
                                      <p>{{$page->title}}</p>
                                  </div>
                                  <div class="card-body blue-card">
                                      <h4 class="mt-2 new-h4">{{$page->title}}</h4>
                                      {!!$page->content!!}
                                  </div>
                              </div>
                          </div>
                      </div>

                  </div>
                  <!--end row-->
              </div>
          </section>
      @endif
  @endsection
