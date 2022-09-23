$(document).ready(function() {
    document.addEventListener('click', outsideEvtListener);

    function outsideEvtListener(evt) {
        const el = document.querySelector('.block-icons');
        if (evt.target === el || el.contains(evt.target)) {
            // клик внутри
            return;
        }
        // код для закрытия меню, например el.classList.add('hidden')
        if ($('.block-icons .profile-menu').css('display') == 'block') {
            $('.block-icons .profile-menu').css('display', 'none');
        }
    }
    $('.dropdown-menu a.dropdown-toggle').on('click', function(e) {
        if (!$(this).next().hasClass('show')) {
            $(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
        }
        var $subMenu = $(this).next(".dropdown-menu");
        $subMenu.toggleClass('show');


        $(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function(e) {
            $('.submenu .show').removeClass("show");
        });


        return false;
    });
    $(window).on("scroll", function() {
        $(this).scrollTop() > 300 ? $(".back-to-top").fadeIn() : $(".back-to-top").fadeOut()
    }), $(".back-to-top").on("click", function() {
        return $("html, body").animate({
            scrollTop: 0
        }, 600), !1
    })
    $("[data-trigger]").on("click", function(e){
        e.preventDefault();
        e.stopPropagation();
        var offcanvas_id =  $(this).attr('data-trigger');
        $(offcanvas_id).toggleClass("show");
        $('body').toggleClass("offcanvas-active");
        $(".screen-overlay").toggleClass("show");
    });

    // Close menu when pressing ESC
    $(document).on('keydown', function(event) {
        if(event.keyCode === 27) {
            $(".mobile-offcanvas").removeClass("show");
            $("body").removeClass("overlay-active");
        }
    });

    $(".btn-close, .screen-overlay").click(function(e){
        $(".screen-overlay").removeClass("show");
        $(".mobile-offcanvas").removeClass("show");
        $("body").removeClass("offcanvas-active");
    });
    $('.latest-news').owlCarousel({
        loop:true,
        margin:10,
        responsiveClass:true,
        nav:false,
        dots: false,
        responsive:{
            0:{
                items:1
            },
            600:{
                items:2
            },
            1024:{
                items:3
            },
            1366:{
                items:4
            }
        }
    });
    $('.product-gallery').owlCarousel({
        loop:true,
        margin:10,
        responsiveClass:true,
        nav:false,
        dots: false,
        thumbs: true,
        thumbsPrerendered: true,
        responsive:{
            0:{
                items:1
            },
            600:{
                items:1
            },
            1000:{
                items:1
            }
        }
    })
    $('#logout_link').on("click", function() {
        $('#logout_form').submit();
    });

    $("#show_hide_password a").on('click', function (event) {
        event.preventDefault();
        if ($('#show_hide_password input').attr("type") == "text") {
            $('#show_hide_password input').attr('type', 'password');
            $('#show_hide_password i').addClass("bx-hide");
            $('#show_hide_password i').removeClass("bx-show");
        } else if ($('#show_hide_password input').attr("type") == "password") {
            $('#show_hide_password input').attr('type', 'text');
            $('#show_hide_password i').removeClass("bx-hide");
            $('#show_hide_password i').addClass("bx-show");
        }
    });
    $('.avatar-img').on('click', function() {
        $('.block-icons .profile-menu').toggle();
    });
    $("#js-file-avatar").change(function(){
        if (window.FormData === undefined) {
            alert('В вашем браузере FormData не поддерживается')
        } else {
            $('#img-avatar').addClass('d-none');
            $('.spinner-border').removeClass('d-none');
            var formData = new FormData();
            formData.append('avatar', $("#js-file-avatar")[0].files[0]);

            $.ajax({
                type: "POST",
                url: '/ajax/avatar_file',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                dataType : 'json',
                success: function(data){
                    if (data.error == '') {
                        $('#img-avatar').attr('src', data.url);
                    } else {
                        console.log('Error');
                    }
                    $('#img-avatar').removeClass('d-none');
                    $('.spinner-border').addClass('d-none');
                }
            });
        }
    });
    $(".profile-info-formblock #phone").inputmask({"mask": "+375 (99) 999-99-99"});
    $('.select2').select2();

    (function() {
        'use strict';
        window.addEventListener('load', function() {
            // Получите все формы, к которым мы хотим применить пользовательские стили проверки Bootstrap
            var forms = document.getElementsByClassName('needs-validation');
            // Зацикливайтесь на них и предотвращайте подчинение
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    let errors = false;
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                        errors = true;
                    }
                    form.classList.add('was-validated');
                  /*  $('.invalid-summernote').each(function(e){
                        if ($(this).css('display') == 'block') {
                            $(this).parent().find('.note-editor').css('border', '1px solid red');
                        } else {
                            $(this).parent().find('.note-editor').css('border', '1px solid #00000032');
                        }
                    });*/
                    if (errors) {
                        $('html, body').animate({
                            scrollTop: $($(':invalid')[1]).offset().top - 20 // класс объекта к которому приезжаем
                        }, 500); // Скорость прокрутки
                    }
                }, false);
            });
        }, false);
    })();
});
