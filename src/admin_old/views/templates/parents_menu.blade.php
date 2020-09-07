@if(isset($parents) && !empty($parents) && is_array($parents))
<div id="relations-menu">
    <header class="side-menu-header">
        <span class="tit">{{__('editora_lang::messages.container_objetos_padre')}}</span>
        <button class="btn-square clr-gray" id="btn-hide-relations"><i class="icon-close"></i><span class="sr-only">{{__('editora_lang::messages.info_word_close_relations')}}</span></button>
    </header>
    <div class="side-menu-content">
        <ul class="relations-list">
            @foreach($parents as $parent)
                <li><a href="{{ route('editora.action', 'view_instance/?p_pagina=1&p_class_id='.$parent['class_id'].'&p_inst_id='.$parent['id']) }}" class="rel-wrapper">{!! _instanceStatus($parent['status']) !!} <span class="txt">{{$parent['key_fields']}}</span></a></li>
            @endforeach
        </ul>
    </div>
</div>
@endif

