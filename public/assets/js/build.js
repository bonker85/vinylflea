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
    if ($('.latest-news').length) {
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
    }


    $('.logout_link').on("click", function() {
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
    $('.del-vinyl-img').on('click', function (e) {
        const id = $(this).attr('id').replace('del-vinyl-img', '');
        const file = $('#' + $(this).attr('id').replace('del-vinyl-img','img-vinyl-')).attr('src');
        const extStart = file.substr( (file.lastIndexOf('.') +1));
        let ext = '';
        let image = '';
        if (extStart.indexOf('?') === -1) {
             ext = extStart;
             image = $('#del-vinyl-img' + id).attr('data-image');
        } else {
             ext = extStart.substr(0, extStart.indexOf('?'));

        }
        const imgElement = $('#img-vinyl-' + id);
        $.ajax({
            type: "GET",
            url: '/ajax/vinyl_delete?id='+id + '&ext=' + ext + '&image=' + image,
            success: function(data){
                if (data.error == '') {
                    imgElement.removeClass('vinyl-img');
                    imgElement.addClass('no-vinyl-img');
                    $('#vinyl' + id).val('');
                    imgElement.attr('src', data.url);
                    $('#del-vinyl-img' + id).hide();
                    $('.error_message').hide();
                    $('.error_message').text('');
                } else {
                    $('.error_message').text(data.error);
                    $('.error_message').show();
                }
            }
        });
    });
    $("#js-file-vinyl").change(function() {
        if (window.FormData === undefined) {
            alert('В вашем браузере FormData не поддерживается')
        } else {
            for (let i=0;i<4;i++) {
                let el = $('.vinyl-img-block .vinyl-set')[i];
                let imgElement = $('img', el);
                if (imgElement.hasClass('no-vinyl-img')) {
                    imgElement.removeClass('no-vinyl-img');
                    imgElement.addClass('vinyl-img');
                    imgElement.addClass('d-none');
                    $('.spinner-border', el).removeClass('d-none');
                    $('.add-advert-button').attr('disabled', true);
                    var formData = new FormData();
                    let num = i+1;
                    formData.append('vinyl' + num, $("#js-file-vinyl")[0].files[0]);
                    formData.append('id', num);
                    $.ajax({
                        type: "POST",
                        url: '/ajax/vinyl_file',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: formData,
                        dataType : 'json',
                        success: function(data){
                            if (data.error == '') {
                                $('.error_message').hide();
                                $('.error_message').text('');
                                $('#del-vinyl-img' + num).addClass('bx-x').show();
                                $('#vinyl' + num).val(data.url);
                                imgElement.attr('src', data.url);
                            } else {
                                $('#vinyl' + num).val('');
                                imgElement.removeClass('vinyl-img');
                                imgElement.addClass('no-vinyl-img');
                                $('.error_message').text(data.error);
                                $('.error_message').show();
                            }
                            imgElement.removeClass('d-none');
                            $('.spinner-border', el).addClass('d-none');
                            $('.add-advert-button').attr('disabled', false);
                        }
                    });
                    $('#js-file-vinyl').val('');
                    return true;
                }
            }
        }
        $('#js-file-vinyl').val('');
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
                        $('.error_message').hide();
                        $('.error_message').text('');
                        $('#img-avatar').attr('src', data.url);
                    } else {
                        $('.error_message').text(data.error);
                        $('.error_message').show();
                    }
                    $('#img-avatar').removeClass('d-none');
                    $('.spinner-border').addClass('d-none');
                }
            });
        }
    });
    $(".profile-info-formblock #phone").inputmask({"mask": "+375 (99) 999-99-99"});
    $(".profile-add_advert-formblock #year").inputmask({"mask": "9999"});
    $('.select2').select2();
    $(".profile-info-formblock #phone").on('change', function() {
        alert($(this).val());
    });
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
    $('.profile-add_advert-formblock select[name=status]').on('change', function(e) {
        if ($(this).val() == 3) {
            $('#reject_message_block').show();
        } else {
            $('#reject_message_block').hide();
        }
    });

    $('.profile-add_advert-formblock #deal').on('change', function(e) {
        if ($(this).val() == 'sale') {
            if ($('#price').val() == 0) {
                $('#price').val('');
            }
            $('.deal-sale').show();
        } else {
            $('.deal-sale').hide();
            if (!$('#price').val()) {
                $('#price').val(0);
            }
        }
    });
    $('.button-phone').on('click', function() {
        const id = $(this).data('advert');
        const user_id = $(this).data('user');
        const data = {params: ''};
        if (id) {
            data.params = 'advert='+id;
        } else if (user_id) {
            data.params = 'user_id='+user_id;
        } else {
            return false;
        }
        $.ajax({
            type: "GET",
            url: '/ajax/show_phone?' + data.params,
            success: function(data){
                if (data.error == '') {
                    $('.button-phone').text(data.phone).css('cursor', 'unset').off();
                }
            }
        });
    });

    $('#message-modal').on('show.bs.modal', function (event) {
        $('.modal-body p.alert-danger').hide();
        let button = $(event.relatedTarget)
        $('#messageModalLabel').text($(button).attr('data-name'));
        $('#advert_id').val($(button).attr('data-id'));
       // $('.modal-pay-button').html($(button).attr('data-button'));
    });

    $('.modal-message-button').on('click', function() {
        if (!$('#message-modal #message').val()) {
            $('.modal-body p.alert-danger').text("Поле сообщения не заполено.").show();
            return false;
        }

        $.ajax({
            url: "/ajax/front-message",
            method: 'POST',
            data: { message: $('#message').val(), id: $('#advert_id').val(), _token: $('.modal input[name=_token]').val()}
        }).done(function(data) {
            if (data.error) {
                $('.modal-body p.alert-danger').text("Произошла ошибка. Сообщение не отправлено.").show();
            } else {
                $('#message-modal').modal('hide');
                location.href='/profile/messages/' + data.dialog_id;
            }
        });
    });
    $('.advert-show-list').on('click', function() {
        //$(this).hide();
        if ($('.advert-mess-list').css('display') == 'block') {
            $('.advert-mess-list').hide();
            $('.advert-mess-h', this).text('Показать все сообщения');
            $(this).css('border-bottom', '');
        } else {
            $(this).css('border-bottom', '1px solid #DEE2E6');
            $('.advert-mess-h', this).text('Скрыть все сообщения');
            $('.advert-mess-list').show();
        }
    });
    if (document.querySelector('.messages-block')) {
        document.querySelector('.messages-block').scrollTop = 100000;
    }
    $('#message_form').on('submit', function(e) {
        e.preventDefault();
        if (!$('.message-textarea').val()) {
            $('.message-textarea').css('border', '1px solid #d22f2f');
            return false;
        } else {
            $('.message-textarea').css('border', 'unset');
        }
        $.ajax({
            url: $('#message_form').attr('action'),
            method: 'POST',
            data: { message: $('.message-textarea').val(), id: $('#advert_id').val(), _token: $('input[name=_token]',$(this)).val()}
        }).done(function(data) {
            if (!data.error) {
                if (data.chat) {
                    $('.chat').html(data.chat);
                    $('.message-textarea').val('');
                    document.querySelector('.messages-block').scrollTop = 100000;
                }
            }
        });
    });
    $('.search')
        .search({
            apiSettings: {
                url: '/ajax/search?q={query}',
            },
            error: {
                noResults: 'Ваш запрос не дал результатов'
            },
            fields: {
                results : 'items',
                title   : 'name',
                url     : 'url'
            },
            minCharacters : 3,
            maxResults: 20
        });
    $('.favorit-link').on('click', function(e) {
        const advert_id = $(this).attr('data-advert');
        $.ajax({
            url: "/ajax/favorit",
            method: 'GET',
            data: { user_id: $(this).attr('data-user'), advert_id: advert_id}
        }).done(function(data) {
            if (data.res) {
                $('.favorit-link[data-advert="'+advert_id+'"] i').removeClass('bx-heart').addClass('bxs-heart');
            } else {
                $('.favorit-link[data-advert="'+advert_id+'"] i').removeClass('bxs-heart').addClass('bx-heart');
            }
        });
    });
});
