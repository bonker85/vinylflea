$(document).ready(function() {
    $('#summernote, #summernote2').summernote({
        toolbar: [
            // [groupName, [list of button]]
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
            ['view', ['fullscreen', 'codeview']]
        ],
        callbacks: {
            onChange: function(contents, $editable) {
                if (contents) {
                    $editable.parent().parent().css('border', '1px solid #00000032');
                }
            }
        }
    });
    bsCustomFileInput.init();
    $('.select2').select2();

    $('#name','#form_admin_create').blur(function() {
        if ($('#name').val() !== ""){
            $.ajax({
                url: "/admin_panel/ajax/translate_url",
                data: { name: $('#name').val()}
            }).done(function(data) {
                $('#url_translate').text(data);
                $('.translate').removeClass('d-none');
            });
        }
    });
    $('#parent_id', '#form_admin_create').change(function() {
        $('#parent_id option:selected').text($.trim($('#parent_id option:selected').text().replace(/-/g, "")));
    });
    $('#url_translate').click(function() {
        $('#url', '#form_admin_create').val($(this).text());
    });

     $("#kt_docs_jstree_dragdrop").jstree({
        "core" : {
            animation: 1,
            check_callback: true,
            "themes" : {
                "icons": false
            },
            data: {
                url: '/admin_panel/ajax/get_pages_nodes',
                dataType: 'json'
            }
        },
        types: {
            default: {
                icon: 'glyphicon glyphicon-eye-open'
            },
            disabled: {
                icon: 'glyphicon glyphicon-eye-close'
            }
        },
        checkbox: {
            whole_node: false,
            three_state: false
        },
        plugins: ['dnd', 'search', 'wholerow', 'checkbox', 'types']

    }).on('move_node.jstree',function(e, data) {
        if (!$.isNumeric(+data.old_parent)) {
            data.old_parent = 0;
        }
        if (!$.isNumeric(+data.parent)) {
            data.parent = 0;
        }
        $('#ajax_loading').show();
        $.ajax({
            url: "/admin_panel/ajax/update_page_position",
            data: {
                id: +data.node.id,
                old_parent: +data.old_parent,
                new_parent: +data.parent,
                old_position: +data.old_position,
                new_position: +data.position

            }
        }).done(function (data) {
            if (data !== '1') {
                alert("Ошибка сохранения узла");
            }
            $('#ajax_loading').hide();
        })
    }).on('changed.jstree', function (e, data) {
            let destroy_form = $('#destroy_form');
            let edit_link = $('#edit_link');
            if (data.selected.length) {
                $('button', destroy_form).removeClass('disabled')
                let link = destroy_form.attr('data-link') + '/';
                let destroy_ids = data.selected.join(',');
                destroy_form.attr('action', link + destroy_ids)
            } else {
                $('button', destroy_form).addClass('disabled');
            }
            if (data.selected.length === 1) {
                let link = edit_link.removeClass('disabled').attr('data-link').replace('/0', '/');
                console.log(link);
                console.log(link + data.node.id);
                edit_link.attr('href', link + data.node.id)
            } else {
                edit_link.addClass('disabled');
            }
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
                    $('.invalid-summernote').each(function(e){
                        if ($(this).css('display') == 'block') {
                            $(this).parent().find('.note-editor').css('border', '1px solid red');
                        } else {
                            $(this).parent().find('.note-editor').css('border', '1px solid #00000032');
                        }
                    });
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

