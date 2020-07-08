<nav class="breadcrumbs">
    <ul>
        @if(isset($instance))
            <li>
                <a href="{{ route('editora.get_main') }}">{{ getMessage('navigation_home') }}</a>
            </li>
            <li class="{{ isset($instance['key_fields']) ?: 'active' }}">
                <a href="{{ route('editora.list_instances', ['class_id'=>$instance['class_id']]) }}">{{ getClassName($instance['class_id'] )}}</a>
            </li>
            @if(isset($instance['key_fields']))
                <li class="active">{{ $instance['key_fields'] }}</li>
            @endif
        @else
            <li class="active">
                <a href="{{route('editora.get_main')}}">{{getMessage('navigation_home')}}</a>
            </li>
        @endif
    </ul>
</nav>