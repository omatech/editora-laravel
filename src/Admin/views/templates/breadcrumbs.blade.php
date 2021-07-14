<nav class="breadcrumbs">
    <ul>
        @if(isset($instance))
            <li>
                <a href="{{ route('editora.action', "get_main") }}">{{ getMessage('navigation_home') }}</a>
            </li>
            @isset($instance['class_id'])
                <li class="{{ isset($instance['key_fields']) ?: 'active' }}">
                    <a href="{{ route('editora.action', 'list_instances?p_class_id='.$instance['class_id']) }}">{{ getClassName($instance['class_id'] )}}</a>
                </li>
            @endisset
            @isset($instance['key_fields'])
                <li class="active">{{ $instance['key_fields'] }}</li>
            @endisset
        @else
            <li class="active">
                <a href="{{route('editora.action', "get_main")}}">{{getMessage('navigation_home')}}</a>
            </li>
        @endif
    </ul>
</nav>