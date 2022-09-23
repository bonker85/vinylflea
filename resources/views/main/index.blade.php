@extends('layouts.main')
@section('title', $page->title)
@section('content')
    <section class="py-4">
        <div class="container">
            <div class="d-flex align-items-center">
                <h5 class="text-uppercase mb-0">FEATURED PRODUCTS</h5>
                <a href="javascript:;" class="btn btn-dark btn-ecomm ms-auto rounded-0">More Products<i class='bx bx-chevron-right'></i></a>
            </div>
            <hr/>
            <div class="product-grid">
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4">
                    <div class="col">
                        <div class="card rounded-0 product-card">
                            <div class="card-header bg-transparent border-bottom-0">
                                <div class="d-flex align-items-center justify-content-end gap-3">
                                    <a href="javascript:;">
                                        <div class="product-compare"><span><i class='bx bx-git-compare'></i> Compare</span>
                                        </div>
                                    </a>
                                    <a href="javascript:;">
                                        <div class="product-wishlist"> <i class='bx bx-heart'></i>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <a href="product-details.html">
                                <img src="assets/images/products/01.png" class="card-img-top" alt="...">
                            </a>
                            <div class="card-body">
                                <div class="product-info">
                                    <a href="javascript:;">
                                        <p class="product-catergory font-13 mb-1">Catergory Name</p>
                                    </a>
                                    <a href="javascript:;">
                                        <h6 class="product-name mb-2">Product Short Name</h6>
                                    </a>
                                    <div class="d-flex align-items-center">
                                        <div class="mb-1 product-price"><span class="me-1 text-decoration-line-through">$99.00</span>
                                            <span class="fs-5">$49.00</span>
                                        </div>
                                        <div class="cursor-pointer ms-auto">
                                            <i class="bx bxs-star text-warning"></i>
                                            <i class="bx bxs-star text-warning"></i>
                                            <i class="bx bxs-star text-warning"></i>
                                            <i class="bx bxs-star text-warning"></i>
                                            <i class="bx bxs-star text-warning"></i>
                                        </div>
                                    </div>
                                    <div class="product-action mt-2">
                                        <div class="d-grid gap-2">
                                            <a href="javascript:;" class="btn btn-dark btn-ecomm">	<i class='bx bxs-cart-add'></i>Add to Cart</a>
                                            <a href="javascript:;" class="btn btn-light btn-ecomm" data-bs-toggle="modal" data-bs-target="#QuickViewProduct"><i class='bx bx-zoom-in'></i>Quick View</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card rounded-0 product-card">
                            <div class="card-header bg-transparent border-bottom-0">
                                <div class="d-flex align-items-center justify-content-end gap-3">
                                    <a href="javascript:;">
                                        <div class="product-compare"><span><i class='bx bx-git-compare'></i> Compare</span>
                                        </div>
                                    </a>
                                    <a href="javascript:;">
                                        <div class="product-wishlist"> <i class='bx bx-heart'></i>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <a href="product-details.html">
                                <img src="assets/images/products/02.png" class="card-img-top" alt="...">
                            </a>
                            <div class="card-body">
                                <div class="product-info">
                                    <a href="javascript:;">
                                        <p class="product-catergory font-13 mb-1">Catergory Name</p>
                                    </a>
                                    <a href="javascript:;">
                                        <h6 class="product-name mb-2">Product Short Name</h6>
                                    </a>
                                    <div class="d-flex align-items-center">
                                        <div class="mb-1 product-price"> <span class="me-1 text-decoration-line-through">$99.00</span>
                                            <span class="fs-5">$49.00</span>
                                        </div>
                                        <div class="cursor-pointer ms-auto"> <i class="bx bxs-star text-warning"></i>
                                            <i class="bx bxs-star text-warning"></i>
                                            <i class="bx bxs-star text-warning"></i>
                                            <i class="bx bxs-star text-light-4"></i>
                                            <i class="bx bxs-star text-light-4"></i>
                                        </div>
                                    </div>
                                    <div class="product-action mt-2">
                                        <div class="d-grid gap-2">
                                            <a href="javascript:;" class="btn btn-dark btn-ecomm">	<i class='bx bxs-cart-add'></i>Add to Cart</a>
                                            <a href="javascript:;" class="btn btn-light btn-ecomm" data-bs-toggle="modal" data-bs-target="#QuickViewProduct"><i class='bx bx-zoom-in'></i>Quick View</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card rounded-0 product-card">
                            <div class="card-header bg-transparent border-bottom-0">
                                <div class="d-flex align-items-center justify-content-end gap-3">
                                    <a href="javascript:;">
                                        <div class="product-compare"><span><i class='bx bx-git-compare'></i> Compare</span>
                                        </div>
                                    </a>
                                    <a href="javascript:;">
                                        <div class="product-wishlist"> <i class='bx bx-heart'></i>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <a href="product-details.html">
                                <img src="assets/images/products/03.png" class="card-img-top" alt="...">
                            </a>
                            <div class="card-body">
                                <div class="product-info">
                                    <a href="javascript:;">
                                        <p class="product-catergory font-13 mb-1">Catergory Name</p>
                                    </a>
                                    <a href="javascript:;">
                                        <h6 class="product-name mb-2">Product Short Name</h6>
                                    </a>
                                    <div class="d-flex align-items-center">
                                        <div class="mb-1 product-price"> <span class="me-1 text-decoration-line-through">$99.00</span>
                                            <span class="fs-5">$49.00</span>
                                        </div>
                                        <div class="cursor-pointer ms-auto"> <i class="bx bxs-star text-warning"></i>
                                            <i class="bx bxs-star text-warning"></i>
                                            <i class="bx bxs-star text-warning"></i>
                                            <i class="bx bxs-star text-warning"></i>
                                            <i class="bx bxs-star text-light-4"></i>
                                        </div>
                                    </div>
                                    <div class="product-action mt-2">
                                        <div class="d-grid gap-2">
                                            <a href="javascript:;" class="btn btn-dark btn-ecomm">	<i class='bx bxs-cart-add'></i>Add to Cart</a>
                                            <a href="javascript:;" class="btn btn-light btn-ecomm" data-bs-toggle="modal" data-bs-target="#QuickViewProduct"><i class='bx bx-zoom-in'></i>Quick View</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card rounded-0 product-card">
                            <div class="card-header bg-transparent border-bottom-0">
                                <div class="d-flex align-items-center justify-content-end gap-3">
                                    <a href="javascript:;">
                                        <div class="product-compare"><span><i class='bx bx-git-compare'></i> Compare</span>
                                        </div>
                                    </a>
                                    <a href="javascript:;">
                                        <div class="product-wishlist"> <i class='bx bx-heart'></i>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <a href="product-details.html">
                                <img src="assets/images/products/04.png" class="card-img-top" alt="...">
                            </a>
                            <div class="card-body">
                                <div class="product-info">
                                    <a href="javascript:;">
                                        <p class="product-catergory font-13 mb-1">Catergory Name</p>
                                    </a>
                                    <a href="javascript:;">
                                        <h6 class="product-name mb-2">Product Short Name</h6>
                                    </a>
                                    <div class="d-flex align-items-center">
                                        <div class="mb-1 product-price"> <span class="me-1 text-decoration-line-through">$99.00</span>
                                            <span class="fs-5">$49.00</span>
                                        </div>
                                        <div class="cursor-pointer ms-auto"> <i class="bx bxs-star text-warning"></i>
                                            <i class="bx bxs-star text-warning"></i>
                                            <i class="bx bxs-star text-warning"></i>
                                            <i class="bx bxs-star text-warning"></i>
                                            <i class="bx bxs-star text-warning"></i>
                                        </div>
                                    </div>
                                    <div class="product-action mt-2">
                                        <div class="d-grid gap-2">
                                            <a href="javascript:;" class="btn btn-dark btn-ecomm">	<i class='bx bxs-cart-add'></i>Add to Cart</a>
                                            <a href="javascript:;" class="btn btn-light btn-ecomm" data-bs-toggle="modal" data-bs-target="#QuickViewProduct"><i class='bx bx-zoom-in'></i>Quick View</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card rounded-0 product-card">
                            <div class="card-header bg-transparent border-bottom-0">
                                <div class="d-flex align-items-center justify-content-end gap-3">
                                    <a href="javascript:;">
                                        <div class="product-compare"><span><i class='bx bx-git-compare'></i> Compare</span>
                                        </div>
                                    </a>
                                    <a href="javascript:;">
                                        <div class="product-wishlist"> <i class='bx bx-heart'></i>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <a href="product-details.html">
                                <img src="assets/images/products/05.png" class="card-img-top" alt="...">
                            </a>
                            <div class="card-body">
                                <div class="product-info">
                                    <a href="javascript:;">
                                        <p class="product-catergory font-13 mb-1">Catergory Name</p>
                                    </a>
                                    <a href="javascript:;">
                                        <h6 class="product-name mb-2">Product Short Name</h6>
                                    </a>
                                    <div class="d-flex align-items-center">
                                        <div class="mb-1 product-price"> <span class="me-1 text-decoration-line-through">$99.00</span>
                                            <span class="fs-5">$49.00</span>
                                        </div>
                                        <div class="cursor-pointer ms-auto"> <i class="bx bxs-star text-warning"></i>
                                            <i class="bx bxs-star text-warning"></i>
                                            <i class="bx bxs-star text-warning"></i>
                                            <i class="bx bxs-star text-light-4"></i>
                                            <i class="bx bxs-star text-light-4"></i>
                                        </div>
                                    </div>
                                    <div class="product-action mt-2">
                                        <div class="d-grid gap-2">
                                            <a href="javascript:;" class="btn btn-dark btn-ecomm">	<i class='bx bxs-cart-add'></i>Add to Cart</a>
                                            <a href="javascript:;" class="btn btn-light btn-ecomm" data-bs-toggle="modal" data-bs-target="#QuickViewProduct"><i class='bx bx-zoom-in'></i>Quick View</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card rounded-0 product-card">
                            <div class="card-header bg-transparent border-bottom-0">
                                <div class="d-flex align-items-center justify-content-end gap-3">
                                    <a href="javascript:;">
                                        <div class="product-compare"><span><i class='bx bx-git-compare'></i> Compare</span>
                                        </div>
                                    </a>
                                    <a href="javascript:;">
                                        <div class="product-wishlist"> <i class='bx bx-heart'></i>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <a href="product-details.html">
                                <img src="assets/images/products/06.png" class="card-img-top" alt="...">
                            </a>
                            <div class="card-body">
                                <div class="product-info">
                                    <a href="javascript:;">
                                        <p class="product-catergory font-13 mb-1">Catergory Name</p>
                                    </a>
                                    <a href="javascript:;">
                                        <h6 class="product-name mb-2">Product Short Name</h6>
                                    </a>
                                    <div class="d-flex align-items-center">
                                        <div class="mb-1 product-price"> <span class="me-1 text-decoration-line-through">$99.00</span>
                                            <span class="fs-5">$49.00</span>
                                        </div>
                                        <div class="cursor-pointer ms-auto"> <i class="bx bxs-star text-warning"></i>
                                            <i class="bx bxs-star text-warning"></i>
                                            <i class="bx bxs-star text-warning"></i>
                                            <i class="bx bxs-star text-warning"></i>
                                            <i class="bx bxs-star text-warning"></i>
                                        </div>
                                    </div>
                                    <div class="product-action mt-2">
                                        <div class="d-grid gap-2">
                                            <a href="javascript:;" class="btn btn-dark btn-ecomm">	<i class='bx bxs-cart-add'></i>Add to Cart</a>
                                            <a href="javascript:;" class="btn btn-light btn-ecomm" data-bs-toggle="modal" data-bs-target="#QuickViewProduct"><i class='bx bx-zoom-in'></i>Quick View</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card rounded-0 product-card">
                            <div class="card-header bg-transparent border-bottom-0">
                                <div class="d-flex align-items-center justify-content-end gap-3">
                                    <a href="javascript:;">
                                        <div class="product-compare"><span><i class='bx bx-git-compare'></i> Compare</span>
                                        </div>
                                    </a>
                                    <a href="javascript:;">
                                        <div class="product-wishlist"> <i class='bx bx-heart'></i>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <a href="product-details.html">
                                <img src="assets/images/products/07.png" class="card-img-top" alt="...">
                            </a>
                            <div class="card-body">
                                <div class="product-info">
                                    <a href="javascript:;">
                                        <p class="product-catergory font-13 mb-1">Catergory Name</p>
                                    </a>
                                    <a href="javascript:;">
                                        <h6 class="product-name mb-2">Product Short Name</h6>
                                    </a>
                                    <div class="d-flex align-items-center">
                                        <div class="mb-1 product-price"> <span class="me-1 text-decoration-line-through">$99.00</span>
                                            <span class="fs-5">$49.00</span>
                                        </div>
                                        <div class="cursor-pointer ms-auto"> <i class="bx bxs-star text-warning"></i>
                                            <i class="bx bxs-star text-warning"></i>
                                            <i class="bx bxs-star text-warning"></i>
                                            <i class="bx bxs-star text-warning"></i>
                                            <i class="bx bxs-star text-light-4"></i>
                                        </div>
                                    </div>
                                    <div class="product-action mt-2">
                                        <div class="d-grid gap-2">
                                            <a href="javascript:;" class="btn btn-dark btn-ecomm">	<i class='bx bxs-cart-add'></i>Add to Cart</a>
                                            <a href="javascript:;" class="btn btn-light btn-ecomm" data-bs-toggle="modal" data-bs-target="#QuickViewProduct"><i class='bx bx-zoom-in'></i>Quick View</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card rounded-0 product-card">
                            <div class="card-header bg-transparent border-bottom-0">
                                <div class="d-flex align-items-center justify-content-end gap-3">
                                    <a href="javascript:;">
                                        <div class="product-compare"><span><i class='bx bx-git-compare'></i> Compare</span>
                                        </div>
                                    </a>
                                    <a href="javascript:;">
                                        <div class="product-wishlist"> <i class='bx bx-heart'></i>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <a href="product-details.html">
                                <img src="assets/images/products/08.png" class="card-img-top" alt="...">
                            </a>
                            <div class="card-body">
                                <div class="product-info">
                                    <a href="javascript:;">
                                        <p class="product-catergory font-13 mb-1">Catergory Name</p>
                                    </a>
                                    <a href="javascript:;">
                                        <h6 class="product-name mb-2">Product Short Name</h6>
                                    </a>
                                    <div class="d-flex align-items-center">
                                        <div class="mb-1 product-price"> <span class="me-1 text-decoration-line-through">$99.00</span>
                                            <span class="fs-5">$49.00</span>
                                        </div>
                                        <div class="cursor-pointer ms-auto"> <i class="bx bxs-star text-warning"></i>
                                            <i class="bx bxs-star text-warning"></i>
                                            <i class="bx bxs-star text-warning"></i>
                                            <i class="bx bxs-star text-warning"></i>
                                            <i class="bx bxs-star text-warning"></i>
                                        </div>
                                    </div>
                                    <div class="product-action mt-2">
                                        <div class="d-grid gap-2">
                                            <a href="javascript:;" class="btn btn-dark btn-ecomm">	<i class='bx bxs-cart-add'></i>Add to Cart</a>
                                            <a href="javascript:;" class="btn btn-light btn-ecomm" data-bs-toggle="modal" data-bs-target="#QuickViewProduct"><i class='bx bx-zoom-in'></i>Quick View</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end row-->
            </div>
        </div>
    </section>
    <!--end Featured product-->

    <!--start News-->
    <section class="py-4">
        <div class="container">
            <div class="d-flex align-items-center">
                <h5 class="text-uppercase mb-0">Latest News</h5>
                <a href="blog.html" class="btn btn-dark ms-auto rounded-0">View All News<i class='bx bx-chevron-right'></i></a>
            </div>
            <hr/>
            <div class="product-grid">
                <div class="latest-news owl-carousel owl-theme">
                    <div class="item">
                        <div class="card rounded-0 product-card border">
                            <div class="news-date">
                                <div class="date-number">24</div>
                                <div class="date-month">FEB</div>
                            </div>
                            <a href="javascript:;">
                                <img src="assets/images/blogs/01.png" class="card-img-top border-bottom" alt="...">
                            </a>
                            <div class="card-body">
                                <div class="news-title">
                                    <a href="javascript:;">
                                        <h5 class="mb-3 text-capitalize">Blog Short Title</h5>
                                    </a>
                                </div>
                                <p class="news-content mb-0">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras non placerat mi. Etiam non tellus sem. Aenean...</p>
                            </div>
                            <div class="card-footer border-top">
                                <a href="javascript:;">
                                    <p class="mb-0"><small>0 Comments</small>
                                    </p>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="card rounded-0 product-card border">
                            <div class="news-date">
                                <div class="date-number">24</div>
                                <div class="date-month">FEB</div>
                            </div>
                            <a href="javascript:;">
                                <img src="assets/images/blogs/02.png" class="card-img-top border-bottom" alt="...">
                            </a>
                            <div class="card-body">
                                <div class="news-title">
                                    <a href="javascript:;">
                                        <h5 class="mb-3 text-capitalize">Blog Short Title</h5>
                                    </a>
                                </div>
                                <p class="news-content mb-0">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras non placerat mi. Etiam non tellus sem. Aenean...</p>
                            </div>
                            <div class="card-footer border-top">
                                <a href="javascript:;">
                                    <p class="mb-0"><small>0 Comments</small>
                                    </p>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="card rounded-0 product-card border">
                            <div class="news-date">
                                <div class="date-number">24</div>
                                <div class="date-month">FEB</div>
                            </div>
                            <a href="javascript:;">
                                <img src="assets/images/blogs/03.png" class="card-img-top border-bottom" alt="...">
                            </a>
                            <div class="card-body">
                                <div class="news-title">
                                    <a href="javascript:;">
                                        <h5 class="mb-3 text-capitalize">Blog Short Title</h5>
                                    </a>
                                </div>
                                <p class="news-content mb-0">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras non placerat mi. Etiam non tellus sem. Aenean...</p>
                            </div>
                            <div class="card-footer border-top">
                                <a href="javascript:;">
                                    <p class="mb-0"><small>0 Comments</small>
                                    </p>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="card rounded-0 product-card border">
                            <div class="news-date">
                                <div class="date-number">24</div>
                                <div class="date-month">FEB</div>
                            </div>
                            <a href="javascript:;">
                                <img src="assets/images/blogs/04.png" class="card-img-top border-bottom" alt="...">
                            </a>
                            <div class="card-body">
                                <div class="news-title">
                                    <a href="javascript:;">
                                        <h5 class="mb-3 text-capitalize">Blog Short Title</h5>
                                    </a>
                                </div>
                                <p class="news-content mb-0">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras non placerat mi. Etiam non tellus sem. Aenean...</p>
                            </div>
                            <div class="card-footer border-top">
                                <a href="javascript:;">
                                    <p class="mb-0"><small>0 Comments</small>
                                    </p>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="card rounded-0 product-card border">
                            <div class="news-date">
                                <div class="date-number">24</div>
                                <div class="date-month">FEB</div>
                            </div>
                            <a href="javascript:;">
                                <img src="assets/images/blogs/05.png" class="card-img-top border-bottom" alt="...">
                            </a>
                            <div class="card-body">
                                <div class="news-title">
                                    <a href="javascript:;">
                                        <h5 class="mb-3 text-capitalize">Blog Short Title</h5>
                                    </a>
                                </div>
                                <p class="news-content mb-0">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras non placerat mi. Etiam non tellus sem. Aenean...</p>
                            </div>
                            <div class="card-footer border-top">
                                <a href="javascript:;">
                                    <p class="mb-0"><small>0 Comments</small>
                                    </p>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="card rounded-0 product-card border">
                            <div class="news-date">
                                <div class="date-number">24</div>
                                <div class="date-month">FEB</div>
                            </div>
                            <a href="javascript:;">
                                <img src="assets/images/blogs/06.png" class="card-img-top border-bottom" alt="...">
                            </a>
                            <div class="card-body">
                                <div class="news-title">
                                    <a href="javascript:;">
                                        <h5 class="mb-3 text-capitalize">Blog Short Title</h5>
                                    </a>
                                </div>
                                <p class="news-content mb-0">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras non placerat mi. Etiam non tellus sem. Aenean...</p>
                            </div>
                            <div class="card-footer border-top">
                                <a href="javascript:;">
                                    <p class="mb-0"><small>0 Comments</small>
                                    </p>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--end News-->


@endsection
