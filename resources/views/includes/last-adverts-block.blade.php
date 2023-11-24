<section class="adverts-section">
    <div class="container">
        @if (Request::is('/'))
            <h1 class="header-in-menu">Виниловые Пластинки</h1>
        @endif
        <div class="d-flex align-items-center all-vinyl-list pt-3">
            <span class="text-uppercase fs-4 mt-0 mb-0">Обновления</span>
            <a href="{{route('vinyls.style', 'all')}}" style="margin-top: 2px;" class="button-st text-uppercase   ms-auto text-dark">Смотреть все<i class='bx bx-chevron-right'></i></a>
        </div>
        <hr/>
        @include('includes.advert-block', ['adverts' => $lastAdvertsList])
        <div class="d-flex align-items-center container">
            <a href="{{route('vinyls.style', 'all')}}" class="text-uppercase   ms-auto text-dark button-st">Смотреть все<i class="bx bx-chevron-right"></i></a>
        </div>
    </div>
</section>
