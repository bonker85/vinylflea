<!--start News-->
<section class="back-wall">
    <div class="container">
        <div class="d-flex align-items-center pt-2 pb-3">
            <h5 class="text-uppercase fs-4 fw-bold mt-0 mb-0">Новости</h5>
            <a href="{{route('news')}}" class="text-uppercase   ms-auto text-dark button-st">Все новости<i class='bx bx-chevron-right'></i></a>
        </div>
        <div class="product-grid">
            <div class="latest-news owl-carousel owl-theme">
                @foreach ($lastNewsList as $new)
                    <div class="item">
                        <div class="card rounded-0 product-card border">
                            @if ($new->id == 9)
                                <a href="/{{$new->url}}">
                            @else
                                <a href="{{route('news', $new->url)}}">
                            @endif
                                <img src="{{asset('/assets/images/posts/' . $new->id . '.webp')}}" class="card-img-top border-bottom" alt="...">
                            </a>
                            <div class="card-body blue-card">
                                <div class="news-title">
                                    @if ($new->id == 9)
                                        <a href="/{{$new->url}}">
                                    @else
                                        <a href="{{route('news', $new->url)}}">
                                    @endif
                                        <h5 class="text-capitalize">{{$new->name}}</h5>
                                    </a>
                                </div>
                                <div class="news-content mb-0">
                                    @if (mb_strlen($new->content) > 300)
                                        {!! mb_substr($new->content, 0, 205) . '...' !!}
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
