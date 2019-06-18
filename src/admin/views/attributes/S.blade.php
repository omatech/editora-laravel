{{-- String d'una linea --}}
@if($p_mode=='V' && $attribute['id']!=1)
    <div class="column column-text">
        <div class="form-group">
            <label for="{{$attribute_name}}" class="form-label">{{$attribute['caption']}} {!! _attributeInfo($attribute['id'], $attribute['name'], $attribute['type']) !!}</label>
            <input type="text" class="form-control" name="{{$attribute_name}}" value="{{$attribute['atrib_values'][0]['text_val']}}" disabled="disabled">
        </div>
    </div>
@elseif(($p_mode=='U' || $p_mode=='I')&& $attribute['id']!=1)
    <div class="column column-text">
        <div class="form-group">
            <label for="{{$attribute_name}}" class="form-label">{{$attribute['caption']}}</label>
            <input type="text" class="form-control" name="{{$attribute_name}}" value="{{$attribute['atrib_values'][0]['text_val']}}">
        </div>
    </div>
@endif
