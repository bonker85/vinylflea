@if(session('success'))
    <div class="container alert alert-warning alert-dismissible fade show" role="alert">
        {!! session('success') !!}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(session('error'))
    <div class="container alert alert-danger alert-dismissible fade show" role="alert">
        {{session('error')}}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if (auth()->check() && $countViewMessages)
    <div class="message-block container alert alert-warning alert-dismissible fade show mt-4" role="alert">
        <a class="link-messages" href="{{route('profile.messages')}}">У Вас есть непрочитанные сообщения</a>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
