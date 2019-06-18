<thead>
<tr>
    <th class="sort"><span class="hidden">Ordenar</span></th>
    <th class="status"><span>Estado</span></th>
    <th class="id"><span>ID</span></th>
    <th class="tit"><span>Instancia</span></th>
    <th class="type"><span>Tipo</span></th>
    <th class="actions"><span class="hidden">Acciones</span></th>
</tr>
</thead>
<tbody id="tabrel{{$attribute['id']}}">
@foreach($attribute['instances'] as $item)
    <tr class="published" id="{{$item['inst_id']}}">
        <td class="sort"><span class="drag-area"><i class="icon-drag"></i></span></td>
        <td class="status">
            <button class="btn-square clr-transparent">
                @if($item['status']=="O")
                    <span class="status-ball clr-published"></span>
                @elseif($item['status']=="V")
                    <span class="status-ball clr-pending"></span>
                @else
                    <span class="status-ball clr-unpublished"></span>
                @endif
            </button>
        </td>
        <td class="id"><a href="{{ route('editora.action', 'view_instance/?p_class_id='.$item['child_class_id'].'&p_inst_id='.$item['inst_id']) }}">{{$item['inst_id']}}</a></td>
        <td class="tit"><a href="{{ route('editora.action', 'view_instance/?p_class_id='.$item['child_class_id'].'&p_inst_id='.$item['inst_id']) }}">{{$item['key_fields']}}</a></td>
        <td class="type">{{$item['class_realname']}}</td>
        <td class="actions">
            <ul>
                <li><a onclick="reldelete('{{route('editora.action', 'delete_relation_instance/?p_relation_id='.$item['id'].'&p_class_id='.$item['parent_class_id'].'&parent_inst_id='.$instance['id'].'&p_inst_id='.$item['inst_id'].'&p_tab=1&p_rel_id='.$attribute['id'])}}')" class="btn-square clr-default"><i class="icon-unlink-rel"></i><span class="hide-txt">Desvincular</span></a></li>
                <li><a href="{{ route('editora.action', 'edit_instance/?p_class_id='.$item['child_class_id'].'&p_inst_id='.$item['inst_id']) }}" class="btn-square clr-default"><i class="icon-pencil"></i><span class="hide-txt">Editar</span></a></li>
            </ul>
        </td>
    </tr>
@endforeach
</tbody>