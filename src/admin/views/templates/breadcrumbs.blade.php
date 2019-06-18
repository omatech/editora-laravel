<nav class="breadcrumbs">
    <ul>
        @if(isset($instance))
            @if(isset($instance['class_name']))
                <li><a href="{{route('editora.action', "get_main")}}">Inicio</a></li>
                <li><a href="{{ route('editora.action', 'list_instances?p_class_id='.$instance['class_id']) }}">{{getClassName($instance['class_id'])}}</a></li>
                <li class="active">{{$instance['key_fields']}}</li>
            @endif
        @else
            <li class="active"><a href="{{route('editora.action', "get_main")}}">Inicio</a></li>
        @endif
    </ul>
</nav>