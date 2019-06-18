@if($p_mode=='R')
    @php($id_rel=0)
    <form class="form" id="relation_all" name="relation_all" method="post" enctype="multipart/form-data" action="{{route('editora.action', 'join_all')}}">
        {{ csrf_field() }}
        <button class="btn clr-secondary">Relacionar</button>
        <input type="hidden" name="p_pagina" value="1"/>
        <input type="hidden" name="p_class_id" value="{{$parent['class_id']}}"/>
        <input type="hidden" name="p_relation_id" value="{{$parent['rel_id']}}"/>
        <input type="hidden" name="p_inst_id" value="{{$parent['inst_id']}}"/>
        <input type="hidden" name="p_parent_inst_id" value="{{$parent['inst_id']}}"/>
        <input type="hidden" name="p_parent_class_id" value="{{$parent['class_id']}}"/>
        <input type="hidden" name="p_multiple" value="1"/>
        <input type="hidden" name="p_tab" value="1" class="input_tabs"/>
@endif
<table class="table main-table" id="pages-table">
    <thead>
    <tr>
        <th class="status"><span>Estado</span></th>
        <th class="id"><span>ID</span></th>
        <th class="picture"><span>Img.</span></th>
        <th class="tit"><span>Clave</span></th>
        <th class="type"><span>Tipo</span></th>
        <th class="creation-date"><span>Fecha creación</span></th>
        <th class="date-condition"><span class="hidden">Condición de Fecha</span></th>
        <th class="favorite"><span class="hidden">Acciones</span></th>
    </tr>
    </thead>
    <tbody>
    @if(isset($instances))
         @foreach($instances as $item)
             @php($link=route('editora.action', 'view_instance/?p_pagina=1&p_class_id='.$item['class_id'].'&p_inst_id='.$item['id']))
            <tr>
                <td class="status">
                    <button class="btn-square">
                        {!! _instanceStatus($item['status']) !!}
                    </button>
                </td>
                <td class="id">
                    @if($p_mode=='R')
                       <span class="btn-favorite">
                            <input type="checkbox" id="rel_chb_{{$id_rel}}" name="rel_chb[]" id="table-add-{{$item['id']}}" value="{{$item['id']}}">
                        </span>
                        @php($id_rel++)
                    @endif
                    <a href="{!! $link !!}">{{$item['id']}}</a>
                </td>
                <td class="picture">
                    <figure class="pic">{!! getListImage($item['id']) !!}</figure>
                </td>
                <td class="tit"><a href="{!! $link !!}">{{$item['key_fields']}} </a></td>
                <td class="type">{{$item['class_realname']}}</td>
                <td class="creation-date"><time class="date">{{$item['creation_date']}}</time></td>
                <td class="date-condition">
                    <button class="btn-square {!! _activeDate($item['publishing_begins'], $item['publishing_ends']) !!}" data-toggle="popover" data-placement="top" data-content="
                    @if($item['publishing_ends']!=null)
                        <p>Publicada del <time>{{$item['publishing_begins']}}</time> al <time>{{$item['publishing_ends']}}</time></p>
                    @else
                        <p>Publicada el <time>{{$item['publishing_begins']}}</time></p>
                    @endif
                    ">
                        <i class="icon-calendar-multiple"></i>
                    </button>
                </td>

                @if($p_mode=='R')
                    @php($link=route('editora.action', 'join2/?p_pagina=1&p_relation_id='.$parent['rel_id'].'&p_parent_class_id='.$parent['class_id'].'&p_parent_inst_id='.$parent['inst_id'].'&p_child_inst_id='.$item['id'].'&p_tab='))
                    <td class="actions">
                        <a href="{{$link}}" class="btn-square clr-mid"><i class="icon-link-rel"></i><span class="hide-txt">Relacionar</span> </a>
                    </td>
                    @php($id_rel++)
                @else
                    <td class="actions">
                        <a href="{{ route('editora.action', 'add_favorite/?p_class_id='.$item['class_id'].'&p_inst_id='.$item['id']) }}" class="btn-square clr-default @if(isset($favorites)){{_isFavorited($item['id'], $favorites)}}@endif" id="table-fav-{{$item['id']}}"><i class="icon-star-outline"></i></a>
                        @if(isset($item['edit']) && $item['edit']=='Y')
                        <a href="{{ route('editora.action', 'edit_instance/?p_pagina=1&p_class_id='.$item['class_id'].'&p_inst_id='.$item['id']) }}" class="btn-square clr-default"><i class="icon-pencil"></i></a>
                        @endif
                    </td>
                @endif
            </tr>
        @endforeach
    @endif
    </tbody>
</table>
@if($p_mode=='R')
    </form>
@endif

@section('scripts')
    @parent
    <script type="text/javascript">
        $(document).ready(function() {
             oTable = $('#pages-table').DataTable({
                 ordering: false,
                 paging: true,
                 searching:true,
                 lengthChange:false,
                 sDom:"ltipr"
             });

             $('#search_instances').keyup(function(){
                oTable.search($(this).val()).draw() ;
             })
        } );
    </script>
@endsection