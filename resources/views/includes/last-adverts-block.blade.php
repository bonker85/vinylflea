<section>
    <div class="container">
        <div class="d-flex align-items-center all-vinyl-list">
            <h5 class="text-uppercase mt-0 mb-0">Обновления</h5>
            <a href="{{route('vinyls.style', 'all')}}" class="btn btn-dark btn-ecomm ms-auto rounded-0">Смотреть все<i class='bx bx-chevron-right'></i></a>
        </div>
        <hr/>
        @include('includes.advert-block', ['adverts' => $lastAdvertsList])

</section>
