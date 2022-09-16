<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light d-flex justify-content-between">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>
    <ul class="navbar-nav navbar-right">
        <li class="nav-item">
            <form action="{{route('logout')}}" method="post">
                @csrf
                <button type="submit" class="logout"><i class="fas fa-sign-out-alt"></i></button>
            </form>
        </li>
    </ul>
</nav>
<!-- /.navbar -->
