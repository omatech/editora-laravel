@if(isset($p_mode) && $p_mode=='R')
    @php($id_rel=0)
    <form class="form" id="relation_all" name="relation_all" method="post" enctype="multipart/form-data" action="{{route('editora.action', 'join_all')}}">
        {{ csrf_field() }}
        <button class="btn clr-secondary" style="font-size:14px">{{getMessage('info_word_join')}}</button>
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
        <th class="status"><span>{{getMessage('info_word_status')}}</span></th>
        <th class="id"><span>{{getMessage('info_word_ID')}}</span></th>
        <th class="picture"><span>{{getMessage('info_word_img')}}</span></th>
        <th class="tit"><span>{{getMessage('info_word_keyword')}}</span></th>
        <th class="type"><span>{{getMessage('info_word_type')}}</span></th>
        <th class="creation-date"><span>{{getMessage('info_word_update_date')}}</span></th>
        <th class="date-condition"><span class="hidden">{{getMessage('info_word_date_condition')}}</span></th>
        <th class="favorite"><span class="hidden">{{getMessage('acciones')}}</span></th>
    </tr>
    </thead>
    <tbody>
    @if(isset($instances))
         @foreach($instances as $item)
             @php($link=route('editora.action', 'view_instance?p_pagina=1&p_class_id='.$item['class_id'].'&p_inst_id='.$item['id']))
            <tr>
                <td class="status">
                    <button class="btn-square">
                        {!! _instanceStatus($item['status']) !!}
                    </button>
                </td>
                <td class="id">
                    @if(isset($p_mode) && $p_mode=='R')
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
                <td class="type">
                    <a href="{{ route('editora.action', 'list_instances?p_class_id=' . $item['class_id']) }}">
                        {{$item['class_realname']}}
                    </a>
                </td>
                <td class="creation-date"><time class="date">{{_formatDate($item['cd_ordre'])}}</time></td>
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

                @if(isset($p_mode) && $p_mode=='R')
                    @php($link=route('editora.action', 'join2?p_pagina=1&p_relation_id='.$parent['rel_id'].'&p_parent_class_id='.$parent['class_id'].'&p_parent_inst_id='.$parent['inst_id'].'&p_child_inst_id='.$item['id'].'&p_tab='))
                    <td class="actions">
                        <a href="{{$link}}" class="btn-square clr-mid"><i class="icon-link-rel"></i><span class="hide-txt">{{getMessage('info_word_join')}}</span> </a>
                    </td>
                    @php($id_rel++)
                @else
                    <td class="actions">
                        <a href="{{ route('editora.action', 'add_favorite?p_class_id='.$item['class_id'].'&p_inst_id='.$item['id']) }}" class="btn-square clr-default @if(isset($favorites)){{_isFavorited($item['id'], $favorites)}}@endif" id="table-fav-{{$item['id']}}"><i class="icon-star-outline"></i></a>
                        @if(isset($item['edit']) && $item['edit']=='Y')
                        <a href="{{ route('editora.action', 'edit_instance?p_pagina=1&p_class_id='.$item['class_id'].'&p_inst_id='.$item['id']) }}" class="btn-square clr-default"><i class="icon-pencil"></i></a>
                        @endif
                    </td>
                @endif
            </tr>
        @endforeach
    @endif
    </tbody>
</table>
@if(isset($count) && $count!=-1)
<h3>{{$count}} {{getMessage('info_objects_found')}}</h3>
@endif
@if(isset($count) && $count>40)
@php($pages = ceil($count/40))
<div class="dataTables_paginate paging_simple_numbers" id="pages-table_paginate">
    <ul class="pagination">
        @if($page>1)
        <li class="paginate_button page-item previous"><a href="{{ route('editora.action', 'list_instances?p_pagina='.($page-1).'&p_class_id='.$class['id']) }}"  class="page-link">{{getMessage('paginacion_anteriores')}}</a></li>
        @endif
        @for($i=1; $i<=$pages; $i++)
        <li class="paginate_button page-item @if($i==$page) active @endif"><a href="{{ route('editora.action', 'list_instances?p_pagina='.$i.'&p_class_id='.$class['id']) }}" class="page-link">{{$i}}</a></li>
        @endfor
        @if($page<$pages)
        <li class="paginate_button page-item next"><a href="{{ route('editora.action', 'list_instances?p_pagina='.($page+1).'&p_class_id='.$class['id']) }}" class="page-link">{{getMessage('paginacion_siguientes')}}</a></li>
        @endif
    </ul>
</div>
@endif
@if(isset($p_mode) && $p_mode=='R')
    </form>
@endif

@section('scripts')
    @parent
    <script type="text/javascript">
        $(document).ready(function() {
             oTable = $('#pages-table').DataTable({
                 ordering: false,
                 paging: false,
                 searching:false,
                 lengthChange:false,
                 info: false,
                 sDom:"ltipr"
             });

             $('#search_instances').keyup(function(){
                oTable.search($(this).val()).draw() ;
             })
        } );
    </script>
@endsection
