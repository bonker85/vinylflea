<section class="adverts-section">
    <div class="container">
        <div class="d-flex align-items-center all-vinyl-list pt-4">
            <h5 class="text-uppercase mt-0 mb-0 btn btn-success fs-5 rounded-3 fw-bold">Обновления</h5>
            <a href="{{route('vinyls.style', 'all')}}" class="text-uppercase text-info ms-auto btn btn-dark fs-5 rounded-3 fw-bold">Смотреть все<i class='bx bx-chevron-right'></i></a>
        </div>
        <hr/>
        @include('includes.advert-block', ['adverts' => $lastAdvertsList])
        <div class="d-flex align-items-center pb-4 container">
            <a href="{{route('vinyls.style', 'all')}}" class="padding-btn p-2 text-uppercase text-info ms-auto btn btn-dark fs-5 rounded-3 fw-bold">Смотреть все<i class="bx bx-chevron-right"></i></a>
        </div>
    </div>
</section>
