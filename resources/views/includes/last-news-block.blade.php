<!--start News-->
<section class="py-4">
    <div class="container">
        <div class="d-flex align-items-center">
            <h5 class="text-uppercase mt-0 mb-0">Новости</h5>
            <a href="{{route('news')}}" class="btn btn-dark ms-auto rounded-0">Все новости<i class='bx bx-chevron-right'></i></a>
        </div>
        <hr/>
        <div class="product-grid">
            <div class="latest-news owl-carousel owl-theme">
                @foreach ($lastNewsList as $new)
                    <div class="item">
                        <div class="card rounded-0 product-card border">
                            <a href="{{route('news', $new->url)}}">
                                <img src="{{asset('/assets/images/posts/' . $new->id . '.webp')}}" class="card-img-top border-bottom" alt="...">
                            </a>
                            <div class="card-body">
                                <div class="news-title">
                                    <a href="{{route('news', $new->url)}}">
                                        <h5 class="text-capitalize">{{$new->name}}</h5>
                                    </a>
                                </div>
                                <div class="news-content mb-0">
                                    @if (mb_strlen($new->content) > 300)
                                        {!! mb_substr($new->content, 0, 200) . '...' !!}
                                    @else
                                        {!! $new->content !!}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
<!--end News-->
