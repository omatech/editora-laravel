@if(isset($class['id']))
    @php $class_id = $class['id']  @endphp
@elseif(isset($instance['class_id']))
    @php $class_id = $instance['class_id'] @endphp
@else
    @php $class_id = 0 @endphp
@endif
<nav id="navigation">
    <ul class="level-1-nav">
        <li>
            <a href="{{ route('editora.action', 'get_main') }}">
                <i class="icon-home"></i>
                <span class="link-text">{{getMessage('navigation_home')}}</span>
            </a>
            <ul class="level-2-nav">
                @if(isset($menu))
                    @foreach($menu as $section)
                    @if(isset($section['list']))
                        @php
                            $menu_active='';
                            $menu_collpase='false';
                            $menu_show='';
                        @endphp
                        @foreach($section['list'] as $item)
                            @if ($item['id']==$class_id)
                                @php
                                    $menu_active='active';
                                    $menu_collpase='true';
                                    $menu_show='show';
                                @endphp
                            @endif
                        @endforeach


                        @if(session('user_type')=='O' || $section['lg_cap'] != 'Hidden_Group' )
                            <li>
                                <a href="#subnav-{{$section['id']}}" data-toggle="collapse" aria-expanded="{{$menu_collpase}}" aria-controls="subnav-{{$section['id']}}">
                                    <span class="link-text">{{$section['lg_cap']}}</span>
                                    <i class="icon-chevron-down"></i>
                                </a>
                                <div class="collapse {{$menu_show}}" id="subnav-{{$section['id']}}">
                                    <ul class="level-3-nav">
                                        @foreach($section['list'] as $item)

                                            @if( empty( config('editora-admin.special_classes')) && $item['id']==1){{--global--}}
                                            <li @if($item['id']==$class_id) class="active" @endif>
                                                <a href="{{ route('editora.action', 'view_instance?p_pagina=1&p_class_id='.$item['id'].'&p_inst_id=2') }}" class="link-list">
                                                    <span class="link-text">{{$item['lg_name']}}</span>
                                                </a>
                                            </li>
                                            @elseif(  empty( config('editora-admin.special_classes')) && $item['id']==10){{--home--}}
                                            <li @if($item['id']==$class_id) class="active" @endif>
                                                <a href="{{ route('editora.action', 'view_instance?p_pagina=1&p_class_id='.$item['id'].'&p_inst_id=1') }}" class="link-list">
                                                    <span class="link-text">{{$item['lg_name']}}</span>
                                                </a>
                                            </li>
                                            @else
                                                @if( !empty( config('editora-admin.special_classes') ) && array_key_exists( $item['id'],config('editora-admin.special_classes') ) )
                                                    <li @if($item['id']==$class_id) class="active" @endif>
                                                        <a href="{{ route('editora.action', 'view_instance?p_pagina=1&p_class_id='.$item['id'].'&p_inst_id='.config('editora-admin.special_classes')[$item['id']]) }}" class="link-list">
                                                            <span class="link-text">{{$item['lg_name']}}</span>
                                                        </a>
                                                    </li>
                                                @else
                                                    <li @if($item['id']==$class_id) class="active" @endif>
                                                        <a href="{{ route('editora.action', 'list_instances?p_class_id='.$item['id']) }}" class="link-list">
                                                            <span class="link-text">{{$item['lg_name']}}</span>
                                                        </a>
                                                        <a href="{{ route('editora.action', 'new_instance?p_class_id='.$item['id']) }}" class="link-new">
                                                            <i class="icon-plus-box"></i>
                                                        </a>
                                                    </li>
                                                @endif

                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            </li>
                            @endif
                        @else
                            <li>
                                <a href="">
                                    <span class="link-text">{{$section['lg_cap']}}</span>
                                    <i class="icon-chevron-down"></i>
                                </a>
                            </li>
                        @endif
                    @endforeach
                @endif
            </ul>
        </li>
        <li @if(last(request()->segments())=='static_text')class="active" @endif>
            <a href="{{ route('editora.action', 'static_text') }}">
                <i class="icon-settings"></i>
                <span class="link-text">{{getMessage('static_text')}}</span>
            </a>
        </li>
        <li>
            <ul class="level-2-nav">
                @if( in_array( last(request()->segments()) , ['unlinked_images', 'create_users', 'list_class', 'urls_broken'] )  )
                    @php
                        $menu_active='active';
                        $menu_collpase='true';
                        $menu_show='show';
                    @endphp
                @else
                    @php
                        $menu_active='';
                        $menu_collpase='false';
                        $menu_show='';
                    @endphp
                @endif
                <li>
                    <a href="#subnav-config" data-toggle="collapse" aria-expanded="{{$menu_collpase}}" aria-controls="subnav-config">
                        <span class="link-text">{{getMessage('special_functions')}}</span>
                        <i class="icon-chevron-down"></i>
                    </a>
                    <div class="collapse {{$menu_show}}" id="subnav-config">
                        <ul class="level-3-nav">
                            <li @if(last(request()->segments())=='unlinked_images')class="active" @endif>
                                <a href="{{ route('editora.action', 'unlinked_images') }}" class="link-list">
                                    <span class="link-text">{{getMessage('unlinked_files')}}</span>
                                </a>
                            </li>
                            <li @if(last(request()->segments())=='urls_broken')class="active" @endif>
                                <a href="{{ route('editora.action', 'urls_broken') }}" class="link-list">
                                    <span class="link-text">{{getMessage('urls_broken')}}</span>
                                </a>
                            </li>
                            @if(session('user_type')=='O' && session('rol_id')==1 )
                                <li @if(last(request()->segments())=='create_users')class="active" @endif>
                                    <a href="{{ route('editora.action', 'create_users') }}" class="link-list">
                                        <span class="link-text">{{getMessage('create_users')}}</span>
                                    </a>
                                </li>
                                <li @if(last(request()->segments())=='list_class')class="active" @endif>
                                    <a href="{{ route('editora.action', 'list_class') }}" class="link-list">
                                        <span class="link-text">{{getMessage('load_content')}}</span>
                                    </a>
                                </li>
                                <li @if(last(request()->segments())=='editora_info')class="active" @endif>
                                    <a href="{{ route('editora.action', 'editora_info') }}" class="link-list">
                                        <span class="link-text">{{getMessage('editora_info')}}</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            </ul>
        </li>
        @includeIf('editora::extraMenu')
    </ul>
</nav>
