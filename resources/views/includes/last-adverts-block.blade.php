<section class="adverts-section">
    <div class="container">
        <div class="d-flex align-items-center all-vinyl-list pt-4">
            <h5 class="text-uppercase mt-0 mb-0 btn btn-success fs-5 rounded-3 fw-bold">Обновления</h5>
            <a href="{{route('vinyls.style', 'all')}}" class="btn-ecomm ms-auto btn btn-success fs-5 rounded-3 fw-bold">Смотреть все<i class='bx bx-chevron-right'></i></a>
        </div>
        <hr/>
        @include('includes.advert-block', ['adverts' => $lastAdvertsList])

</section>
