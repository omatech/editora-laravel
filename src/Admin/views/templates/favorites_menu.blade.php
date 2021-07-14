<div id="favorites-menu">
    <header class="side-menu-header">
        <span class="tit">Mis favoritos</span>
        <button class="btn-square clr-gray" id="btn-hide-favorites"><i class="icon-close"></i></button>
    </header>
    <div class="side-menu-content" id="favorites-menu-list">
        <ul class="favorite-list">
            @if(isset($favorites))
                @foreach($favorites as $item)
                    <li class="favorite-list-item">
                        <p class="favorite-tit"><a href="{{route('editora.action', 'view_instance?p_pagina=1&p_class_id='.$item['class_id'].'&p_inst_id='.$item['id'])}}">{{$item['key_fields']}}</a></p>
                        <span class="btn-favorite">
                            <label for="fav-btn-{{$item['id']}}" class="btn-square clr-default"><a onclick="favdelete('{{$item['class_id']}}', '{{$item['id']}}')"><i class="icon-star-off"></i></a></label>
                        </span>
                    </li>
                @endforeach
            @endif
        </ul>
    </div>
</div>
