<!--
<section class="back-wall">
    <div class="container">
        <div class="pb-4 row pt-2">
            <a href="{{route('main.vinyl-list')}}" class="d-flex align-items-center justify-content-center ">
                <img src="/images/vinyl-list.jpg" width="100%" style="max-width:600px" />
            </a>
        </div>
    </div>
</section>
-->
<section class="back-wall">
    <div class="container popular-grid">
        <div class="row row-cols-1 row-cols-lg-2 row-cols-xl-3">
            @foreach ($popular_styles as $popular_style)
            <div class="col">
                <div class="card rounded-0 border shadow-none">
                    <div class="row g-0 align-items-center popular-block">
                        <div class="col popular-img">
                            <a href="/vinyls/{{$popular_style->slug}}">
                                <img src="{{asset('/assets/images/popular/' . $popular_style->slug . '.webp')}}" class="img-fluid" alt="Popular {{mb_strtoupper($popular_style->name)}}">
                            </a>
                        </div>
                        <div class="col">
                            <div class="card-body blue-card">
                                <h5 class="card-title text-uppercase">{{mb_strtoupper($popular_style->name)}}</h5>
                                <p class="card-text text-uppercase">{{$popular_style->count}} пластинок</p>	<a href="/vinyls/{{$popular_style->slug}}" class="btn btn-dark btn-ecomm">Смотреть</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        <!--end row-->
    </div>
</section>
