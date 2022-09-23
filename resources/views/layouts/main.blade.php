<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--favicon-->
    <link rel="icon" href="{{asset('assets/images/favicon-32x32.png')}}" type="image/png" />
    <!--plugins-->
    <link href="{{asset('assets/plugins/OwlCarousel/css/owl.carousel.min.css')}}" rel="stylesheet" />
    <link href="{{asset('assets/plugins/metismenu/css/metisMenu.min.css')}}" rel="stylesheet" />
    <!-- loader-->
    <link href="{{asset('assets/css/pace.min.css')}}" rel="stylesheet" />
    <!-- Bootstrap CSS -->
    <link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{mix("/assets/css/build.css")}}" rel="stylesheet">
    <link href="{{asset('/assets/css/icons.css')}}" rel="stylesheet">
    <link href="{{asset('/assets/css/select2.min.css')}}" rel="stylesheet">
    <title>@yield('title') | VinylFlea</title>
</head>

<body>

<b class="screen-overlay"></b>
<!--wrapper-->
<div class="wrapper">
    <!--start top header wrapper-->
    <div class="header-wrapper @if(request()->route()->getPrefix() == '/profile') header-profile @endif">
        <div class="header-content pb-md-0">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-8 col-md-auto">
                        <div class="d-flex align-items-center">
                            <div class="mobile-toggle-menu d-lg-none px-lg-2" data-trigger="#navbar_main"><i class='bx bx-menu'></i>
                            </div>
                            <div class="logo d-none d-lg-flex">
                                <a href="index.html">
                                    <img src="assets/images/logo-icon.png" class="logo-icon" alt="" />
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col col-md order-4 order-md-2 search-box">
                        <div class="input-group flex-nowrap px-xl-4">
                            <input type="text" class="form-control w-100" placeholder="Search for Products">
                            <select class="form-select flex-shrink-0" aria-label="Default select example" style="width: 10.5rem;">
                                <option selected>All Categories</option>
                                <option value="1">One</option>
                                <option value="2">Two</option>
                                <option value="3">Three</option>
                            </select>	<span class="input-group-text cursor-pointer bg-transparent"><i class='bx bx-search'></i></span>
                        </div>
                    </div>
                    <div class="col-4 col-md-auto order-2 order-md-4 block-icons">
                        <div class="top-cart-icons float-end">
                            <nav class="navbar navbar-expand">
                                <ul class="navbar-nav ms-auto">
                                    <li class="nav-item cart-list">
                                        @if(request()->route()->getPrefix() == '/profile')
                                            @if (auth()->user()->avatar)
                                                <img src="{{asset('/storage') . auth()->user()->avatar}}" class="avatar-img" />
                                            @else
                                                <img src="{{asset('/assets/images/avatars/no-avatar.png')}}" class="avatar-img" />
                                            @endif
                                        @else
                                            <a href="/profile" class="nav-link cart-link"><i class='bx bx-user'></i></a>
                                        @endif
                                    </li>
                                    @auth
                                        <li class="nav-item"><a id="logout_link" href="#" class="nav-link cart-link"><i class="bx bx-exit"></i></a>
                                        </li>
                                        <form id="logout_form" method="post" action="{{ route('logout') }}">
                                            @csrf
                                        </form>
                                    @endauth
                                </ul>
                            </nav>
                            @include('includes.profile-menu')
                        </div>
                    </div>
                </div>
                <!--end row-->
            </div>
        </div>
        <div class="primary-menu border-top">
            <div class="container">
                <nav id="navbar_main" class="mobile-offcanvas navbar navbar-expand-lg">
                    <div class="offcanvas-header">
                        <button class="btn-close float-end"></button>
                        <h5 class="py-2">Navigation</h5>
                    </div>
                    <ul class="navbar-nav">
                        <li class="nav-item active"> <a class="nav-link" href="index.html">Home </a>
                        </li>
                        <li class="nav-item dropdown">	<a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">Categories <i class='bx bx-chevron-down'></i></a>
                            <div class="dropdown-menu dropdown-large-menu">
                                <div class="row">
                                    <div class="col-md-4">
                                        <h6 class="large-menu-title">Fashion</h6>
                                        <ul class="">
                                            <li><a href="#">Casual T-Shirts</a>
                                            </li>
                                            <li><a href="#">Formal Shirts</a>
                                            </li>
                                            <li><a href="#">Jackets</a>
                                            </li>
                                            <li><a href="#">Jeans</a>
                                            </li>
                                            <li><a href="#">Dresses</a>
                                            </li>
                                            <li><a href="#">Sneakers</a>
                                            </li>
                                            <li><a href="#">Belts</a>
                                            </li>
                                            <li><a href="#">Sports Shoes</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <!-- end col-3 -->
                                    <div class="col-md-4">
                                        <h6 class="large-menu-title">Electronics</h6>
                                        <ul class="">
                                            <li><a href="#">Mobiles</a>
                                            </li>
                                            <li><a href="#">Laptops</a>
                                            </li>
                                            <li><a href="#">Macbook</a>
                                            </li>
                                            <li><a href="#">Televisions</a>
                                            </li>
                                            <li><a href="#">Lighting</a>
                                            </li>
                                            <li><a href="#">Smart Watch</a>
                                            </li>
                                            <li><a href="#">Galaxy Phones</a>
                                            </li>
                                            <li><a href="#">PC Monitors</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <!-- end col-3 -->
                                    <div class="col-md-4">
                                        <div class="pramotion-banner1">
                                            <img src="assets/images/gallery/menu-img.jpg" class="img-fluid" alt="" />
                                        </div>
                                    </div>
                                    <!-- end col-3 -->
                                </div>
                                <!-- end row -->
                            </div>
                            <!-- dropdown-large.// -->
                        </li>
                        <li class="nav-item dropdown">	<a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">Shop  <i class='bx bx-chevron-down'></i></a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item dropdown-toggle dropdown-toggle-nocaret" href="#">Shop Layouts <i class='bx bx-chevron-right float-end'></i></a>
                                    <ul class="submenu dropdown-menu">
                                        <li><a class="dropdown-item" href="shop-grid-left-sidebar.html">Shop Grid - Left Sidebar</a>
                                        </li>
                                        <li><a class="dropdown-item" href="shop-grid-right-sidebar.html">Shop Grid - Right Sidebar</a>
                                        </li>
                                        <li><a class="dropdown-item" href="shop-list-left-sidebar.html">Shop List - Left Sidebar</a>
                                        </li>
                                        <li><a class="dropdown-item" href="shop-list-right-sidebar.html">Shop List - Right Sidebar</a>
                                        </li>
                                        <li><a class="dropdown-item" href="shop-grid-filter-on-top.html">Shop Grid - Top Filter</a>
                                        </li>
                                        <li><a class="dropdown-item" href="shop-list-filter-on-top.html">Shop List - Top Filter</a>
                                        </li>
                                    </ul>
                                </li>
                                <li><a class="dropdown-item dropdown-toggle dropdown-toggle-nocaret" href="#">Shop Pages <i class='bx bx-chevron-right float-end'></i></a>
                                    <ul class="submenu dropdown-menu">
                                        <li><a class="dropdown-item" href="shop-cart.html">Shop Cart</a>
                                        </li>
                                        <li><a class="dropdown-item" href="shop-categories.html">Shop Categories</a>
                                        </li>
                                        <li><a class="dropdown-item" href="checkout-details.html">Checkout Details</a>
                                        </li>
                                        <li><a class="dropdown-item" href="checkout-shipping.html">Checkout Shipping</a>
                                        </li>
                                        <li><a class="dropdown-item" href="checkout-payment.html">Checkout Payment</a>
                                        </li>
                                        <li><a class="dropdown-item" href="checkout-review.html">Checkout Review</a>
                                        </li>
                                        <li><a class="dropdown-item" href="checkout-complete.html">Checkout Complete</a>
                                        </li>
                                        <li><a class="dropdown-item" href="order-tracking.html">Order Tracking</a>
                                        </li>
                                        <li><a class="dropdown-item" href="product-comparison.html">Product Comparison</a>
                                        </li>
                                    </ul>
                                </li>
                                <li><a class="dropdown-item" href="about-us.html">About Us</a>
                                </li>
                                <li><a class="dropdown-item" href="contact-us.html">Contact Us</a>
                                </li>
                                <li><a class="dropdown-item" href="authentication-signin.html">Sign In</a>
                                </li>
                                <li><a class="dropdown-item" href="authentication-signup.html">Sign Up</a>
                                </li>
                                <li><a class="dropdown-item" href="authentication-forgot-password.html">Forgot Password</a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item"> <a class="nav-link" href="blog.html">Blog </a>
                        </li>
                        <li class="nav-item"> <a class="nav-link" href="about-us.html">About Us </a>
                        </li>
                        <li class="nav-item"> <a class="nav-link" href="contact-us.html">Contact Us </a>
                        </li>
                        <li class="nav-item"> <a class="nav-link" href="shop-categories.html">Our Store</a>
                        </li>
                        <li class="nav-item dropdown">	<a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">My Account  <i class='bx bx-chevron-down'></i></a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="/profile">Dashboard</a>
                                </li>
                                <li><a class="dropdown-item" href="account-downloads.html">Downloads</a>
                                </li>
                                <li><a class="dropdown-item" href="account-orders.html">Orders</a>
                                </li>
                                <li><a class="dropdown-item" href="account-payment-methods.html">Payment Methods</a>
                                </li>
                                <li><a class="dropdown-item" href="account-user-details.html">User Details</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    <!--end top header wrapper-->

    <!--start page wrapper -->
    <div class="page-wrapper">
        @include('includes.message')
        <div class="page-content">
            @yield('content')
        </div>
    </div>
    <!--end page wrapper -->
    <!--start footer section-->
    <footer>
        <section class="py-4 border-top bg-light">
            <div class="container">

                <div class="row row-cols-1 row-cols-md-2 align-items-center">
                    <div class="col">
                        <p class="mb-0">Copyright © 2021. All right reserved.</p>
                    </div>
                </div>
                <!--end row-->
            </div>
        </section>
    </footer>
    <!--end footer section-->
    <!--start quick view product-->
    <!-- Modal -->
    <div class="modal fade" id="QuickViewProduct">
        <div class="modal-dialog modal-dialog-centered modal-xl modal-fullscreen-xl-down">
            <div class="modal-content rounded-0 border-0">
                <div class="modal-body">
                    <button type="button" class="btn-close float-end" data-bs-dismiss="modal"></button>
                    <div class="row g-0">
                        <div class="col-12 col-lg-6">
                            <div class="image-zoom-section">
                                <div class="product-gallery owl-carousel owl-theme border mb-3 p-3" data-slider-id="1">
                                    <div class="item">
                                        <img src="assets/images/product-gallery/01.png" class="img-fluid" alt="">
                                    </div>
                                    <div class="item">
                                        <img src="assets/images/product-gallery/02.png" class="img-fluid" alt="">
                                    </div>
                                    <div class="item">
                                        <img src="assets/images/product-gallery/03.png" class="img-fluid" alt="">
                                    </div>
                                    <div class="item">
                                        <img src="assets/images/product-gallery/04.png" class="img-fluid" alt="">
                                    </div>
                                </div>
                                <div class="owl-thumbs d-flex justify-content-center" data-slider-id="1">
                                    <button class="owl-thumb-item">
                                        <img src="assets/images/product-gallery/01.png" class="" alt="">
                                    </button>
                                    <button class="owl-thumb-item">
                                        <img src="assets/images/product-gallery/02.png" class="" alt="">
                                    </button>
                                    <button class="owl-thumb-item">
                                        <img src="assets/images/product-gallery/03.png" class="" alt="">
                                    </button>
                                    <button class="owl-thumb-item">
                                        <img src="assets/images/product-gallery/04.png" class="" alt="">
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="product-info-section p-3">
                                <h3 class="mt-3 mt-lg-0 mb-0">Allen Solly Men's Polo T-Shirt</h3>
                                <div class="product-rating d-flex align-items-center mt-2">
                                    <div class="rates cursor-pointer font-13">	<i class="bx bxs-star text-warning"></i>
                                        <i class="bx bxs-star text-warning"></i>
                                        <i class="bx bxs-star text-warning"></i>
                                        <i class="bx bxs-star text-warning"></i>
                                        <i class="bx bxs-star text-light-4"></i>
                                    </div>
                                    <div class="ms-1">
                                        <p class="mb-0">(24 Ratings)</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mt-3 gap-2">
                                    <h5 class="mb-0 text-decoration-line-through text-light-3">$98.00</h5>
                                    <h4 class="mb-0">$49.00</h4>
                                </div>
                                <div class="mt-3">
                                    <h6>Discription :</h6>
                                    <p class="mb-0">Virgil Abloh’s Off-White is a streetwear-inspired collection that continues to break away from the conventions of mainstream fashion. Made in Italy, these black and brown Odsy-1000 low-top sneakers.</p>
                                </div>
                                <dl class="row mt-3">	<dt class="col-sm-3">Product id</dt>
                                    <dd class="col-sm-9">#BHU5879</dd>	<dt class="col-sm-3">Delivery</dt>
                                    <dd class="col-sm-9">Russia, USA, and Europe</dd>
                                </dl>
                                <div class="row row-cols-auto align-items-center mt-3">
                                    <div class="col">
                                        <label class="form-label">Quantity</label>
                                        <select class="form-select form-select-sm">
                                            <option>1</option>
                                            <option>2</option>
                                            <option>3</option>
                                            <option>4</option>
                                            <option>5</option>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <label class="form-label">Size</label>
                                        <select class="form-select form-select-sm">
                                            <option>S</option>
                                            <option>M</option>
                                            <option>L</option>
                                            <option>XS</option>
                                            <option>XL</option>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <label class="form-label">Colors</label>
                                        <div class="color-indigators d-flex align-items-center gap-2">
                                            <div class="color-indigator-item bg-primary"></div>
                                            <div class="color-indigator-item bg-danger"></div>
                                            <div class="color-indigator-item bg-success"></div>
                                            <div class="color-indigator-item bg-warning"></div>
                                        </div>
                                    </div>
                                </div>
                                <!--end row-->
                                <div class="d-flex gap-2 mt-3">
                                    <a href="javascript:;" class="btn btn-dark btn-ecomm">	<i class="bx bxs-cart-add"></i>Add to Cart</a>	<a href="javascript:;" class="btn btn-light btn-ecomm"><i class="bx bx-heart"></i>Add to Wishlist</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end row-->
                </div>
            </div>
        </div>
    </div>
    <!--end quick view product-->
    <!--Start Back To Top Button--> <a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
    <!--End Back To Top Button-->
</div>
<!--end wrapper-->

<!-- Bootstrap JS -->
<script src="{{asset("/assets/js/bootstrap.bundle.min.js")}}"></script>
<script src="{{asset("/assets/js/jquery.min.js")}}"></script>
<script src="{{mix("/assets/js/build.js")}}"></script>
<script src="{{asset("/assets/js/jquery.inputmask.min.js")}}"></script>
<script src="{{asset("/assets/plugins/OwlCarousel/js/owl.carousel.min.js")}}"></script>
<script src="{{asset("/assets/plugins/metismenu/js/metisMenu.min.js")}}"></script>
<script src="{{asset("/assets/js/pace.min.js")}}"></script>
<script src="{{asset("/assets/plugins/OwlCarousel/js/owl.carousel2.thumbs.min.js")}}"></script>
<script src="{{asset("/assets/js/select2.min.js")}}"></script>
<!--app JS-->
</body>

</html>


