<div class="col-lg-4 profile-menu">
    <div class="card shadow-none mb-3 mb-lg-0 border rounded-0">
        <div class="card-body add-vinyl">
            <a href="{{route('profile.add_advert')}}" class="rounded-3 list-group-item  d-flex justify-content-between align-items-center"><i class='bx bx-plus fs-5'></i> Добавить</a>
        </div>
        <div class="card-body mainmenu">
            <div class="list-group list-group-flush">
                <a href="{{route('profile.adverts')}}" class="list-group-item @if ($route_name == 'profile.adverts') active @endif d-flex justify-content-between align-items-center">Мои пластинки <i class='bx bx-clipboard fs-5'></i></a>
                <a href="account-orders.html" class="list-group-item d-flex justify-content-between align-items-center">Сообщения <i class='bx bx-message fs-5'></i></a>
                <a href="account-downloads.html" class="list-group-item d-flex justify-content-between align-items-center">Избранное <i class='bx bx-heart fs-5'></i></a>
                <a href="{{route('profile.settings')}}" class="list-group-item @if ($route_name == 'profile.settings') active @endif d-flex justify-content-between align-items-center">Настройки <i class='bx bx-user-circle fs-5'></i></a>
                <a href="{{route('logout')}}" class="list-group-item d-flex justify-content-between align-items-center">Выйти <i class='bx bx-log-out fs-5'></i></a>
            </div>
        </div>
    </div>
</div>
