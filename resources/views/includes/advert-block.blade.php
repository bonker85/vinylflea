<div class="product-grid">
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4">
        @foreach ($adverts as $advert)
            <div class="">
                <div class="card rounded-0 product-card">
                    <div class="card-header bg-transparent border-bottom-0">
                        <div class="d-flex align-items-center justify-content-end gap-3">
                            @if (auth()->check() && auth()->user()->id != $advert->user_id)
                                <a class="favorit-link" data-user="{{auth()->user()->id}}" data-advert="{{$advert->id}}">
                                    <div class="product-wishlist">
                                        <i class='bx bx-heart'></i>
                                    </div>
                                </a>
                            @else
                                <div class="product-wishlist">
                                    <i class='bx bx-heart'></i>
                                </div>
                            @endif
                        </div>
                    </div>
                    <a href="{{route('vinyls.details', $advert->url)}}" class="main-advert-link-block">
                    @if (count($advert->images))
                        <div class="main-advert-img image-rds">
                            @foreach ($advert->images as $image)
                                <img src="{{asset('/storage' . $image->path)}}" class="card-img-top"   alt="{{$advert->name}}" loading="lazy">
                                @break
                            @endforeach
                    @else
                        <div class="main-advert-img no-image-bg">
                            <img src="{{asset('/assets/images/avatars/no-avatar.png')}}" class="card-img-top"   alt="{{$advert->name}}" loading="lazy">
                    @endif
                        </div>
                    </a>
                    <div class="card-body">
                        <div class="product-info">
                            <a href="{{route('vinyls.details', $advert->url)}}">
                                <h6 class="product-name mb-2">{{$advert->name}}</h6>
                            </a>
                            <a href="{{route('vinyls.style', $advert->style->slug)}}">
                                <p class="product-catergory font-13 mb-1">Стиль: <b>{{$advert->style->name}}</b></p>
                            </a>
                            <p class="product-catergory font-13 mb-1">Исполнитель:
                                <span class="author">@if ($advert->author) {{$advert->author}} @else не указан @endif</span></p>
                            <div class="d-flex align-items-center publisher">
                                <a href="{{route('user', $advert->user_id)}}"> {{$advert->user->name}} </a>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="mb-1 product-price">
                        <span class="fs-5">
                            @if ($advert->deal == 'sale')
                                {{str_replace('.00', '', $advert->price)}} Руб.
                            @elseif ($advert->deal == 'exchange')
                                обменяю
                            @else
                                отдам даром
                            @endif
                        </span>
                                </div>
                            </div>
                            <div class="product-action mt-2">
                                <div class="d-grid gap-2">
                                    @auth
                                        @if(auth()->user()->id == $advert->user_id) <a href="{{route('vinyls.details', $advert->url)}}" class="btn btn-dark btn-ecomm"><i class='bx bxs-show'></i>Просмотр пластинки</a>@else<a href="javascript:;" data-name="{{$advert->name}}" data-id="{{$advert->id}}" data-button="" data-bs-toggle="modal" data-bs-target="#message-modal" class="btn btn-dark btn-ecomm"><i class='bx bx-message'></i>Отправить сообщение</a>@endif
                                    @elseguest
                                        <a href="{{route('login')}}" class="btn btn-dark btn-ecomm">	<i class='bx bx-message'></i>Отправить сообщение</a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                            </div>
                </div>
                @endforeach
            </div>
        @if ($adverts instanceof \Illuminate\Pagination\AbstractPaginator &&
                    $adverts->total() > $adverts->perPage())
            <div class="my-4 border-top"></div>
            <div class="d-flex justify-content-between">
                {{ $adverts->links()}}
            </div>
        @endif
    </div>
</div>
