<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Cache-Control" content="no-cache" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Editora - {{$title}}</title>

    <link href="{{ asset('/vendor/editora/img/favicon.ico') }}" rel="shortcut icon" />

    <link rel="stylesheet" type="text/css" href="{{ asset('/vendor/editora/css/editora.css') }}?v=1" >
    <link rel="stylesheet" type="text/css" href="{{ asset('/vendor/editora/css/dropzone.css') }}" >
    <link rel="stylesheet" type="text/css" href="{{ asset('/vendor/editora/css/color-picker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('/vendor/editora/css/datatables.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('/vendor/editora/css/cropper.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('/vendor/editora/css/auto-complete.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('/vendor/editora/css/datepicker.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('/vendor/editora/css/font-awesome/css/font-awesome.css') }}"/>

    <link rel="stylesheet" type="text/css" href="{{ asset('/vendor/editora/css/extras.css') }}" >
</head>
<body class="hide-navigation hide-favorites hide-modifications hide-relations @if(isset($body_class)) {{$body_class}} @endif">
    <header id="topbar" class="container-fluid">
        <span class="topbar-left">
            <button class="btn-square clr-default" id="btn-toggle-navigation">
                <i class="icon-menu"></i>
            </button>
            <a href="{{ route('editora.get_main') }}" class="logo">
                <img src="{{ asset('/vendor/editora/img/omalogo-sm.png') }}" alt="Inicio" class="logo-sm">
                <img src="{{ asset('/vendor/editora/img/omalogo.png') }}" alt="Inicio" class="logo-lg">
            </a>
            {{-- @include('editora::templates.breadcrumbs') --}}
        </span>
    <ul class="topbar-right">
        <li class="dropdown search">
            <button class="btn-square clr-transparent" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="icon-magnify"></i>
            </button>
            <div class="dropdown-menu">
                <form id="formsearch" action="{{ route('editora.search') }}">
                        <span class="input-group">
                            <input type="text" class="form-control" id="p_search_query" name="p_search_query" placeholder="Buscar">
                            <a href="#" onclick="document.getElementById('formsearch').submit(); return false;" class="input-addon"><i class="icon-magnify"></i></a>
                        </span>
                </form>
            </div>
        </li>
        <li class="modifications">
            <button class="btn-square clr-default" id="btn-toggle-modifications">
                <i class="icon-clock"></i>
            </button>
        </li>
        <li class="favorites">
            <button class="btn-square clr-default" id="btn-toggle-favorites">
                <i class="icon-star"></i>
            </button>
        </li>
        <li class="dropdown user">
            <button class="btn-square clr-default" data-toggle="dropdown" aria-expanded="false">
                <i class="icon-account"></i>
            </button>

            <button class="btn clr-default" data-toggle="dropdown" aria-expanded="false">
                <i class="icon-account"></i>
                <span class="btn-text">{{session('user_nom')}}</span>
                <i class="icon-dots-vertical"></i>
            </button>

            <div class="dropdown-menu">
                <a class="dropdown-item" href="{{route('editora.configure')}}">
                    <i class="icon-information-outline"></i>
                    <span class="dd-text">{{__('editora_lang::messages.settings')}}</span>
                </a>
                <a class="dropdown-item" href="{{route('editora.logout')}}">
                    <i class="icon-power"></i>
                    <span class="dd-text">{{__('editora_lang::messages.info_word_logout')}}</span>
                </a>
            </div>
        </li>
    </ul>
</header>

@include('editora::templates.main_menu')
<div id="favorites-menu">
    <header class="side-menu-header">
        <span class="tit">Mis favoritos</span>
        <button class="btn-square clr-gray" id="btn-hide-favorites"><i class="icon-close"></i></button>
    </header>
    <div class="side-menu-content" id="favorites-menu-list">
        @include('editora::templates.favorites_menu')
    </div>
</div>
@include('editora::templates.parents_menu')
@include('editora::templates.last_accessed_menu')

@yield('body')

@if(isset($message) && $message!=null)
{!!$message!!}
@endif

<footer id="footer">
    <div class="container">
        <p class="copy">Powered by <strong>Omatech</strong></p>
        <nav class="footer-nav">
            <ul>
                <li><a href="http://www.omatech.com" title="www.omatech.com">www.omatech.com</a></li>
                <li><a href="mailto:info@omatech.com" title="info@omatech.com">info@omatech.com</a></li>
                <li><span>93 219 77 63</span></li>
            </ul>
        </nav>
    </div>
</footer>

@section('modals')
@show
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_API_KEY')}}"></script>
<script type="text/javascript" src="{{ asset('/vendor/editora/js/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('/vendor/editora/js/popper.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('/vendor/editora/js/bootstrap.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('/vendor/editora/js/ckeditor/ckeditor.js') }}"></script>
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

        // Animar menus
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

        // Popovers
        $('[data-toggle="popover"]').popover({
            container: 'body',
            html: true
        });
        // Add favorited class to table row
        $('.btn-favorite input[type="checkbox"]').change(function () {
            if ($(this).is(':checked')) {
                $(this).closest('tr').addClass('favorited');
            } else {
                $(this).closest('tr').removeClass('favorited');
            }
        });


        //drag and drop relations
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
        var link = '{!! route('editora.delete_favorite') !!}'
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

    //drag and drop relations
    function make_magic(code, info, return_to, list) {
        info = 'ajax='+code+'&'+info;

        $.ajax({
            url: '{{route('editora.ajax_actions')}}',
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

@section('scripts')
@show
</body>
</html>
