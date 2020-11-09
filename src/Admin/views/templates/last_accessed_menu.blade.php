<div id="modifications-menu">
    <header class="side-menu-header">
        <span class="tit">{{getMessage('info_word_last_modifications')}}</span>
        <button class="btn-square clr-gray" id="btn-hide-modifications"><i class="icon-close"></i><span class="sr-only">{{getMessage('info_word_close_last_modifications')}}</span></button>
    </header>
    <div class="side-menu-content">
        <ul class="last-modifications-list">
            @if(isset($last_accessed))
                @foreach($last_accessed as $item)
                    <li class="last-modifications-list-item">
                        <p class="tit"><a href="{{ route('editora.action', 'view_instance/?p_pagina=1&p_class_id='.$item['class_id'].'&p_inst_id='.$item['id']) }}">{{$item['key_fields']}}</a></p>
                        <p class="data">
                            <time class="date">{{$item['fecha']}}</time>
                        </p>
                    </li>
                @endforeach
            @endif
        </ul>
    </div>
</div>
