@extends('layouts.main')
@section('title', 'Мои сообщения')
@section('content')
<section class="py-3 border-bottom border-top d-none d-md-flex bg-light">
    <div class="container">
        <div class="page-breadcrumb d-flex align-items-center">
            <h3 class="breadcrumb-title pe-3">Мои сообщения</h3>
            <div class="ms-auto">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="/"><i class="bx bx-home-alt"></i> Главная</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Мои сообщения</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>
<!--end breadcrumb-->
<!--start shop cart-->
<section class="py-4">
    <div class="container">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    @if ($messages)
                        <div class="col-lg-4 advert-mess-left-block">
                            <div class="card shadow-none mb-3 mb-lg-0 border rounded-0">
                                <div class="card-body advert-show-list">
                                    <div class="list-group list-group-flush ">
                                        <a  class="list-group-item active d-flex justify-content-between align-items-center">
                                            <div class="advert-mess-h">Показать все сообщения</div>
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body advert-mess-list">
                                    <div class="list-group list-group-flush ">
                                        @foreach ($advertLists as $item)
                                            @if ($item->advert)
                                                <a title="@if ($item->advert->author) {{$item->advert->author}} | @endif {{$item->advert->name}}" href="{{route('profile.messages', $item->id)}}" class="list-group-item @if(request()->advertDialogId == $item->id || (!request()->advertDialogId && $loop->first)) active @endif d-flex justify-content-between align-items-center">
                                                    <div>
                                                        @if ($item->advert->images && count($item->advert->images))
                                                            @foreach ($item->advert->images as $image)
                                                                <img src="{{asset('/storage' . $image->path)}}" class="advert-img-mess"   alt="{{$item->advert->name}}" loading="lazy">
                                                                @break
                                                            @endforeach
                                                        @else
                                                            <img src="{{asset('/assets/images/avatars/no-avatar.png')}}" class="advert-img-mess"   alt="{{$item->advert->name}}" loading="lazy">
                                                        @endif
                                                    </div>
                                                    <div class="advert-mess-h">@if ($item->advert->author) {{$item->advert->author}} | @endif {{$item->advert->name}}</div>
                                                    <div class="mess-sender">@if (auth()->user()->id == $item->from_user_id) {{$item->toUser->name}} @else {{$item->fromUser->name}} @endif</div>
                                                    @if ($item->count_messages)<div class="count-messages">{{$item->count_messages}}</div>@endif
                                                </a>
                                            @else
                                                <a title="Пластинка удалена" href="{{route('profile.messages', $item->id)}}" class="list-group-item @if(request()->advertDialogId == $item->id || (!request()->advertDialogId && $loop->first)) active @endif d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <img src="{{asset('/assets/images/avatars/no-avatar.png')}}" class="advert-img-mess" loading="lazy">
                                                    </div>
                                                    <div class="advert-mess-h del-advert-name">Пластинка удалена</div>
                                                    @if ($item->count_messages)<div class="count-messages">{{$item->count_messages}}</div>@endif
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="card shadow-none mb-0 border message-header">
                                <div class="card-body">
                                    <div class="mess-avatar-block">
                                        @if ($advertDialog->advert)
                                        <a href="{{route('vinyls.details', $advertDialog->advert->url)}}" target="_blank">
                                            @if ($advertDialog->advert->images && count($advertDialog->advert->images))
                                                @foreach ($advertDialog->advert->images as $image)
                                                    <img src="{{asset('/storage' . $image->path)}}" class="advert-img-mess"   alt="{{$advertDialog->advert->name}}" loading="lazy">
                                                    @break
                                                @endforeach
                                            @else
                                                <img src="{{asset('/assets/images/avatars/no-avatar.png')}}" class="advert-img-mess"   alt="{{$advertDialog->advert->name}}" loading="lazy">
                                            @endif
                                        </a>
                                        @else
                                            <img src="{{asset('/assets/images/avatars/no-avatar.png')}}" class="advert-img-mess"   alt="Пластинка удалена" loading="lazy">
                                        @endif
                                    </div>
                                    <div class="mess-advert-block">
                                        <div class="advert-mess-h del-advert-name">Пластинка удалена</div>
                                        <a href="{{route('user', (auth()->user()->id == $advertDialog->from_user_id) ? $advertDialog->toUser->id : $advertDialog->fromUser->id)}}" class="advert-mess-author-block" target="_blank">
                                            @if (auth()->user()->id == $advertDialog->from_user_id) @php $avatar = $advertDialog->toUser->avatar @endphp @else @php $avatar = $advertDialog->fromUser->avatar @endphp @endif
                                            <img src="{{asset($avatar ? 'storage' . $avatar : '/assets/images/avatars/no-avatar.png')}}" />
                                            <p> @if (auth()->user()->id == $advertDialog->from_user_id) {{$advertDialog->toUser->name}} @else {{$advertDialog->fromUser->name}} @endif</p>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="card shadow-none mb-0 border messages-block">
                                <div class="card-body ">
                                    <div class="">
                                        <ul class="chat">
                                            @foreach ($messages as $message)
                                                <li class="chat-tip @if($message->from_id == auth()->user()->id) right-user @else left-user @endif">
                                                    <div class="mess-dt">{{$message->getFormatDate()}}</div>
                                                    @if($message->to_id == auth()->user()->id)
                                                    <div>
                                                        <img class="avatar-mess" src="{{asset($avatar ? 'storage' . $avatar : '/assets/images/avatars/no-avatar.png')}}"/>
                                                    </div>
                                                    @endif
                                                   {{$message->message}}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="card shadow-none mb-0 border message-footer">
                                <form method="post" action="{{route('profile.add_message', $advertDialog->id)}}" id="message_form">
                                    @csrf
                                    <div class="card-body">
                                            <textarea maxlength="1000" name="message" class="message-textarea form-control" rows="2"></textarea>
                                            <button type="submit" class="offset-0  btn btn-success">Отправить</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @else
                        @include('includes.profile-menu')
                        <div class="col-lg-8">
                            <h3>Сообщений нет</h3>
                        </div>
                    @endif
                </div>
                <!--end row-->
            </div>
        </div>
    </div>
</section>
@endsection
