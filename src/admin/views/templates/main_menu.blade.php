<nav id="navigation">
    <ul class="level-1-nav">
        <li>
            <a href="{{ route('editora.action', 'get_main') }}">
                <i class="icon-home"></i>
                <span class="link-text">Inicio</span>
            </a>
            <ul class="level-2-nav">
                @foreach($menu as $section)
                    @if(isset($section['list']))
                        <li>
                            <a href="#subnav-{{$section['id']}}" data-toggle="collapse" aria-expanded="false" aria-controls="subnav-{{$section['id']}}">
                                <span class="link-text">{{$section['lg_cap']}}</span>
                                <i class="icon-chevron-down"></i>
                            </a>
                            <div class="collapse" id="subnav-{{$section['id']}}">
                                <ul class="level-3-nav">
                                    @foreach($section['list'] as $item)
                                        @if($item['id']==1){{--global--}}
                                            <li>
                                                <a href="{{ route('editora.action', 'view_instance/?p_pagina=1&p_class_id='.$item['id'].'&p_inst_id=2') }}" class="link-list">
                                                    <span class="link-text">{{$item['lg_name']}}</span>
                                                </a>
                                            </li>
                                        @elseif($item['id']==10){{--home--}}
                                            <li>
                                                <a href="{{ route('editora.action', 'view_instance/?p_pagina=1&p_class_id='.$item['id'].'&p_inst_id=1') }}" class="link-list">
                                                    <span class="link-text">{{$item['lg_name']}}</span>
                                                </a>
                                            </li>
                                        @else
                                            <li>
                                                <a href="{{ route('editora.action', 'list_instances/?p_class_id='.$item['id']) }}" class="link-list">
                                                    <span class="link-text">{{$item['lg_name']}}</span>
                                                </a>
                                                <a href="{{ route('editora.action', 'new_instance/?p_class_id='.$item['id']) }}" class="link-new">
                                                    <i class="icon-plus-box"></i>
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </li>
                    @else
                        <li>
                            <a href="">
                                <span class="link-text">{{$section['lg_cap']}}</span>
                                <i class="icon-chevron-down"></i>
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </li>
        <li>
            <a href="{{ route('editora.action', 'static_text') }}">
                <i class="icon-settings"></i>
                <span class="link-text">Textos estáticos</span>
            </a>
        </li>
        <li>
            <ul class="level-2-nav">
                <li>
                    <a href="#subnav-config" data-toggle="collapse" aria-expanded="false" aria-controls="subnav-config">
                        <span class="link-text">Funciones especiales</span>
                        <i class="icon-chevron-down"></i>
                    </a>
                    <div class="collapse" id="subnav-config">
                        <ul class="level-3-nav">
                            <li>
                                <a href="{{ route('editora.action', 'unlinked_images') }}" class="link-list">
                                    <span class="link-text">Ficheros no relacionados</span>
                                </a>
                            </li>
                            @if($_SESSION['user_type']=='O' && $_SESSION['rol_id']==1 )
                                <li>
                                    <a href="{{ route('editora.action', 'create_users') }}" class="link-list">
                                        <span class="link-text">Crear usuarios</span>
                                    </a>
                                </li>
                            @endif

                            @if($_SESSION['user_type']=='O' && $_SESSION['rol_id']==1 )
                                <li>
                                    <a href="{{ route('editora.action', 'list_class') }}" class="link-list">
                                        <span class="link-text">Carga excel contenido</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>

            </ul>
        </li>
    </ul>
</nav>