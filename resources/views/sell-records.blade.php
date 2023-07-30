@extends('layouts.main')
@section('title', "Продать виниловые пластинки")
@section('description', "Продать виниловые пластинки, Где продать виниловые пластинки")
@section('content')
    <section>
        <div class="container">
            <div class="row">
                <div class="col-12 col-lg-12">
                    <h4>Пишите нам в мессенджеры <a href="https://t.me/vinylfleaby">Telegram</a>, <a href="viber://chat?number=375257167247">Viber</a> или социальные сети <a href="https://vk.com/id765124456">Вконтакте</a>, <a href="https://www.instagram.com/vinyl.flea/">Instagram</a></h4>
                    @if (isset($success))
                        <p class="success-sell-send">Данные приняты в обработку. Скоро с Вами свяжутся. Спасибо!</p>
                    @else
                    <img width="100%" src="{{asset('assets/images/sell-vinyl.png')}}"/>
                    <div class="form-sell-vinyl">
                        <form method="post" action="{{route('main.sell-records')}}">
                            @csrf
                            @method('POST')
                            <textarea name="message" placeholder="Оставьте ваши контактные данные такие как (телефон, viber или email), а также адрес, состояние пластинок  и мы свяжемся с Вами в ближайшее время, Cпасибо!"></textarea>
                            <input type="submit" class="btn btn-success"/>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
