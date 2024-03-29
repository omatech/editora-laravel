@php
  if (!isset($p_mode)) $p_mode='D';
  if (!isset($page)) $page=1;
  if (!isset($class['id'])) $class['id']=1;
@endphp
@if(isset($p_mode) && $p_mode=='R')
    <form class="form" id="relation_all" name="relation_all" method="post" enctype="multipart/form-data" action="{{route('editora.action', 'join_all')}}">
        {{ csrf_field() }}
        <button class="btn clr-secondary" style="font-size:14px">{{getMessage('info_word_join')}}</button>
        <a class="btn clr-primary" style="font-size:14px; color: white;" onclick="selectAll()">{{getMessage('info_word_select_all')}}</a>
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
                           <input type="checkbox" name="rel_chb[]" id="table-add-{{$item['id']}}" value="{{$item['id']}}">
                           <label for="table-add-{{$item['id']}}">{{$item['id']}}</label>
                        </span>
                    @else
                        <a href="{!! $link !!}">{{$item['id']}}</a>
                    @endif
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
                    <button class="btn-square {!! _activeDate($item['publishing_begins'], $item['publishing_ends']) !!}" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="
                    @if($item['publishing_ends']!=null)
                        <p>{{ getMessage('published_from') }} <time>{{$item['publishing_begins']}}</time> {{ getMessage('published_to') }} <time>{{$item['publishing_ends']}}</time></p>
                    @else
                        <p>{{ getMessage('published_on') }} <time>{{$item['publishing_begins']}}</time></p>
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
@isset($paginator)
    {{ $paginator->links() }}
@endisset
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
                 sDom: "ltipr",
                 @if(isset($p_mode) && $p_mode=='R')
                 columns: [
                     null,
                     {contentPadding: "iiiiiiiiiiiiiiii", width: "0px"},
                     null,
                     null,
                     null,
                     null,
                     null,
                     null,
                 ],
                 @endif
             });

             $('#search_instances').keyup(function(){
                oTable.search($(this).val()).draw() ;
             })
        } );

        function selectAll() {
            $('input[name="rel_chb[]"]').prop('checked', true);
        }
    </script>
@endsection
