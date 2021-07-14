<header id="topbar" class="container-fluid">
    <span class="topbar-left">
        <button class="btn-square clr-default" id="btn-toggle-navigation">
            <i class="icon-menu"></i>
        </button>
        <a href="{{ route('editora.action', 'get_main') }}" class="logo">
            <img src="{{ asset('/vendor/editora/img/omalogo-sm.png') }}" alt="Inicio" class="logo-sm">
            <img src="{{ asset('/vendor/editora/img/omalogo.png') }}" alt="Inicio" class="logo-lg">
        </a>
        @include('editora::templates.breadcrumbs')
    </span>
    <ul class="topbar-right">
        <li class="dropdown search">
            <button class="btn-square clr-transparent" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="icon-magnify"></i>
            </button>
            <div class="dropdown-menu">
                <form id="formsearch" action="{{ route('editora.action', 'search') }}">
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
                <a class="dropdown-item" href="{{route('editora.action', 'configure')}}">
                    <i class="icon-information-outline"></i>
                    <span class="dd-text">{{getMessage('settings')}}</span>
                </a>
                <a class="dropdown-item" href="{{route('editora.action', 'logout')}}">
                    <i class="icon-power"></i>
                    <span class="dd-text">{{getMessage('info_word_logout')}}</span>
                </a>
            </div>
        </li>
    </ul>
</header>