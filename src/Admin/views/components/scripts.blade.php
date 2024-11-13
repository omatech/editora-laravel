<script type="importmap">
    {
        "imports": {
            "ckeditor5": "{{ asset('/vendor/editora/js/ckeditor5/ckeditor5.js') }}",
            "ckeditor5/": "{{ '/vendor/editora/js/ckeditor5/' }}"
        }
    }
</script>
<script type="module" src="{{ asset('/vendor/editora/js/ckeditor5/ckeditor5.js') }}"></script>
<script type="module" src="{{ asset('/vendor/editora/js/ckeditor5/main.js') }}"></script>

@if(env('GOOGLE_API_KEY'))
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_API_KEY')}}"></script>
@endif
<script type="text/javascript" src="{{ asset('/vendor/editora/js/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('/vendor/editora/js/popper.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('/vendor/editora/js/bootstrap.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('/vendor/editora/js/dropzone.js') }}"></script>
<script type="text/javascript" src="{{ asset('/vendor/editora/js/color-picker.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('/vendor/editora/js/datatables.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('/vendor/editora/js/maps.js') }}"> </script>
<script type="text/javascript" src="{{ asset('/vendor/editora/js/cropper.min.js') }}"> </script>
<script type="text/javascript" src="{{ asset('/vendor/editora/js/auto-complete.min.js') }}"> </script>
<script type="text/javascript" src="{{ asset('/vendor/editora/js/jquery-ui.min.js') }}"> </script>
<script type="text/javascript" src="{{ asset('/vendor/editora/js/bootstrap-datepicker.js') }}"> </script>

<script>
    function showMenuInLargeDevices() {
        var screenWidth = window.innerWidth;
        if(screenWidth >= 1674 &&  $('body').hasClass('hide-navigation')) {
            $('body').toggleClass('show-navigation hide-navigation');
        }
    }

    $(document).ready(function() {
        showMenuInLargeDevices();
        $(window).resize(function() {
            showMenuInLargeDevices();
        });
        $('.datepicker').datepicker();
        $('#btn-toggle-navigation').on('click', function(){
            $('body').toggleClass('show-navigation hide-navigation');
        })
        $('#btn-toggle-favorites').on('click', function(){
            $('body').toggleClass('show-favorites hide-favorites');
            $('body').removeClass('show-modifications');
            $('body').addClass('hide-modifications');
            $('body').removeClass('show-relations');
            $('body').addClass('hide-relations');
        })
        $('#btn-hide-favorites').on('click', function(){
            $('body').removeClass('show-favorites').addClass('hide-favorites');
        })
        $('#btn-toggle-modifications').on('click', function(){
            $('body').toggleClass('show-modifications hide-modifications');
            $('body').removeClass('show-favorites');
            $('body').addClass('hide-favorites');
            $('body').removeClass('show-relations');
            $('body').addClass('hide-relations');
        })
        $('#btn-hide-modifications').on('click', function(){
            $('body').removeClass('show-modifications').addClass('hide-modifications');
        })
        $('#btn-toggle-relations').on('click', function(){
            $('body').toggleClass('show-relations hide-relations');
            $('body').removeClass('show-favorites');
            $('body').addClass('hide-favorites');
            $('body').removeClass('show-modifications');
            $('body').addClass('hide-modifications');
        })
        $('#btn-hide-relations').on('click', function(){
            $('body').removeClass('show-relations').addClass('hide-relations');
        })

        $('[data-toggle="popover"]').popover({
            container: 'body',
            html: true
        });
        $('.btn-favorite input[type="checkbox"]').change(function () {
            if ($(this).is(':checked')) {
                $(this).closest('tr').addClass('favorited');
            } else {
                $(this).closest('tr').removeClass('favorited');
            }
        });

        activeSortable();
        $('.alert').bind('click', function () {
            $(this).addClass('hidden');
        });

        $('.toast').toast({
            animation: true,
            autohide: false,
            delay: 10000
        });
        $('.toast').toast('show');
    });

    function activeSortable() {
        var fixHelper = function(e, ui) {
            ui.children().each(function() {
                $(this).width($(this).width());
            });
            return ui;
        };

        $("[id^=divrel] tbody").sortable({
            axis: 'y',
            create: function(event, ui ) {},
            helper: fixHelper,
            update: function (e) {
                parent = $(this).parents('[id^=divrel]').attr('data-instid');
                var list = $(this).parent().parent().parent().find('.alert');
                save_list(parent, $(this).sortable('toArray'), list);
            }
        }).disableSelection();
    }

    function reldelete(link) {
        var relid = link.substring(link.indexOf('p_rel_id=')+9);
        $.ajax({
            url: link,
            type: "GET",
            dataType: "html",
            success: function(html) {
                $("#relation-"+relid).html(html);
                activeSortable();
            }
        });
    }

    function favdelete(class_id, inst_id) {
        var params = 'p_pagina=1&p_class_id='+class_id+'&p_inst_id='+inst_id;
        var link = '{!! route('editora.action', 'delete_favorite') !!}'
        $.ajax({
            url: link,
            type: "GET",
            data: params,
            dataType: "html",
            success: function(html) {
                $("#favorites-menu-list").html(html);
            }
        });
    }

    function make_magic(code, info, return_to, list) {
        info = 'ajax='+code+'&'+info;
        $.ajax({
            url: '{{route('editora.action', 'ajax_actions')}}',
            type: "GET",
            data: info,
            beforeSend: function (jqXHR, settings) {
                list.removeClass('alert_error alert_right hidden').addClass('saving');
                $('.alert.list div p').html('Saving ...');
            },
            success: function(data) {
                if(data != '') {
                    $(return_to).html(data);
                    list.removeClass('saving').addClass('alert_right');
                } else {
                    $(return_to).html(data);
                    list.removeClass('saving').addClass('alert_error');
                }
            }
        });
    }
    function save_list(instance_id, sortable, list) {
        var info = 'instance_id=' + instance_id + '&ordered=' + sortable;
        make_magic('ajax_order', info, '.alert.list div p', list);
    }
</script>